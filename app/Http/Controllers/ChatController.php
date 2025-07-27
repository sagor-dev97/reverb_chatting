<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Events\MessageSent;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Show chat page with user list
    public function index()
    {
        // Exclude logged-in user from user list
        $users = User::where('id', '!=', auth()->id())->get();
        return view('chat', compact('users'));
    }

    // Fetch chat messages between logged-in user and selected user
   // In ChatController fetchMessages method
public function fetchMessages(User $user, Request $request)
{
    $query = ChatMessage::with('sender')
        ->where(function ($q) use ($user) {
            $q->where('sender_id', auth()->id())
                ->where('receiver_id', $user->id);
        })
        ->orWhere(function ($q) use ($user) {
            $q->where('sender_id', $user->id)
                ->where('receiver_id', auth()->id());
        })
        ->orderByDesc('created_at');

    if ($request->has('before')) {
        $beforeId = $request->query('before');
        $query->where('id', '<', $beforeId);
    }

    $messages = $query->take(20)->get()->reverse()->values();

    return response()->json($messages);
}


    // seen 
    public function markMessagesAsRead($senderId)
    {
        ChatMessage::where('sender_id', $senderId)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['status' => 'success']);
    }


    // Send a new message to the selected user
    public function sendMessage(Request $request, User $user)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = ChatMessage::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $user->id,
            'message' => $request->message,
        ]);


        broadcast(new MessageSent(auth()->user(), $message))->toOthers();

        return response()->json($message);
    }
}
