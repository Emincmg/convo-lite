<?php

namespace Emincmg\ConvoLite\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Conversation
 *
 * @package Symfony\App\Facade
 *
 * @method ConversationQuery create()
 */
class Convo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'convo';
    }
}
