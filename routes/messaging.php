<?php

use App\Http\Controllers\Messaging\ChatRoomController;
use App\Http\Controllers\Messaging\ChatMessageController;
use App\Http\Controllers\Messaging\ChatParticipantController;
use App\Http\Controllers\Messaging\ChatAttachmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // Chat Rooms
    Route::get('/chats', [ChatRoomController::class, 'index'])
        ->name('messaging.index');
    
    Route::get('/chats/{chatRoom}', [ChatRoomController::class, 'show'])
        ->name('messaging.show');

    // This route will be called from CollecteController when creating a new collecte
    Route::post('/chats/create/{collecte}', [ChatRoomController::class, 'create'])
        ->name('messaging.create');

    // Chat Messages
    Route::post('/chats/{chatRoom}/messages', [ChatMessageController::class, 'store'])
        ->name('messaging.messages.store');
    
    Route::delete('/messages/{message}', [ChatMessageController::class, 'destroy'])
        ->name('messaging.messages.destroy');

    // Chat Participants
    Route::post('/chats/{chatRoom}/participants/{user}', [ChatParticipantController::class, 'store'])
        ->middleware('can:manage-participants,chatRoom')
        ->name('messaging.participants.add');
    
    Route::delete('/chats/{chatRoom}/participants/{user}', [ChatParticipantController::class, 'destroy'])
        ->middleware('can:manage-participants,chatRoom')
        ->name('messaging.participants.remove');
    
    Route::post('/chats/{chatRoom}/leave', [ChatParticipantController::class, 'leave'])
        ->name('messaging.participants.leave');

    // File Attachments
    Route::get('/messages/{message}/download', [ChatAttachmentController::class, 'download'])
        ->name('messaging.attachments.download');
});
