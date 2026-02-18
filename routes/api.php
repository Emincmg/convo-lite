<?php

use Emincmg\ConvoLite\Http\Controllers\ConvoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'auth:sanctum'])->prefix('convo-lite')->group(function () {
    Route::get('/conversations', [ConvoController::class, 'conversations']);
    Route::post('/conversations', [ConvoController::class, 'createConversation']);

    Route::get('/conversations/{conversationId}/messages', [ConvoController::class, 'messages']);
    Route::post('/conversations/{conversationId}/messages', [ConvoController::class, 'sendMessage']);
    Route::post('/conversations/{conversationId}/typing', [ConvoController::class, 'typing']);

    Route::put('/messages/{messageId}', [ConvoController::class, 'editMessage']);
    Route::delete('/messages/{messageId}', [ConvoController::class, 'deleteMessage']);
    Route::post('/messages/{messageId}/read', [ConvoController::class, 'markAsRead']);
    Route::post('/messages/{messageId}/reactions', [ConvoController::class, 'addReaction']);
    Route::delete('/messages/{messageId}/reactions', [ConvoController::class, 'removeReaction']);
});
