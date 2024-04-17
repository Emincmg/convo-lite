<?php

namespace Emincmg\ConvoLite\Traits;


use Emincmg\ConvoLite\Models\Message;

trait SendsMessage
{
    /**
     * Send a message to the existing conversation.
     * @param string $body
     * @param int $userId
     * @return void
     */
    public function sendMessage(string $body, int $userId): void
    {
        $user = config('convo-lite.user_model')::findOrFail($userId);
        $message = new Message([
            'body' => $body,
            'user_id' => $userId,
            'sender_name' => $user->name,
        ]);
        $this->messages()->save($message);
    }
}
