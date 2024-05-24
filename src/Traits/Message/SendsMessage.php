<?php

namespace Emincmg\ConvoLite\Traits\Message;


use Emincmg\ConvoLite\Models\Message;
use Exception;
use function Emincmg\ConvoLite\Traits\config;

trait SendsMessage
{
    use AttachesFiles;

    /**
     * Sends a message with the given body, sender name, user ID, and optional files.
     *
     * @param string $body The body of the message
     * @param string $senderName The name of the sender
     * @param int $userId The ID of the user
     * @param array|null $files An optional array of file attachments
     *
     * @return Message|null The created message or null if the user was not found
     *
     * @throws Exception If the user is not found with the given user ID
     */
    public function sendMessage(string $body, string $senderName, int $userId, ?array $files): ?Message
    {
        $user = config('convo-lite.user_model')::findOrFail($userId);
        if (!$user) {
            throw new Exception("User not found with ID: $userId");
        }

        $message = new Message([
            'body' => $body,
            'user_id' => $userId,
            'sender_name' => $senderName,
        ]);

        if ($files !== null) {
            $this->attachFiles($files);
        }
        $this->messages()->save($message);

        return $message;
    }
}
