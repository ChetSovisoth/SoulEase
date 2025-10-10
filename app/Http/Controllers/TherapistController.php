<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TherapistProfile;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TherapistController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'therapist')
            ->with('therapistProfile')
            ->whereHas('therapistProfile', function ($q) {
                $q->where('is_verified', true)
                  ->where('is_available', true);
            });

        // Filter by specialization
        if ($request->has('specialization')) {
            $query->whereHas('therapistProfile', function ($q) use ($request) {
                $q->where('specialization', 'like', '%' . $request->specialization . '%');
            });
        }

        $therapists = $query->paginate(12);

        return Inertia::render('Therapists/Index', [
            'therapists' => $therapists,
            'filters' => $request->only(['specialization']),
        ]);
    }

    public function show(User $therapist)
    {
        if ($therapist->role !== 'therapist') {
            abort(404);
        }

        $therapist->load('therapistProfile');

        // Get available slots for next 7 days
        $availableSlots = $therapist->availabilitySlots()
            ->available()
            ->whereBetween('start_time', [now(), now()->addDays(7)])
            ->orderBy('start_time')
            ->get()
            ->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'date' => $slot->start_time->format('Y-m-d'),
                    'start_time' => $slot->start_time->format('H:i'),
                    'end_time' => $slot->end_time->format('H:i'),
                    'is_available' => !$slot->is_booked,
                ];
            });

        return Inertia::render('Therapists/Show', [
            'therapist' => $therapist,
            'availabilitySlots' => $availableSlots,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if (!$user->isTherapist()) {
            abort(403);
        }

        $validated = $request->validate([
            'specialization' => 'required|string|max:255',
            'qualifications' => 'nullable|string',
            'years_of_experience' => 'required|integer|min:0',
            'hourly_rate' => 'required|numeric|min:0',
            'languages' => 'nullable|array',
            'bio' => 'nullable|string',
        ]);

        $user->update(['bio' => $validated['bio'] ?? $user->bio]);

        $user->therapistProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'specialization' => $validated['specialization'],
                'qualifications' => $validated['qualifications'],
                'years_of_experience' => $validated['years_of_experience'],
                'hourly_rate' => $validated['hourly_rate'],
                'languages' => $validated['languages'] ?? [],
            ]
        );

        return redirect()->back()->with('success', 'Profile updated successfully');
    }
}
