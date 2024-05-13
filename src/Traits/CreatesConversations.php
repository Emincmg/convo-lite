<?php

namespace Emincmg\ConvoLite\Traits;

use Emincmg\ConvoLite\Models\Conversation;
use Exception;

trait CreatesConversations
{
    /**
     * Create a conversation between users.
     *
     * @param int $userId
     * @param int|array $receiverIds
     * @param string $title
     * @return Conversation
     * @throws Exception
     */
    public function create(int $userId, int|array $receiverIds, string $title): Conversation
    {
        $conversation = Conversation::create([
            'title' => $title,
        ]);
        $user = config('convo_lite.user_model')::find($userId);
        if (!$user) {
            throw new Exception("User not found with ID: $userId");
        }
        $user->conversations()->save($conversation);

        if (is_array($receiverIds)) {
            foreach ($receiverIds as $receiverId) {
                $receiver = config('convo_lite.user_model')::find($receiverId);
                if (!$receiver) {
                    throw new Exception("Receiver not found with ID: $receiverId");
                }
                $receiver->conversations()->save($conversation);
            }
        } else {
            $receiver = config('convo_lite.user_model')::find($receiverIds);
            if (!$receiver) {
                throw new Exception("Receiver not found with ID: $receiverIds");
            }
            $receiver->conversations()->save($conversation);
        }

        return $conversation;
    }
}
