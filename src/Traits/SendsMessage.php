<?php

namespace Emincmg\ConvoLite\Traits;


use Emincmg\ConvoLite\Models\Message;
use Exception;

trait SendsMessage
{
    use AttachesFiles;

    /**
     * Send a message to the existing conversation. Accepts array of UploadedFiles as a parameter.
     *
     * @param string $body
     * @param int $userId
     * @param array|null $files
     * @return Message|null
     * @throws Exception
     */
    public function sendMessage(string $body, int $userId, ?array $files): ?Message
    {
        $user = config('convo-lite.user_model')::findOrFail($userId);
        if (!$user) {
            throw new Exception("User not found with ID: $userId");
        }

        $message = new Message([
            'body' => $body,
            'user_id' => $userId,
            'sender_name' => $user->name,
        ]);;
        if ($files !== null) {
            $this->attachFiles($files);
        }
        $this->messages()->save($message);

        return $message;
    }
}
