<?php

namespace Emincmg\ConvoLite\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Conversation
 *
 * @package Symfony\App\Facade
 *
 * @method static \Illuminate\Support\Collection createConversation(int $creatorId, array|int $receiverIds, ?string $title = null)
 * @method static void addParticipators(\Conversation|int $conversation, int|array $users)
 * @method static void removeParticipators(\Conversation|int $conversation, int|array $users)
 * @method static void markConversationStatus(\Conversation|int $conversation, string $param)
 * @method static \Conversation getConversationById(int $id)
 * @method static \Illuminate\Support\Collection getConversationAttachments(\Conversation|int $conversation)
 * @method static \Conversation|null getConversationByTitle(string $title)
 * @method static \Illuminate\Support\Collection getMessagesByConversation(\Conversation $conversation)
 * @method static \Message sendMessage(\Conversation|int $conversation, mixed $user, string $messageContent, \Illuminate\Http\UploadedFile|array|null $file = null)
 */
class Convo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'convo';
    }
}
