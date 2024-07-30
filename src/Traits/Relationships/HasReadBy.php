<?php

namespace Emincmg\ConvoLite\Traits\Relationships;

use Emincmg\ConvoLite\Models\Conversation;
use Emincmg\ConvoLite\Models\ReadBy;

trait HasReadBy
{
    public function readBy()
    {
        return $this->hasMany(ReadBy::class);
    }
}
