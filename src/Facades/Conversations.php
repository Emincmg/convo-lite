<?php

namespace Emincmg\ConvoLite\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method create(int|array $userId, int|array $receiverId, string $title)
 */
class Conversations extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'conversations';
    }
}
