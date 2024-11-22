<?php

namespace Emincmg\ConvoLite;

use Emincmg\ConvoLite\Events\ConversationCreated;
use Emincmg\ConvoLite\Events\MessageSent;
use Emincmg\ConvoLite\Models\Conversation;
use Emincmg\ConvoLite\Models\Message;
use Emincmg\ConvoLite\Models\ReadBy;
use Emincmg\ConvoLite\Traits\Conversation\GetsConversationInstance;
use Emincmg\ConvoLite\Traits\GetsUserInstance;
use Emincmg\ConvoLite\Traits\Message\GetsMessageInstance;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class Convo
{
    use GetsUserInstance, GetsConversationInstance, GetsMessageInstance;

    /**
     * Creates a new conversation.
     *
     * @param int $creatorId The ID of the user creating the conversation.
     * @param array|int $receiverIds The ID(s) of the user(s) who will receive the conversation.
     * @param string|null $title The title of the conversation.
     *
     * @return Collection Collection containing created conversations.
     * @throws \Exception If not found user with given ID.
     */
    public static function createConversation(int $creatorId, array|int $receiverIds, ?string $title = null): Collection
    {
        $conversations = collect();

        $creator = self::getUserInstance($creatorId);
        if (!$creator) {
            throw new \Exception("Creator user not found with ID: $creatorId");
        }

        if (in_array($creatorId, (array)$receiverIds)) {
            throw new \Exception('Conversation creator can not be the same with receiver');
        }

        foreach ((array)$receiverIds as $receiverId) {
            $receiver = self::getUserInstance($receiverId);
            if (!$receiver) {
                throw new \Exception("Receiving user not found with ID: $receiverId");
            }

            $conversation = new Conversation();
            $conversation->title = $title ?? 'New Conversation';
            $conversation->save();

            $conversation->users()->attach([$creator->id, $receiver->id]);

            $conversation->load('users', 'messages');
            $conversations->push($conversation);

            event(new ConversationCreated($conversation, $receiver));
        }

        return $conversations;
    }

    /**
     * Add user(s) to a conversation.
     *
     * @param Conversation|int $conversation Conversation (or its ID) that user(s) will be attached.
     * @param int|array $users Array of user IDs or user instances.
     * @throws \Exception If no conversation is found with given ID.
     */
    public static function addParticipators(Conversation|int $conversation, int|array $users): void
    {
        self::manageParticipants($conversation, $users, 'attach');
    }

    /**
     * Remove user(s) from a conversation.
     *
     * @param Conversation|int $conversation Conversation (or its ID) that user(s) will be attached.
     * @param int|array $users Array of user IDs or user instances.
     * @throws \Exception If no conversation is found with given ID.
     */
    public static function removeParticipators(Conversation|int $conversation, int|array $users): void
    {
        self::manageParticipants($conversation, $users, 'detach');
    }

    /**
     * Manage attaching/detaching participators from a conversation.
     *
     * @param Conversation|int $conversation Conversation (or its ID) that user(s) will be attached/detached.
     * @param int|array $users Array of user IDs or user instances.
     * @param string $intent Intent of calling. Can be either attach or detach.
     * @return void
     * @throws \Exception If no conversation is found with given ID.
     */
    private static function manageParticipants(Conversation|int $conversation, int|array $users, string $intent): void
    {
        if (is_int($conversation)) {
            $conversation = self::getConversationInstance($conversation);
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
        if ($intent === 'attach') {
            $conversation->users()->attach($userIds);
        } else {
            $conversation->users()->detach($userIds);
        }
    }


    /**
     * Mark the status of a conversation.
     *
     *  This method updates the status of a message. It accepts either a Conversation instance
     *  or a conversation ID (integer) and updates the status field in the database.
     *
     * @param Conversation|int $conversation The conversation instance or its id that will be marked.
     * @param string $param The new status to be assigned to the conversation.
     *
     * @return Conversation Conversation instance that its status is updated.
     * @throws Symfony\Component\HttpKernel\Exception\NotFoundHttpException If no Conversation is found with the given ID.
     */
    public static function markConversationStatus(Conversation|int $conversation, string $param): Conversation
    {
        if (is_int($conversation)) {
            $conversation = self::getConversationInstance($conversation);
        }
        $conversation->update(['status' => $param]);
        return $conversation;
    }

    /**
     * Mark the status of a given message.
     *
     * This method updates the status of a message. It accepts either a Message instance
     * or a message ID (integer) and updates the status field in the database.
     *
     * @param Message|int $message The Message model instance or message ID to update.
     * @param User|int $user The user instance or its ID to mark the message as read.
     * @param string $param The new status value to set for the message.
     *
     * @return ReadBy Readby instance that related to both conversation and message.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the message ID does not exist in the database.
     */
    public static function markMessageAsRead(Message|int $message, User|int $user, string $param): ReadBy
    {
        if (is_int($message)) {
            $message = self::getMessageInstance($message);
        }

        if (is_int($user)) {
            $user = self::getUserInstance($user);
        }

        if ($message instanceof Message) {
            return ReadBy::create([
                'conversation_id' => $message->conversation->id,
                'message_id' => $message->id,
                'user_id' => $user->id,
            ]);
        } else {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Invalid message ID.');
        }
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
        return self::getConversationInstance($id);
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
            $conversation = self::getConversationInstance($conversation);
        }

        return $conversation->attachments;
    }

    /**
     * Return the messages of a conversation via conversation model.
     *
     * @param Conversation $conversation The conversation that its messages will be returned.
     * @return Collection The collection of messages of the conversation.
     */
    public static function getMessagesByConversation(Conversation $conversation): Collection
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
            $conversation = self::getConversationInstance($conversation);
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

        ReadBy::create([
            'conversation_id' => $conversation->id,
            'message_id' => $message->id,
            'user_id' => $user->id,
        ]);

        event(new MessageSent($message));

        $message->load('user', 'conversation', 'attachments', 'readBy');

        return $message;
    }
}
