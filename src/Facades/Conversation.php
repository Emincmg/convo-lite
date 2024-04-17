<?php

namespace Emincmg\ConvoLite\Facades;

use Illuminate\Support\Facades\Facade;

class Conversation extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'conversation';
    }
}
