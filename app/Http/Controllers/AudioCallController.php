<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Events\IncomingCall;
use App\Events\CallSignal;
use App\Events\CallEnded;
use App\Events\AudioCallEvent;

class AudioCallController extends Controller
{
    /**
     * Show the audio call interface with a list of users.
     */
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('audio_call', compact('users'));
    }

    /**
     * Send an incoming call event to the receiver.
     */
  public function incoming(Request $request)
{
    $request->validate([
        'toUserId' => 'required|exists:users,id',
    ]);

    $fromUser = auth()->user();
    $toUserId = $request->toUserId;

    broadcast(new IncomingCall(
        $fromUser->id,
        $fromUser->name,
        $toUserId
    ));

    return response()->json(['status' => 'Call initiated']);
}




    /**
     * Handle the signaling data exchange.
     */
    public function signal(Request $request)
    {
        $request->validate([
            'toUserId' => 'required|exists:users,id',
            'signalData' => 'required'
        ]);

        $fromUser = auth()->user();
        
        broadcast(new CallSignal(
            $fromUser->id,
            $fromUser->name,
            $request->toUserId,
            $request->signalData
        ))->toOthers();

        return response()->json(['status' => 'Signal sent']);
    }


    /**
     * Handle the call end event.
     */
    public function ended(Request $request)
    {
        $request->validate([
            'toUserId' => 'required|exists:users,id'
        ]);

        broadcast(new CallEnded(
            Auth::id(),
            Auth::user()->name,
            $request->toUserId
        ))->toOthers();

        return response()->json(['status' => 'Call ended']);
    }
}
