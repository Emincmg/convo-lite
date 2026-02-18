<?php

namespace Emincmg\ConvoLite\Exceptions;

use Exception;

class InvalidParticipantException extends Exception
{
    public function __construct(string $message = 'Invalid participant')
    {
        parent::__construct($message);
    }
}
