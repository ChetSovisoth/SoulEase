<?php

namespace App\Http\Controllers;

use App\Events\StartVideoChat;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VideoCallController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->get();

        return Inertia::render('VideoCall', [
            'users' => $users,
            'authUser' => auth()->user()
        ]);
    }

    public function callUser(Request $request)
    {
        $data = [
            'userToCall' => $request->user_to_call,
            'from' => auth()->id(),
            'signalData' => $request->signal_data
        ];

        broadcast(new StartVideoChat($data))->toOthers();

        return response()->json(['success' => true]);
    }
}
