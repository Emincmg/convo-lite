<?php

namespace Emincmg\ConvoLite\Traits;

use Emincmg\ConvoLite\Models\Conversation;

trait HasConversations
{
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class);
    }
}
