<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class CallSignal implements ShouldBroadcastNow
{
    use Dispatchable,InteractsWithSockets, SerializesModels;

    public $fromUserId;
    public $fromUserName;
    public $toUserId;
    public $signalData;

    public function __construct($fromUserId, $fromUserName, $toUserId, $signalData)
    {
        $this->fromUserId = $fromUserId;
        $this->fromUserName = $fromUserName;
        $this->toUserId = $toUserId;
        $this->signalData = $signalData;
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
            'signalData' => $this->signalData,
        ];
    }
     public function broadcastAs()
    {
        return 'IncomingCall';  // must match exactly with JS listener
    }
}
