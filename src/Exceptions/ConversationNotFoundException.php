<?php

namespace Emincmg\ConvoLite\Exceptions;

use Exception;

class ConversationNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("Conversation not found with ID: $id");
    }
}
