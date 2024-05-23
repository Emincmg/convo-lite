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
     * @param array|int $receiverIds
     * @param string $title
     * @return Conversation
     * @throws Exception
     */
    public function create(int $userId, array|int $receiverIds, string $title): Conversation
    {
        $conversation = Conversation::create([
            'title' => $title,
        ]);
        $user = $this->getUserInstance($userId);

        if (! $user) {
            throw new Exception("User not found with ID: $userId");
        }

        $user->conversations()->save($conversation);

        if (!is_array($receiverIds)) {
            $receiverIds = [$receiverIds];
        }

        foreach ($receiverIds as $receiverId) {
            $receiver = $this->getUserInstance($receiverId);
            if (! $receiver) {
                throw new Exception("Receiving user not found with ID: $receiverId");
            }
            $receiver->conversations()->save($conversation);
        }

        return $conversation;
    }

    private function getUserInstance(int $userId)
    {
        return config('convo-lite.user_model')::find($userId);
    }
}
