<?php

namespace Emincmg\ConvoLite\Traits\Message;

use Emincmg\ConvoLite\Exceptions\MessageNotFoundException;
use Emincmg\ConvoLite\Models\Message;

trait GetsMessageInstance
{
    /**
     * Retrieve the message instance based on the provided ID.
     *
     * @param int $messageId The ID of the message.
     * @return Message The message instance.
     * @throws MessageNotFoundException If no message is found with the given ID.
     */
    private static function getMessageInstance(int $messageId): Message
    {
        $message = Message::find($messageId);

        if (!$message) {
            throw new MessageNotFoundException($messageId);
        }

        return $message;
    }
}
