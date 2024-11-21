<?php

namespace Emincmg\ConvoLite\Events;

use Emincmg\ConvoLite\Models\Conversation;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;

class ConversationCreated implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, SerializesModels;

    public function __construct(public Conversation $conversation, public object $notifiable)
    {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->notifiable->id)
        ];
    }

    /**
     * The name of the queue on which to place the broadcasting job.
     */
    public function broadcastQueue(): string
    {
        return 'convo-lite';
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs()
    {
        return 'conversation.created';
    }

}
