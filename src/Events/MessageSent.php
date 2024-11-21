<?php

namespace Emincmg\ConvoLite\Events;

use Emincmg\ConvoLite\Models\Message;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;

class MessageSent implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable,SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadCastOn():Channel
    {
        return new Channel('convo-lite');
    }

    public function broadcastQueue(): string
    {
        return 'convo-lite';
    }

    public function broadcastAs()
    {
        return 'MessageSent';
    }

    public function broadcastWith()
    {
        return [
            'created_at'=>$this->message->created_at,
            'body' => $this->message->body,
            'read_by'=>$this->message->readBy->map(function ($user) {
                return $user->name;
            }),
            'conversation'=>[
                'id'=>$this->message->conversation->id,
                'title'=>$this->message->conversation->title,
            ]
        ];
    }
}
