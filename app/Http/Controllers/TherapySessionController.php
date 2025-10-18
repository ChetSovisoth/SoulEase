<?php

namespace App\Http\Controllers;

use App\Models\TherapySession;
use App\Models\AvailabilitySlot;
use App\Models\Payment;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class TherapySessionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $baseQuery = function() use ($user) {
            if ($user->isPatient()) {
                return TherapySession::where('patient_id', $user->id);
            } elseif ($user->isTherapist()) {
                return TherapySession::where('therapist_id', $user->id);
            } else {
                return TherapySession::query();
            }
        };

        // Upcoming sessions (pending and confirmed, scheduled in the future)
        $upcomingSessions = $baseQuery()
            ->with(['patient', 'therapist', 'therapist.therapistProfile', 'payment'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at', 'asc')
            ->get()
            ->map(function($session) {
                $session->total_amount = $session->payment ? $session->payment->amount : 0;
                $session->duration = $session->duration_minutes ?? 60;
                return $session;
            });

        // Past sessions (completed or past scheduled date)
        $pastSessions = $baseQuery()
            ->with(['patient', 'therapist', 'therapist.therapistProfile', 'payment'])
            ->where(function($query) {
                $query->where('status', 'completed')
                      ->orWhere(function($q) {
                          $q->where('scheduled_at', '<', now())
                            ->whereIn('status', ['confirmed']);
                      });
            })
            ->orderBy('scheduled_at', 'desc')
            ->get()
            ->map(function($session) {
                $session->total_amount = $session->payment ? $session->payment->amount : 0;
                $session->duration = $session->duration_minutes ?? 60;
                return $session;
            });

        // Cancelled sessions
        $cancelledSessions = $baseQuery()
            ->with(['patient', 'therapist', 'therapist.therapistProfile', 'payment'])
            ->where('status', 'cancelled')
            ->orderBy('scheduled_at', 'desc')
            ->get()
            ->map(function($session) {
                $session->total_amount = $session->payment ? $session->payment->amount : 0;
                $session->duration = $session->duration_minutes ?? 60;
                return $session;
            });

        return Inertia::render('Sessions/Index', [
            'upcomingSessions' => $upcomingSessions,
            'pastSessions' => $pastSessions,
            'cancelledSessions' => $cancelledSessions,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->isPatient()) {
            abort(403);
        }

        $validated = $request->validate([
            'therapist_id' => 'required|exists:users,id',
            'availability_slot_id' => 'required|exists:availability_slots,id',
            'duration_minutes' => 'integer|min:15|max:240',
        ]);

        $slot = AvailabilitySlot::findOrFail($validated['availability_slot_id']);

        if ($slot->is_booked) {
            return back()->withErrors(['slot' => 'This time slot is already booked.']);
        }

        // Mark slot as booked
        $slot->update(['is_booked' => true]);

        // Determine session duration
        $durationMinutes = $validated['duration_minutes'] ?? 60;

        // Create session
        $session = TherapySession::create([
            'patient_id' => $user->id,
            'therapist_id' => $validated['therapist_id'],
            'availability_slot_id' => $slot->id,
            'scheduled_at' => $slot->start_time,
            'duration_minutes' => $durationMinutes,
            'status' => 'pending',
            'video_room_id' => 'room-' . Str::uuid(),
        ]);

        // Calculate dynamic price based on duration
        $pricingService = app(PricingService::class);
        $therapist = $session->therapist;
        $sessionPrice = $pricingService->calculateSessionPrice($therapist, $durationMinutes);

        // Create payment
        Payment::create([
            'therapy_session_id' => $session->id,
            'patient_id' => $user->id,
            'therapist_id' => $validated['therapist_id'],
            'amount' => $sessionPrice,
            'currency' => 'USD',
            'status' => 'pending',
        ]);

        return redirect()->route('sessions.show', $session)
            ->with('success', 'Session booked successfully!');
    }

    public function show(TherapySession $session)
    {
        $user = auth()->user();

        if ($session->patient_id !== $user->id && $session->therapist_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $session->load(['patient', 'therapist', 'therapist.therapistProfile', 'payment', 'messages']);
        $session->total_amount = $session->payment ? $session->payment->amount : 0;
        $session->duration = $session->duration_minutes ?? 60;

        return Inertia::render('Sessions/Show', [
            'session' => $session,
            'paddleToken' => config('services.paddle.client_token'),
            'paddleVendorId' => (int)config('services.paddle.vendor_id'),
        ]);
    }

    public function updateStatus(Request $request, TherapySession $session)
    {
        $user = $request->user();

        if ($session->therapist_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $session->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Session status updated successfully');
    }

    public function confirm(Request $request, TherapySession $session)
    {
        $user = $request->user();

        if ($session->therapist_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        if ($session->status !== 'pending') {
            return back()->withErrors(['session' => 'Only pending sessions can be confirmed.']);
        }

        if ($session->payment && $session->payment->status !== 'completed') {
            return back()->withErrors(['session' => 'Payment must be completed before confirming the session.']);
        }

        $session->update(['status' => 'confirmed']);

        return redirect()->back()->with('success', 'Session confirmed successfully');
    }

    public function complete(Request $request, TherapySession $session)
    {
        $user = $request->user();

        if ($session->therapist_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        if ($session->status !== 'confirmed') {
            return back()->withErrors(['session' => 'Only confirmed sessions can be completed.']);
        }

        $session->update(['status' => 'completed']);

        return redirect()->back()->with('success', 'Session marked as completed');
    }

    public function cancel(Request $request, TherapySession $session)
    {
        $user = $request->user();

        if ($session->patient_id !== $user->id && $session->therapist_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        if ($session->status === 'completed') {
            return back()->withErrors(['session' => 'Completed sessions cannot be cancelled.']);
        }

        $session->update(['status' => 'cancelled']);

        // Refund payment if it was completed
        if ($session->payment && $session->payment->status === 'completed') {
            $session->payment->update(['status' => 'refunded']);
        }

        return redirect()->back()->with('success', 'Session cancelled successfully');
    }

    public function saveNotes(Request $request, TherapySession $session)
    {
        $user = $request->user();

        if ($session->therapist_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:5000',
        ]);

        $session->update(['notes' => $validated['notes']]);

        return redirect()->back()->with('success', 'Notes saved successfully');
    }

    public function joinVideo(TherapySession $session)
    {
        $user = auth()->user();

        if ($session->patient_id !== $user->id && $session->therapist_id !== $user->id) {
            abort(403);
        }

        if ($session->status !== 'confirmed') {
            return back()->withErrors(['session' => 'This session is not confirmed yet.']);
        }

        return Inertia::render('Sessions/Video', [
            'session' => $session->load(['patient', 'therapist']),
            'jitsiDomain' => config('services.jitsi.domain', 'meet.jit.si'),
        ]);
    }
}
