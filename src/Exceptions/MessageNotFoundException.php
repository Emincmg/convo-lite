<?php

namespace Emincmg\ConvoLite\Exceptions;

use Exception;

class MessageNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("Message not found with ID: $id");
    }
}
