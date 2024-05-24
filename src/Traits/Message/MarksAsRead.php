<?php

namespace Emincmg\ConvoLite\Traits\Message;

use Emincmg\ConvoLite\Models\Message;

trait MarksAsRead
{
    /**
     * Marks a user as read.
     *
     * @param int|array $userId The ID of the user to mark as read.
     * @return void
     */
    public function markAsRead(int|array $userId): void
    {
        $readers = json_decode($this->read_by_id, true);

        if (!is_array($readers)) {
            $readers = [];
        }

        $readers[] = $userId;

        $this->update(['read_by_id' => json_encode($readers)]);
    }
}
