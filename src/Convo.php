<?php

namespace Emincmg\ConvoLite;

use Cassandra\Collection;
use Emincmg\ConvoLite\Models\Conversation;
use Emincmg\ConvoLite\Models\Message;
use Emincmg\ConvoLite\Traits\GetsUserInstance;
use Illuminate\Http\UploadedFile;

class Convo
{
    use GetsUserInstance;

    /**
     * Creates a new conversation.
     *
     * @param int $userId The ID of the user creating the conversation.
     * @param array|int $receiverIds The ID(s) of the user(s) who will receive the conversation.
     * @param string|null $title The title of the conversation.
     *
     * @return Conversation The created conversation.
     */
    public static function createConversation(int $userId, array|int $receiverIds, ?string $title = null): Conversation
    {
        $conversation = Conversation::create([
            'title' => $title,
        ]);
        $user = self::getUserInstance($userId);

        if (!$user) {
            throw new Exception("User not found with ID: $userId");
        }

        $user->conversations()->save($conversation);

        if (!is_array($receiverIds)) {
            $receiverIds = [$receiverIds];
        }

        foreach ($receiverIds as $receiverId) {
            $receiver = self::getUserInstance($receiverId);
            if (!$receiver) {
                throw new Exception("Receiving user not found with ID: $receiverId");
            }
            $receiver->conversations()->save($conversation);
        }

        return $conversation;
    }

    /**
     * Marks the status of a conversation.
     *
     * @param Conversation|int $conversation The conversation instance or its id that will be marked.
     * @param string $param The new status to be assigned to the conversation.
     *
     * @return void
     *
     */
    public static function markConversationStatus(Conversation|int $conversation, string $param): void
    {
        if (is_int($conversation)) {
            $conversation = Conversation::findOrFail($conversation);
        }
        $conversation->update(['status' => $param]);
    }

    /**
     * Get a Conversation by its ID.
     *
     * @param int $id The ID of the Conversation.
     * @return Conversation The Conversation object matching the given ID.
     * @throws Symfony\Component\HttpKernel\Exception\NotFoundHttpException If no Conversation is found with the given ID.
     */
    public static function getConversationById(int $id): Conversation
    {
        return Conversation::findOrFail($id);
    }


    /**
     * Get all conversation attachments.
     *
     * @param Conversation|int $conversation Conversation model or its id.
     * @return Collection Collection of attachments.
     */
    public static function getConversationAttachments(Conversation|int $conversation): Collection
    {
        if (is_int($conversation)) {
            $conversation = Conversation::findOrFail($conversation);
        }

        return $conversation->attachments;
    }

    /**
     * Get a Conversation by its title.
     *
     * @param string $title The title of the Conversation.
     * @return Convo|null The Conversation object matching the given title or null if no Conversation is found.
     */
    public static function getConversationByTitle(string $title): ?Conversation
    {
        return Conversation::where('title', $title)->first();
    }

    /**
     * Return the messages of a conversation via conversation model.
     *
     * @param Conversation $conversation The conversation that its messages will be returned.
     * @return \Illuminate\Support\Collection The collection of messages of the conversation.
     */
    public static function getMessagesByConversation(Conversation $conversation): \Illuminate\Support\Collection
    {
        return $conversation->messages;
    }

    /**
     * Send a message to a conversation.
     *
     * @param Conversation|int $conversation Conversation that message will be sent to. Can be an instance of Conversation or its id.
     * @param mixed $user Message sender. Can be user id or direct model of the sender.
     * @param Message|string|null $message Message that will be sent. Can be string or Message instance.
     * @param UploadedFile|array|null $file File(s) that will be attached to the message. Can be array of files or a file.
     * @return Message Message resource that will be returned.
     */
    public static function sendMessage(Conversation|int $conversation, mixed $user, Message|string|null $message = null, UploadedFile|array|null $file = null): Message
    {
        $userModel = config('convo-lite.user_model');

        if (is_int($conversation)) {
            $conversation = Conversation::findOrFail($conversation);
        }

        if (is_string($message)) {
            $message = new Message();
            $message->body = $message;
        }

        if ($file) {
            $message->attachFiles($file);
        }

        if ($user instanceof $userModel) {
            $user->messages()->save($message);
        } else if (is_int($userModel)) {
            $user = self::getUserInstance($user);
            $user->messages()->save($message);
            $conversation->messages()->save($message);
        }

        return $message;
    }
}
