<?php
namespace Emincmg\ConvoLite\Traits;

use Emincmg\ConvoLite\Http\Controllers\Models\Conversation;

trait MarksConversationStatus
{
    /**
     * Mark conversation status as given parameter.
     * @param int $conversationId
     * @param string $param
     * @return void
     */
    private function markConversationStatus(int $conversationId, string $param): void
    {
        $conversation = Conversation::findOrFail($conversationId);
        if ($conversation) {
            $conversation->update(['status' => $param]);
        }
    }
}
