<?php

namespace Emincmg\ConvoLite\Events;

use Emincmg\ConvoLite\Models\Message;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable,SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadCastOn():Channel
    {
        return new Channel('convo-lite.message.sent');
    }
}
