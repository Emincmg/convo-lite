<?php

namespace Emincmg\ConvoLite;

use Emincmg\ConvoLite\Events\ConversationCreated;
use Emincmg\ConvoLite\Events\MessageSent;
use Emincmg\ConvoLite\Events\UserOnlineStatusChanged;
use Emincmg\ConvoLite\Events\UserTyping;
use Emincmg\ConvoLite\Exceptions\InvalidParticipantException;
use Emincmg\ConvoLite\Exceptions\UserNotFoundException;
use Emincmg\ConvoLite\Models\Conversation;
use Emincmg\ConvoLite\Models\Message;
use Emincmg\ConvoLite\Models\Reaction;
use Emincmg\ConvoLite\Models\ReadBy;
use Emincmg\ConvoLite\Traits\Conversation\GetsConversationInstance;
use Emincmg\ConvoLite\Traits\GetsUserInstance;
use Emincmg\ConvoLite\Traits\Message\GetsMessageInstance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
     * @throws UserNotFoundException If user not found with given ID.
     * @throws InvalidParticipantException If creator and receiver are the same.
     */
    public static function createConversation(int $creatorId, array|int $receiverIds, ?string $title = null): Collection
    {
        $conversations = collect();

        $creator = self::getUserInstance($creatorId);

        if (in_array($creatorId, (array)$receiverIds)) {
            throw new InvalidParticipantException('Conversation creator can not be the same with receiver');
        }

        foreach ((array)$receiverIds as $receiverId) {
            $receiver = self::getUserInstance($receiverId);

            $existingConversation = self::findExistingConversation($creatorId, $receiverId);
            if ($existingConversation) {
                $conversations->push($existingConversation);
                continue;
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
     * Find existing conversation between two users.
     *
     * @param int $userId1 First user ID.
     * @param int $userId2 Second user ID.
     * @return Conversation|null The existing conversation or null.
     */
    private static function findExistingConversation(int $userId1, int $userId2): ?Conversation
    {
        return Conversation::whereHas('users', function ($query) use ($userId1) {
            $query->where('user_id', $userId1);
        })->whereHas('users', function ($query) use ($userId2) {
            $query->where('user_id', $userId2);
        })->first();
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
                throw new InvalidParticipantException('Invalid user ID.');
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
     * Mark a message as read by a user.
     *
     * @param Message|int $message The Message model instance or message ID.
     * @param mixed $user The user instance or its ID.
     *
     * @return ReadBy ReadBy instance that related to both conversation and message.
     */
    public static function markMessageAsRead(Message|int $message, mixed $user): ReadBy
    {
        if (is_int($message)) {
            $message = self::getMessageInstance($message);
        }

        if (is_int($user)) {
            $user = self::getUserInstance($user);
        }

        return ReadBy::create([
            'conversation_id' => $message->conversation->id,
            'message_id' => $message->id,
            'user_id' => $user->id,
        ]);
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
     * @param Conversation|int $conversation Conversation that message will be sent to.
     * @param mixed $user Message sender. Can be user id or model.
     * @param string $messageContent Message body that will be sent.
     * @param UploadedFile|array|null $file File(s) that will be attached.
     * @param Message|int|null $replyTo Message to reply to.
     * @return Message Message resource that will be returned.
     */
    public static function sendMessage(
        Conversation|int $conversation,
        mixed $user,
        string $messageContent,
        UploadedFile|array|null $file = null,
        Message|int|null $replyTo = null
    ): Message {
        $message = new Message();
        $message->body = $messageContent;

        if (is_int($conversation)) {
            $conversation = self::getConversationInstance($conversation);
        }

        $message->conversation_id = $conversation->id;

        if (is_int($user)) {
            $user = self::getUserInstance($user);
        }

        $message->user_id = $user->id;

        if ($replyTo) {
            $message->reply_to_id = is_int($replyTo) ? $replyTo : $replyTo->id;
        }

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

        $message->load('user', 'conversation', 'attachments', 'readBy', 'replyTo');

        return $message;
    }

    /**
     * Edit an existing message.
     *
     * @param Message|int $message The message instance or its ID.
     * @param string $newContent The new message content.
     * @return Message The updated message.
     */
    public static function editMessage(Message|int $message, string $newContent): Message
    {
        if (is_int($message)) {
            $message = self::getMessageInstance($message);
        }

        $message->update(['body' => $newContent]);

        return $message->fresh();
    }

    /**
     * Delete a message.
     *
     * @param Message|int $message The message instance or its ID.
     * @return bool True if deleted successfully.
     */
    public static function deleteMessage(Message|int $message): bool
    {
        if (is_int($message)) {
            $message = self::getMessageInstance($message);
        }

        return $message->delete();
    }

    /**
     * Delete a conversation and all its messages.
     *
     * @param Conversation|int $conversation The conversation instance or its ID.
     * @return bool True if deleted successfully.
     */
    public static function deleteConversation(Conversation|int $conversation): bool
    {
        if (is_int($conversation)) {
            $conversation = self::getConversationInstance($conversation);
        }

        $conversation->messages()->delete();
        $conversation->attachments()->delete();
        $conversation->users()->detach();

        return $conversation->delete();
    }

    /**
     * Get paginated messages of a conversation.
     *
     * @param Conversation|int $conversation The conversation instance or its ID.
     * @param int $perPage Number of messages per page.
     * @return LengthAwarePaginator Paginated messages.
     */
    public static function getMessagesPaginated(Conversation|int $conversation, int $perPage = 20): LengthAwarePaginator
    {
        if (is_int($conversation)) {
            $conversation = self::getConversationInstance($conversation);
        }

        return $conversation->messages()
            ->with(['user', 'attachments', 'readBy'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Search messages within a conversation.
     *
     * @param Conversation|int $conversation The conversation instance or its ID.
     * @param string $query The search query.
     * @return Collection Collection of matching messages.
     */
    public static function searchMessages(Conversation|int $conversation, string $query): Collection
    {
        if (is_int($conversation)) {
            $conversation = self::getConversationInstance($conversation);
        }

        return $conversation->messages()
            ->where('body', 'like', "%{$query}%")
            ->with(['user', 'attachments'])
            ->get();
    }

    /**
     * Get unread message count for a user in a conversation.
     *
     * @param Conversation|int $conversation The conversation instance or its ID.
     * @param mixed $user The user instance or its ID.
     * @return int Number of unread messages.
     */
    public static function getUnreadCount(Conversation|int $conversation, mixed $user): int
    {
        if (is_int($conversation)) {
            $conversation = self::getConversationInstance($conversation);
        }

        if (is_int($user)) {
            $user = self::getUserInstance($user);
        }

        $readMessageIds = ReadBy::where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->pluck('message_id');

        return $conversation->messages()
            ->whereNotIn('id', $readMessageIds)
            ->where('user_id', '!=', $user->id)
            ->count();
    }

    /**
     * Get the last message of a conversation.
     *
     * @param Conversation|int $conversation The conversation instance or its ID.
     * @return Message|null The last message or null if no messages.
     */
    public static function getLastMessage(Conversation|int $conversation): ?Message
    {
        if (is_int($conversation)) {
            $conversation = self::getConversationInstance($conversation);
        }

        return $conversation->messages()
            ->with(['user', 'attachments'])
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Get all conversations for a user.
     *
     * @param mixed $user The user instance or its ID.
     * @return Collection Collection of conversations.
     */
    public static function getConversationsForUser(mixed $user): Collection
    {
        if (is_int($user)) {
            $user = self::getUserInstance($user);
        }

        return Conversation::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['users', 'messages' => function ($query) {
            $query->latest()->limit(1);
        }])->get();
    }

    /**
     * Broadcast typing indicator.
     *
     * @param Conversation|int $conversation The conversation instance or its ID.
     * @param mixed $user The user instance or its ID.
     * @param bool $isTyping Whether the user is typing.
     * @return void
     */
    public static function broadcastTyping(Conversation|int $conversation, mixed $user, bool $isTyping = true): void
    {
        if (is_int($conversation)) {
            $conversation = self::getConversationInstance($conversation);
        }

        if (is_int($user)) {
            $user = self::getUserInstance($user);
        }

        event(new UserTyping($conversation, $user, $isTyping));
    }

    /**
     * Broadcast user online status change.
     *
     * @param mixed $user The user instance or its ID.
     * @param bool $isOnline Whether the user is online.
     * @return void
     */
    public static function broadcastOnlineStatus(mixed $user, bool $isOnline): void
    {
        if (is_int($user)) {
            $user = self::getUserInstance($user);
        }

        event(new UserOnlineStatusChanged($user, $isOnline));
    }

    /**
     * Add a reaction to a message.
     *
     * @param Message|int $message The message instance or its ID.
     * @param mixed $user The user instance or its ID.
     * @param string $emoji The emoji reaction.
     * @return Reaction The created reaction.
     */
    public static function addReaction(Message|int $message, mixed $user, string $emoji): Reaction
    {
        if (is_int($message)) {
            $message = self::getMessageInstance($message);
        }

        if (is_int($user)) {
            $user = self::getUserInstance($user);
        }

        return Reaction::firstOrCreate([
            'message_id' => $message->id,
            'user_id' => $user->id,
            'emoji' => $emoji,
        ]);
    }

    /**
     * Remove a reaction from a message.
     *
     * @param Message|int $message The message instance or its ID.
     * @param mixed $user The user instance or its ID.
     * @param string $emoji The emoji reaction to remove.
     * @return bool True if removed.
     */
    public static function removeReaction(Message|int $message, mixed $user, string $emoji): bool
    {
        if (is_int($message)) {
            $message = self::getMessageInstance($message);
        }

        if (is_int($user)) {
            $user = self::getUserInstance($user);
        }

        return Reaction::where([
            'message_id' => $message->id,
            'user_id' => $user->id,
            'emoji' => $emoji,
        ])->delete() > 0;
    }

    /**
     * Get all reactions for a message.
     *
     * @param Message|int $message The message instance or its ID.
     * @return Collection Collection of reactions grouped by emoji.
     */
    public static function getReactions(Message|int $message): Collection
    {
        if (is_int($message)) {
            $message = self::getMessageInstance($message);
        }

        return $message->reactions()
            ->with('user')
            ->get()
            ->groupBy('emoji');
    }
}
