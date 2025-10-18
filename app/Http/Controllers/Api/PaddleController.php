<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaddleService;
use App\Services\PricingService;
use App\Services\PaddlePriceMapper;
use App\Models\User;
use App\Models\TherapySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaddleController extends Controller
{
    private PaddleService $paddleService;
    private PricingService $pricingService;
    private PaddlePriceMapper $priceMapper;

    public function __construct(
        PaddleService $paddleService,
        PricingService $pricingService,
        PaddlePriceMapper $priceMapper
    ) {
        $this->paddleService = $paddleService;
        $this->pricingService = $pricingService;
        $this->priceMapper = $priceMapper;
    }

    /**
     * Create a custom Paddle price for a therapy session
     * OR return a pre-configured price ID
     */
    public function createPrice(Request $request)
    {
        try {
            $validated = $request->validate([
                'productName' => 'required|string',
                'price' => 'required|numeric|min:0',
                'quantity' => 'integer|min:1',
                'userId' => 'required|exists:users,id',
                'duration_minutes' => 'integer|min:15|max:240',
            ]);

            $durationMinutes = $validated['duration_minutes'] ?? 60;

            // Try to use pre-configured price ID first (simpler and more reliable)
            if ($this->priceMapper->isPriceConfigured($durationMinutes)) {
                $priceId = $this->priceMapper->getPriceIdForDuration($durationMinutes);

                Log::info('Using pre-configured Paddle price', [
                    'duration' => $durationMinutes,
                    'price_id' => $priceId,
                ]);

                return response()->json([
                    'price_id' => $priceId,
                    'amount' => $validated['price'],
                    'method' => 'pre-configured',
                ]);
            }

            // Fallback: Try to create dynamic price (requires working API key)
            try {
                $priceId = $this->paddleService->createCustomPrice(
                    $validated['productName'],
                    $validated['price']
                );

                if ($priceId) {
                    return response()->json([
                        'price_id' => $priceId,
                        'amount' => $validated['price'],
                        'method' => 'dynamic',
                    ]);
                }
            } catch (\Exception $apiError) {
                Log::warning('Dynamic price creation failed, falling back to error', [
                    'error' => $apiError->getMessage(),
                ]);
            }

            // If both methods fail, return helpful error
            return response()->json([
                'error' => 'Paddle price not configured',
                'message' => 'Please configure PADDLE_PRICE_' . $durationMinutes . 'MIN in your .env file, or fix your Paddle API credentials.',
                'duration' => $durationMinutes,
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error in createPrice', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to create price in Paddle',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate session price based on duration
     * Note: All sessions are priced at $100/hour standard rate
     */
    public function calculatePrice(Request $request)
    {
        try {
            $validated = $request->validate([
                'therapist_id' => 'required|exists:users,id',
                'duration_minutes' => 'required|integer|min:15|max:240',
            ]);

            $therapist = User::findOrFail($validated['therapist_id']);

            if (!$therapist->isTherapist()) {
                return response()->json([
                    'error' => 'Invalid therapist'
                ], 400);
            }

            $breakdown = $this->pricingService->getPriceBreakdown(
                $therapist,
                $validated['duration_minutes']
            );

            return response()->json($breakdown);
        } catch (\Exception $e) {
            Log::error('Error in calculatePrice', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'An error occurred while calculating the price'
            ], 500);
        }
    }

    /**
     * Get available session durations and pricing
     * Note: All sessions are priced at $100/hour standard rate
     */
    public function getDurationOptions(Request $request)
    {
        try {
            $validated = $request->validate([
                'therapist_id' => 'required|exists:users,id',
            ]);

            $therapist = User::findOrFail($validated['therapist_id']);

            if (!$therapist->isTherapist()) {
                return response()->json([
                    'error' => 'Invalid therapist'
                ], 400);
            }

            $durations = $this->pricingService->getAvailableDurations();
            $options = [];

            foreach ($durations as $duration) {
                $price = $this->pricingService->calculateSessionPrice(
                    $therapist,
                    $duration['value']
                );

                $options[] = [
                    'value' => $duration['value'],
                    'label' => $duration['label'],
                    'price' => $price,
                ];
            }

            return response()->json([
                'durations' => $options,
                'therapist' => [
                    'id' => $therapist->id,
                    'name' => $therapist->name,
                    'hourly_rate' => 100, // Standardized rate
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getDurationOptions', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'An error occurred while fetching duration options'
            ], 500);
        }
    }

    /**
     * Handle Paddle webhook events
     */
    public function webhook(Request $request)
    {
        try {
            $eventType = $request->input('event_type');
            $data = $request->input('data');

            Log::info('Paddle webhook received', [
                'event_type' => $eventType,
                'data' => $data,
            ]);

            // Handle different event types
            switch ($eventType) {
                case 'transaction.completed':
                    $this->handleTransactionCompleted($data);
                    break;

                case 'transaction.payment_failed':
                    $this->handlePaymentFailed($data);
                    break;

                default:
                    Log::info('Unhandled Paddle event type', ['type' => $eventType]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error handling Paddle webhook', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Handle completed transaction
     */
    private function handleTransactionCompleted(array $data)
    {
        $customData = $data['custom_data'] ?? [];
        $userId = $customData['user_id'] ?? null;
        $sessionId = $customData['session_id'] ?? null;

        if ($sessionId) {
            $session = TherapySession::find($sessionId);
            if ($session && $session->payment) {
                $session->payment->update([
                    'status' => 'completed',
                    'transaction_id' => $data['id'] ?? null,
                    'payment_method' => 'paddle',
                    'payment_details' => $data,
                ]);

                Log::info('Payment completed for session', ['session_id' => $sessionId]);
            }
        }
    }

    /**
     * Handle failed payment
     */
    private function handlePaymentFailed(array $data)
    {
        $customData = $data['custom_data'] ?? [];
        $sessionId = $customData['session_id'] ?? null;

        if ($sessionId) {
            $session = TherapySession::find($sessionId);
            if ($session && $session->payment) {
                $session->payment->update([
                    'status' => 'failed',
                    'payment_details' => $data,
                ]);

                Log::warning('Payment failed for session', ['session_id' => $sessionId]);
            }
        }
    }
}
