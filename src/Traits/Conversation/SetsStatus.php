<?php

namespace Emincmg\ConvoLite\Traits\Conversation;

use Emincmg\ConvoLite\Http\Controllers\Models\Conversation;

trait SetsStatus
{

    /**
     * Set the status of the conversation.
     *
     * @param string $status The status to set.
     * @return void
     */
    private function setStatus(string $status): void
    {
        $this->update(['status'=>$status]);
    }
}
