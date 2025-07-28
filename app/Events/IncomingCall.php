<?php
namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class IncomingCall implements ShouldBroadcast
{
    use SerializesModels;

    public $fromUserId;
    public $fromUserName;
    public $receiverId;

    public function __construct($fromUserId, $fromUserName, $receiverId)
    {
        $this->fromUserId = $fromUserId;
        $this->fromUserName = $fromUserName;
        $this->receiverId = $receiverId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('audio-call.' . $this->receiverId);
    }

    public function broadcastWith()
    {
        return [
            'fromUserId' => $this->fromUserId,
            'fromUserName' => $this->fromUserName,
        ];
    }

     public function broadcastAs()
    {
        return 'IncomingCall';  // must match exactly with JS listener
    }
}



