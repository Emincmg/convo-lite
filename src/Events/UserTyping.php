<?php

namespace Emincmg\ConvoLite\Events;

use Emincmg\ConvoLite\Models\Conversation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Conversation $conversation;
    public mixed $user;
    public bool $isTyping;

    public function __construct(Conversation $conversation, mixed $user, bool $isTyping = true)
    {
        $this->conversation = $conversation;
        $this->user = $user;
        $this->isTyping = $isTyping;
    }

    public function broadcastOn(): array
    {
        return $this->conversation->users
            ->where('id', '!=', $this->user->id)
            ->map(fn($user) => new PrivateChannel("user.{$user->id}"))
            ->toArray();
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name ?? 'User',
            'is_typing' => $this->isTyping,
        ];
    }
}
