<?php

namespace Emincmg\ConvoLite\Exceptions;

use Exception;

class UserNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("User not found with ID: $id");
    }
}
