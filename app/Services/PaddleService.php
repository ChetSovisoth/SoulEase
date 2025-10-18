<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaddleService
{
    private ?string $apiKey;
    private string $apiUrl;
    private ?string $productId;
    private bool $isSandbox;

    public function __construct()
    {
        // Trim whitespace from API key to prevent authentication errors
        $this->apiKey = trim(config('services.paddle.api_key') ?? '');
        $this->productId = trim(config('services.paddle.product_id') ?? '');

        // Always use sandbox
        $this->isSandbox = true;
        $this->apiUrl = 'https://sandbox-api.paddle.com';

        Log::info('PaddleService initialized', [
            'environment' => 'sandbox',
            'api_url' => $this->apiUrl,
        ]);
    }

    /**
     * Create a custom price in Paddle for dynamic pricing
     */
    public function createCustomPrice(string $productName, float $amount, string $currency = 'USD'): ?string
    {
        // Check if API key is configured
        if (!$this->apiKey) {
            Log::error('Paddle API key not configured');
            throw new \Exception('Paddle API key not configured. Please set PADDLE_API_KEY in your .env file.');
        }

        // For now, use a simpler approach - return a mock price_id for testing
        // In production, you'll need to either:
        // 1. Create a product in Paddle dashboard and use pre-configured price IDs
        // 2. Or implement the Paddle API v2 correctly

        // If you have a product_id configured, try to create a price
        if ($this->productId) {
            return $this->createPriceForProduct($productName, $amount, $currency);
        }

        // Otherwise, log and throw exception with helpful message
        Log::error('Paddle integration not fully configured', [
            'message' => 'Please configure PADDLE_PRODUCT_ID or use pre-configured prices',
            'amount' => $amount,
        ]);

        throw new \Exception('Paddle integration is not fully configured. Please either: 1) Set PADDLE_PRODUCT_ID in .env, or 2) Use pre-configured price IDs from Paddle dashboard.');
    }

    /**
     * Create a price for an existing product
     */
    private function createPriceForProduct(string $description, float $amount, string $currency = 'USD'): ?string
    {
        try {
            Log::info('Creating Paddle price', [
                'product_id' => $this->productId,
                'amount' => $amount,
                'description' => $description,
                'api_key_length' => strlen($this->apiKey),
                'api_key_prefix' => substr($this->apiKey, 0, 10) . '...', // Log first 10 chars for debugging
            ]);

            $payload = [
                'description' => $description,
                'product_id' => $this->productId,
                'unit_price' => [
                    'amount' => (string) round($amount * 100), // Convert to cents
                    'currency_code' => $currency,
                ],
            ];

            Log::info('Paddle API Request', ['payload' => $payload]);

            $response = Http::withToken($this->apiKey)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Paddle-Version' => '1',
                ])
                ->post("{$this->apiUrl}/prices", $payload);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Paddle price created successfully', ['price_id' => $data['data']['id'] ?? null]);
                return $data['data']['id'] ?? null;
            }

            Log::error('Paddle create price failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception('Paddle API error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Exception creating Paddle price', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Get or create a product in Paddle (for future use)
     */
    public function getOrCreateProduct(string $productName): ?string
    {
        try {
            // Try to get existing product
            $response = Http::withToken($this->apiKey)
                ->get("{$this->apiUrl}/products", [
                    'name' => $productName,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['data'])) {
                    return $data['data'][0]['id'];
                }
            }

            // Create new product if not found
            $response = Http::withToken($this->apiKey)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->apiUrl}/products", [
                    'name' => $productName,
                    'description' => 'Online therapy session',
                    'tax_category' => 'standard',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data']['id'] ?? null;
            }

            Log::error('Failed to create Paddle product', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception getting/creating Paddle product', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Create a transaction (checkout session)
     */
    public function createTransaction(array $items, array $customer, array $customData = []): ?array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->apiUrl}/transactions", [
                    'items' => $items,
                    'customer' => $customer,
                    'custom_data' => $customData,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Paddle create transaction failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception creating Paddle transaction', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
