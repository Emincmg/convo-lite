<?php

namespace Emincmg\ConvoLite\Traits;

use Emincmg\ConvoLite\Models\Message;

trait MarksMessageStatus
{
    /**
     * Mark message as read/unread. True if read, false if unread.
     * @param int $messageId
     * @param int $id
     * @param bool $status
     * @return void
     */
    private function markMessageStatus(int $messageId, int $id, bool $status): void
    {
        $message = Message::findOrFail($messageId);
        if ($message){
            $messageReadData = json_decode($message->read_by_id, true) ?? [];
            if ($status === true) {
                $messageReadData[] = $id;
            } else {
                $idToDelete = array_search($id, $messageReadData);
                if ($idToDelete !== false) {
                    unset($messageReadData[$idToDelete]);
                }
            }
            $updatedJson = json_encode($messageReadData);
            $message->update(['read_by_id' => $updatedJson]);
        }
    }
}
