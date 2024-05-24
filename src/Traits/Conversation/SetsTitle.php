<?php

namespace Emincmg\ConvoLite\Traits\Conversation;

use Emincmg\ConvoLite\Http\Controllers\Models\Conversation;

trait SetsTitle
{

    /**
     * Sets the title for the conversation.
     *
     * @param string $title The title to set.
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->update(['title' => $title]);
    }
}
