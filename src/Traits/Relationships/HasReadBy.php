<?php

namespace Emincmg\ConvoLite\Traits\Relationships;

use Emincmg\ConvoLite\Models\Conversation;

trait HasReadBy
{
    public function readBy()
    {
        return $this->hasMany(ReadBy::class);
    }
}
