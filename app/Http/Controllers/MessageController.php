<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Events\MessageSent;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $selectedUserId = $request->query('user');

        // Get all conversations with message counts and last message
        $conversationUsers = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender', 'receiver'])
            ->latest()
            ->get()
            ->map(function ($message) use ($user) {
                return $message->sender_id === $user->id ? $message->receiver : $message->sender;
            })
            ->unique('id')
            ->values();

        // Build conversations with additional data
        $conversations = $conversationUsers->map(function ($conversationUser) use ($user) {
            $lastMessage = Message::between($user->id, $conversationUser->id)
                ->latest()
                ->first();

            $unreadCount = Message::where('sender_id', $conversationUser->id)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();

            return [
                'user' => $conversationUser,
                'last_message' => $lastMessage,
                'unread_count' => $unreadCount,
            ];
        });

        // Get messages for selected user if provided
        $initialMessages = [];
        if ($selectedUserId) {
            $selectedUser = User::find($selectedUserId);
            if ($selectedUser) {
                // Add selected user to conversations if not already there
                $existsInConversations = $conversations->contains(function ($conv) use ($selectedUserId) {
                    return $conv['user']->id == $selectedUserId;
                });

                if (!$existsInConversations) {
                    $conversations->prepend([
                        'user' => $selectedUser,
                        'last_message' => null,
                        'unread_count' => 0,
                    ]);
                }

                $initialMessages = Message::between($user->id, $selectedUser->id)
                    ->with(['sender', 'receiver'])
                    ->orderBy('created_at', 'asc')
                    ->get();

                // Mark messages as read
                Message::where('sender_id', $selectedUser->id)
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                    ]);
            }
        }

        return Inertia::render('Messages/Index', [
            'conversations' => $conversations,
            'initialMessages' => $initialMessages,
            'selectedUserId' => $selectedUserId ? (int)$selectedUserId : null,
        ]);
    }

    public function conversation(Request $request, User $user)
    {
        $currentUser = $request->user();

        $messages = Message::between($currentUser->id, $user->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $currentUser->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return Inertia::render('Messages/Conversation', [
            'messages' => $messages,
            'recipient' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string|max:1000',
            'therapy_session_id' => 'nullable|exists:therapy_sessions,id',
        ]);

        $message = Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $validated['receiver_id'],
            'content' => $validated['content'],
            'therapy_session_id' => $validated['therapy_session_id'] ?? null,
            'is_read' => false,
        ]);

        $message->load(['sender', 'receiver']);

        // Broadcast message event
        broadcast(new MessageSent($message))->toOthers();

        return back();
    }

    public function unreadCount(Request $request)
    {
        $count = Message::where('receiver_id', $request->user()->id)
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }
}
