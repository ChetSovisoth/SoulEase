<?php

namespace App\Http\Controllers;

use App\Models\TherapySession;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isPatient()) {
            return $this->patientDashboard($user);
        } elseif ($user->isTherapist()) {
            return $this->therapistDashboard($user);
        } elseif ($user->isAdmin()) {
            return $this->adminDashboard($user);
        }

        abort(403, 'Unauthorized access');
    }

    protected function patientDashboard($user)
    {
        $upcomingSessions = TherapySession::where('patient_id', $user->id)
            ->upcoming()
            ->with(['therapist', 'therapist.therapistProfile'])
            ->orderBy('scheduled_at')
            ->take(5)
            ->get();

        $pastSessions = TherapySession::where('patient_id', $user->id)
            ->past()
            ->with(['therapist'])
            ->orderBy('scheduled_at', 'desc')
            ->take(5)
            ->get();

        $unreadMessages = Message::where('receiver_id', $user->id)
            ->unread()
            ->with('sender')
            ->latest()
            ->take(10)
            ->get();

        return Inertia::render('Dashboard/Patient', [
            'upcomingSessions' => $upcomingSessions,
            'pastSessions' => $pastSessions,
            'unreadMessages' => $unreadMessages,
        ]);
    }

    protected function therapistDashboard($user)
    {
        $upcomingSessions = TherapySession::where('therapist_id', $user->id)
            ->upcoming()
            ->with(['patient'])
            ->orderBy('scheduled_at')
            ->take(10)
            ->get();

        $todaySessions = TherapySession::where('therapist_id', $user->id)
            ->whereDate('scheduled_at', today())
            ->with(['patient'])
            ->orderBy('scheduled_at')
            ->get();

        $unreadMessages = Message::where('receiver_id', $user->id)
            ->unread()
            ->with('sender')
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'total_sessions' => TherapySession::where('therapist_id', $user->id)->count(),
            'completed_sessions' => TherapySession::where('therapist_id', $user->id)
                ->where('status', 'completed')->count(),
            'pending_sessions' => TherapySession::where('therapist_id', $user->id)
                ->where('status', 'pending')->count(),
            'today_sessions' => $todaySessions->count(),
        ];

        return Inertia::render('Dashboard/Therapist', [
            'upcomingSessions' => $upcomingSessions,
            'todaySessions' => $todaySessions,
            'unreadMessages' => $unreadMessages,
            'stats' => $stats,
        ]);
    }

    protected function adminDashboard($user)
    {
        $stats = [
            'total_users' => User::count(),
            'total_patients' => User::where('role', 'patient')->count(),
            'total_therapists' => User::where('role', 'therapist')->count(),
            'total_sessions' => TherapySession::count(),
            'completed_sessions' => TherapySession::where('status', 'completed')->count(),
            'pending_sessions' => TherapySession::where('status', 'pending')->count(),
        ];

        $recentSessions = TherapySession::with(['patient', 'therapist'])
            ->latest()
            ->take(10)
            ->get();

        $recentUsers = User::latest()
            ->take(10)
            ->get();

        return Inertia::render('Dashboard/Admin', [
            'stats' => $stats,
            'recentSessions' => $recentSessions,
            'recentUsers' => $recentUsers,
        ]);
    }
}
