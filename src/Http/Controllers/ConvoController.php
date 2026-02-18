<?php

namespace Emincmg\ConvoLite\Http\Controllers;

use Emincmg\ConvoLite\Convo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ConvoController extends Controller
{
    public function conversations(Request $request): JsonResponse
    {
        $conversations = Convo::getConversationsForUser($request->user()->id);
        return response()->json($conversations);
    }

    public function createConversation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'receiver_ids' => 'required|array',
            'receiver_ids.*' => 'integer',
            'title' => 'nullable|string|max:255',
        ]);

        $conversations = Convo::createConversation(
            $request->user()->id,
            $validated['receiver_ids'],
            $validated['title'] ?? null
        );

        return response()->json($conversations->first(), 201);
    }

    public function messages(Request $request, int $conversationId): JsonResponse
    {
        $messages = Convo::getMessagesPaginated($conversationId, $request->input('per_page', 50));
        return response()->json($messages);
    }

    public function sendMessage(Request $request, int $conversationId): JsonResponse
    {
        $validated = $request->validate([
            'body' => 'required|string',
            'reply_to_id' => 'nullable|integer|exists:messages,id',
            'files' => 'nullable|array',
            'files.*' => 'file|max:10240',
        ]);

        $message = Convo::sendMessage(
            $conversationId,
            $request->user(),
            $validated['body'],
            $request->file('files'),
            $validated['reply_to_id'] ?? null
        );

        return response()->json($message, 201);
    }

    public function editMessage(Request $request, int $messageId): JsonResponse
    {
        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        $message = Convo::editMessage($messageId, $validated['body']);
        return response()->json($message);
    }

    public function deleteMessage(int $messageId): JsonResponse
    {
        Convo::deleteMessage($messageId);
        return response()->json(['success' => true]);
    }

    public function markAsRead(Request $request, int $messageId): JsonResponse
    {
        $readBy = Convo::markMessageAsRead($messageId, $request->user());
        return response()->json($readBy);
    }

    public function addReaction(Request $request, int $messageId): JsonResponse
    {
        $validated = $request->validate([
            'emoji' => 'required|string|max:32',
        ]);

        $reaction = Convo::addReaction($messageId, $request->user(), $validated['emoji']);
        return response()->json($reaction);
    }

    public function removeReaction(Request $request, int $messageId): JsonResponse
    {
        $validated = $request->validate([
            'emoji' => 'required|string|max:32',
        ]);

        Convo::removeReaction($messageId, $request->user(), $validated['emoji']);
        return response()->json(['success' => true]);
    }

    public function typing(Request $request, int $conversationId): JsonResponse
    {
        $validated = $request->validate([
            'is_typing' => 'required|boolean',
        ]);

        Convo::broadcastTyping($conversationId, $request->user(), $validated['is_typing']);
        return response()->json(['success' => true]);
    }
}
