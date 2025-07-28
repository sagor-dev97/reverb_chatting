<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class CallEnded implements ShouldBroadcastNow
{
    use Dispatchable,InteractsWithSockets, SerializesModels;

    public $fromUserId;
    public $fromUserName;
    public $toUserId;

    public function __construct($fromUserId, $fromUserName, $toUserId)
    {
        $this->fromUserId = $fromUserId;
        $this->fromUserName = $fromUserName;
        $this->toUserId = $toUserId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('audio-call.' . $this->toUserId);
    }

    public function broadcastWith()
    {
        return [
            'fromUserId' => $this->fromUserId,
            'fromUserName' => $this->fromUserName,
        ];
    }
}
