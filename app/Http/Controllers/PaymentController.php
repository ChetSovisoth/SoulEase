<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\TherapySession;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function show(Payment $payment)
    {
        $user = auth()->user();

        if ($payment->patient_id !== $user->id && $payment->therapist_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $payment->load(['therapySession', 'patient', 'therapist']);

        return Inertia::render('Payments/Show', [
            'payment' => $payment,
        ]);
    }

    public function process(Request $request, Payment $payment)
    {
        $user = $request->user();

        if ($payment->patient_id !== $user->id) {
            abort(403);
        }

        if ($payment->status !== 'pending') {
            return back()->withErrors(['payment' => 'This payment has already been processed.']);
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:credit_card,paypal,stripe',
            'card_number' => 'required_if:payment_method,credit_card',
            'card_exp' => 'required_if:payment_method,credit_card',
            'card_cvv' => 'required_if:payment_method,credit_card',
        ]);

        // Mock payment processing
        $transactionId = 'txn_' . Str::uuid();

        $payment->update([
            'status' => 'completed',
            'payment_method' => $validated['payment_method'],
            'transaction_id' => $transactionId,
            'payment_details' => [
                'card_last4' => isset($validated['card_number']) ? substr($validated['card_number'], -4) : null,
                'processed_at' => now(),
            ],
        ]);

        // Update session status to confirmed
        $payment->therapySession->update(['status' => 'confirmed']);

        return redirect()->route('payments.complete', ['payment_id' => $payment->id])
            ->with('success', 'Payment processed successfully!');
    }

    public function complete(Request $request)
    {
        $user = auth()->user();

        // Get payment ID from query parameter
        $paymentId = $request->query('payment_id');

        if (!$paymentId) {
            return redirect()->route('dashboard')
                ->withErrors(['payment' => 'Invalid payment request.']);
        }

        $payment = Payment::findOrFail($paymentId);

        if ($payment->patient_id !== $user->id) {
            abort(403);
        }

        if ($payment->status !== 'completed') {
            return redirect()->route('sessions.show', $payment->therapy_session_id)
                ->withErrors(['payment' => 'Payment has not been completed yet.']);
        }

        $payment->load([
            'therapySession.therapist.therapistProfile',
            'patient',
        ]);

        return Inertia::render('Payments/Complete', [
            'payment' => $payment,
        ]);
    }

    public function history(Request $request)
    {
        $user = $request->user();

        if ($user->isPatient()) {
            $payments = Payment::where('patient_id', $user->id);
        } elseif ($user->isTherapist()) {
            $payments = Payment::where('therapist_id', $user->id);
        } else {
            $payments = Payment::query();
        }

        $payments = $payments->with(['therapySession', 'patient', 'therapist'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Payments/History', [
            'payments' => $payments,
        ]);
    }
}
