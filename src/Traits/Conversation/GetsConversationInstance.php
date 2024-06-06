<?php
namespace Emincmg\ConvoLite\Traits\Conversation;

use Emincmg\ConvoLite\Http\Controllers\Models\Conversation;
use Emincmg\ConvoLite\Traits\User;
use function Emincmg\ConvoLite\Traits\config;

trait GetsConversationInstance
{
    /**
     * Retrieve the conversation instance based on the provided user ID.
     *
     * @param int $conversationId The ID of the conversation.
     * @return \Emincmg\ConvoLite\Models\Conversation|null The conversation instance if found, or null if not found.
     */
    private static function getConversationInstance(int $conversationId): ?\Emincmg\ConvoLite\Models\Conversation
    {
        return \Emincmg\ConvoLite\Models\Conversation::with(['users','messages'])->find($conversationId);
    }
}
