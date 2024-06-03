<?php

namespace Emincmg\ConvoLite;

use Emincmg\ConvoLite\Traits\Conversation\CreatesConversations;

class Convo
{

    /**
     * Creates a new conversation.
     *
     * @param int $userId The ID of the user creating the conversation.
     * @param array|int $receiverIds The ID(s) of the user(s) who will receive the conversation.
     * @param string|null $title The title of the conversation.
     *
     * @return Convo The created conversation.
     *
     */
    public function createConversation(int $userId, array|int $receiverIds, ?string $title = null): Convo
    {
        $conversation = Conversation::create([
            'title' => $title,
        ]);
        $user = $this->getUserInstance($userId);

        if (! $user) {
            throw new Exception("User not found with ID: $userId");
        }

        $user->conversations()->save($conversation);

        if (!is_array($receiverIds)) {
            $receiverIds = [$receiverIds];
        }

        foreach ($receiverIds as $receiverId) {
            $receiver = $this->getUserInstance($receiverId);
            if (! $receiver) {
                throw new Exception("Receiving user not found with ID: $receiverId");
            }
            $receiver->conversations()->save($conversation);
        }

        return $conversation;
    }

    /**
     * Marks the status of a conversation.
     *
     * @param Convo $conversation The conversation that will be marked.
     * @param string $param The new status to be assigned to the conversation.
     *
     * @return void
     *
     */
    public function markConversationStatus(Convo $conversation, string $param): void
    {
        $conversation->update(['status' => $param]);
    }

    /**
     * Get a Conversation by its ID.
     *
     * @param int $id The ID of the Conversation.
     * @return Convo The Conversation object matching the given ID.
     * @throws Symfony\Component\HttpKernel\Exception\NotFoundHttpException If no Conversation is found with the given ID.
     */
    public static function getById(int $id): Convo
    {
        return Convo::findOrFail($id);
    }

    /**
     * Get a Conversation by its title.
     *
     * @param string $title The title of the Conversation.
     * @return Convo|null The Conversation object matching the given title or null if no Conversation is found.
     */
    public static function getByTitle(string $title): ?Convo
    {
        return Convo::where('title', $title)->first();
    }
}
