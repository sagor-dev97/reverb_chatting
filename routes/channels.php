<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('audio-call.{userId}', function ($user, $userId) {
    return true;
});


Broadcast::channel('video-call.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});