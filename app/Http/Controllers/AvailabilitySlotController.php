<?php

namespace App\Http\Controllers;

use App\Models\AvailabilitySlot;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AvailabilitySlotController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user->isTherapist()) {
            abort(403);
        }

        $slots = AvailabilitySlot::where('therapist_id', $user->id)
            ->whereBetween('start_time', [now(), now()->addDays(30)])
            ->orderBy('start_time')
            ->get();

        return response()->json($slots);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->isTherapist()) {
            abort(403);
        }

        $validated = $request->validate([
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
        ]);

        $slot = AvailabilitySlot::create([
            'therapist_id' => $user->id,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'is_booked' => false,
        ]);

        return response()->json($slot, 201);
    }

    public function bulkCreate(Request $request)
    {
        $user = $request->user();

        if (!$user->isTherapist()) {
            abort(403);
        }

        $validated = $request->validate([
            'dates' => 'required|array',
            'dates.*' => 'required|date',
            'time_slots' => 'required|array',
            'time_slots.*.start' => 'required|date_format:H:i',
            'time_slots.*.end' => 'required|date_format:H:i',
        ]);

        $createdSlots = [];

        foreach ($validated['dates'] as $date) {
            foreach ($validated['time_slots'] as $timeSlot) {
                $startTime = Carbon::parse($date)->setTimeFromTimeString($timeSlot['start']);
                $endTime = Carbon::parse($date)->setTimeFromTimeString($timeSlot['end']);

                if ($startTime->isPast()) {
                    continue;
                }

                $createdSlots[] = AvailabilitySlot::create([
                    'therapist_id' => $user->id,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'is_booked' => false,
                ]);
            }
        }

        return response()->json($createdSlots, 201);
    }

    public function destroy(AvailabilitySlot $slot)
    {
        if ($slot->therapist_id !== auth()->id() || $slot->is_booked) {
            abort(403);
        }

        $slot->delete();

        return response()->json(['message' => 'Slot deleted successfully']);
    }
}
