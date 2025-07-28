<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AudioCallController;
use App\Http\Controllers\VideoCallController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::get('/chat/{user}/messages', [ChatController::class, 'fetchMessages'])->name('chat.messages');
    Route::post('/chat/{user}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/messages/mark-as-read/{senderId}', [ChatController::class, 'markMessagesAsRead'])->name('messages.mark-as-read');


    Route::get('/audio-call', [AudioCallController::class, 'index'])->name('audio-call');


    Route::post('/video-call/request/{user}', [VideoCallController::class, 'requestVideoCall'])->name('video-call.request');
    Route::post('/video-call/request/status/{user}', [VideoCallController::class, 'requestVideoCallStatus'])->name('video-call.request-status');
});
 

// Route::get('/audio-call', [AudioCallController::class, 'index'])->middleware('auth');
Route::post('/audio-call/incoming', [AudioCallController::class, 'incoming'])->middleware('auth');
Route::post('/audio-call/signal', [AudioCallController::class, 'signal'])->middleware('auth');
Route::post('/audio-call/ended', [AudioCallController::class, 'ended'])->middleware('auth');


// Route::controller(ProfileController::class)->group(function () {
//     Route::get('/chatting', 'index')->name('chatting');
// });
// Route::get('/send-message', [ChatController::class, 'sendMessage'])->middleware('auth');


require __DIR__.'/auth.php';
