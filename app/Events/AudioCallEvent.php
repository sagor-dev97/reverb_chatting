<?php

use Illuminate\Support\Facades\Log;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class AudioCallEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $toUserId;
    public $fromUserId;
    public $fromUserName;
    public $fromUserImage;
    public $signalData;
    public $type;

    public function __construct($toUserId, $fromUserId, $fromUserName, $fromUserImage = null, $signalData = null, $type)
    {
        $this->toUserId = $toUserId;
        $this->fromUserId = $fromUserId;
        $this->fromUserName = $fromUserName;
        $this->fromUserImage = $fromUserImage;
        $this->signalData = $signalData;
        $this->type = $type;

        Log::info($this->type);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('audio-call.' . $this->toUserId);
    }

    public function broadcastWith()
    {
        return [
            'toUserId' => $this->toUserId,
            'fromUserId' => $this->fromUserId,
            'fromUserName' => $this->fromUserName,
            'fromUserImage' => $this->fromUserImage,
            'signalData' => $this->signalData,
            'type' => $this->type,
        ];
    }

    public function broadcastAs()
    {
        return 'IncomingCall';
    }
}

 



