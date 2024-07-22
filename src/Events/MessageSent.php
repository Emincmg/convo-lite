<?php

namespace Emincmg\ConvoLite\Events;

use Emincmg\ConvoLite\Models\Message;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class MessageSent
{
    use Dispatchable,SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }
}
