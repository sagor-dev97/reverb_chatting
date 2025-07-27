<?php

namespace App\Events;

use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $message;

    public function __construct(User $user, ChatMessage $message)
    {
        $this->user = $user;
        $this->message = $message;

        Log::info('Broadcasting message event', [
            'user' => $user->toArray(),
            'message' => $message->toArray(),
        ]);
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }

    public function broadcastOn()
    {
        // Use public channel for all chat messages
        return new Channel('public-chat');
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message->message,
            'sender' => [
                'id' => $this->user->id,
                'name' => $this->user->name
            ],
            'receiver_id' => $this->message->receiver_id,
            'created_at' => $this->message->created_at->toDateTimeString(),
            'is_you' => false // Frontend can set this to true for current user's messages
        ];
    }
}