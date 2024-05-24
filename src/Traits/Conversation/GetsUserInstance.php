<?php
namespace Emincmg\ConvoLite\Traits\Conversation;

use Emincmg\ConvoLite\Http\Controllers\Models\Conversation;

trait GetsUserInstance
{
    /**
     * Retrieve the user instance based on the provided user ID.
     *
     * @param int $userId The ID of the user.
     * @return User|null The user instance if found, or null if not found.
     */
    private function getUserInstance(int $userId)
    {
        return config('convo-lite.user_model')::find($userId);
    }
}
