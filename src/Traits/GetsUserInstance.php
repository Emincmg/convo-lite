<?php

namespace Emincmg\ConvoLite\Traits;

use Emincmg\ConvoLite\Exceptions\UserNotFoundException;

trait GetsUserInstance
{
    /**
     * Retrieve the user instance based on the provided ID.
     *
     * @param int $userId The ID of the user.
     * @return mixed The user instance.
     * @throws UserNotFoundException If no user is found with the given ID.
     */
    private static function getUserInstance(int $userId)
    {
        $user = resolve(config('convo-lite.user_model'))->find($userId);

        if (!$user) {
            throw new UserNotFoundException($userId);
        }

        return $user;
    }
}
