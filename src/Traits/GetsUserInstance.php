<?php
namespace Emincmg\ConvoLite\Traits;

use Emincmg\ConvoLite\Http\Controllers\Models\Conversation;

trait GetsUserInstance
{
    /**
     * Retrieve the user instance based on the provided user ID.
     *
     * @param int $userId The ID of the user.
     * @return User|null The user instance if found, or null if not found.
     */
    private static function getUserInstance(int $userId): ?User
    {
        return resolve(config('convo-lite.user_model'))->find($userId);
    }
}
