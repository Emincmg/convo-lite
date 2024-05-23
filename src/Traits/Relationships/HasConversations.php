<?php

namespace Emincmg\ConvoLite\Traits\Relationships;

use Emincmg\ConvoLite\Models\Conversation;

trait HasConversations
{
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class);
    }
}
