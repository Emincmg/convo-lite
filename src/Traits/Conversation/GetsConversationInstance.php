<?php

namespace Emincmg\ConvoLite\Traits\Conversation;

use Emincmg\ConvoLite\Exceptions\ConversationNotFoundException;
use Emincmg\ConvoLite\Models\Conversation;

trait GetsConversationInstance
{
    /**
     * Retrieve the conversation instance based on the provided ID.
     *
     * @param int $conversationId The ID of the conversation.
     * @return Conversation The conversation instance.
     * @throws ConversationNotFoundException If no conversation is found with the given ID.
     */
    private static function getConversationInstance(int $conversationId): Conversation
    {
        $conversation = Conversation::with(['users', 'messages'])->find($conversationId);

        if (!$conversation) {
            throw new ConversationNotFoundException($conversationId);
        }

        return $conversation;
    }
}
