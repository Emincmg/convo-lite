<?php

namespace Emincmg\ConvoLite;

use Emincmg\ConvoLite\Models\Conversation;
use Emincmg\ConvoLite\Models\Message;
use Emincmg\ConvoLite\Traits\GetsUserInstance;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class Convo
{
    use GetsUserInstance;

    /**
     * Creates a new conversation.
     *
     * @param int $creatorId The ID of the user creating the conversation.
     * @param array|int $receiverIds The ID(s) of the user(s) who will receive the conversation.
     * @param string|null $title The title of the conversation.
     *
     * @return Conversation The created conversation.
     */
    public static function createConversation(int $creatorId, array|int $receiverIds, ?string $title = null): Conversation
    {
        $conversations = collect();

        $creator = self::getUserInstance($creatorId);
        if (!$creator) {
            throw new \Exception("Creator user not found with ID: $creatorId");
        }

        foreach ((array)$receiverIds as $receiverId) {
            $receiver = self::getUserInstance($receiverId);
            if (!$receiver) {
                throw new \Exception("Receiving user not found with ID: $receiverId");
            }

            $conversation = new Conversation();
            $conversation->title = $title;
            $conversation->save();

            $conversation->users()->attach([$creator->id, $receiver->id]);

            $conversations->push($conversation);
        }

        return $conversations;
    }

    /**
     * Attach user(s) to a conversation.
     *
     * @param Conversation|int $conversation Conversation that user(s) will be attached.
     * @param int|array $users Array of user IDs or user instances.
     * @throws \Exception
     */
    public static function attachParticipators(Conversation|int $conversation, int|array $users): void
    {
        if (is_int($conversation)) {
            $conversation = Conversation::findOrFail($conversation);
        }

        if (!is_array($users)) {
            $users = [$users];
        }

        $userIds = [];

        foreach ($users as $user) {
            if (!is_int($user)) {
                throw new \Exception('Invalid user ID.');
            }
            $userIds[] = $user;
        }

        $conversation->users()->attach($userIds);
    }


    /**
     * Marks the status of a conversation.
     *
     * @param Conversation|int $conversation The conversation instance or its id that will be marked.
     * @param string $param The new status to be assigned to the conversation.
     *
     * @return void
     * @throws Symfony\Component\HttpKernel\Exception\NotFoundHttpException If no Conversation is found with the given ID.
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
     * @return \Illuminate\Support\Collection Collection of attachments.
     */
    public static function getConversationAttachments(Conversation|int $conversation): \Illuminate\Support\Collection
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
     * @param string $messageContent Message body that will be sent.
     * @param UploadedFile|array|null $file File(s) that will be attached to the message. Can be an array of files or a file.
     * @return Message Message resource that will be returned.
     */
    public static function sendMessage(Conversation|int $conversation, mixed $user, string $messageContent, UploadedFile|array|null $file = null): Message
    {
        $message = new Message();
        $message->body = $messageContent;

        if (is_int($conversation)) {
            $conversation = Conversation::findOrFail($conversation);
        }

        $message->conversation_id = $conversation->id;

        if (is_int($user)) {
            $user = self::getUserInstance($user);
        }

        if (!$user) {
            throw new Exception("User not found.");
        }

        $message->user_id = $user->id;

        if ($file) {
            $message->attachFiles($file);
        }

        $message->save();

        return $message;
    }

    public static function sendMessageAnd()
    {

    }
}
