<?php
namespace Emincmg\ConvoLite\Traits\Message;

use Emincmg\ConvoLite\Http\Controllers\Models\Conversation;
use Emincmg\ConvoLite\Traits\User;
use function Emincmg\ConvoLite\Traits\config;

trait GetsMessageInstance
{
    /**
     * Retrieve the conversation instance based on the provided user ID.
     *
     * @param int $messageId The ID of the message.
     * @return \Emincmg\ConvoLite\Models\Message|null The message instance if found, or null if not found.
     */
    private static function getMessageInstance(int $messageId): ?\Emincmg\ConvoLite\Models\Message
    {
        return \Emincmg\ConvoLite\Models\Message::find($messageId);
    }
}
