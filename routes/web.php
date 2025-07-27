<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;

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
});



// Route::controller(ProfileController::class)->group(function () {
//     Route::get('/chatting', 'index')->name('chatting');
// });
// Route::get('/send-message', [ChatController::class, 'sendMessage'])->middleware('auth');


require __DIR__.'/auth.php';
