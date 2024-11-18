<?php
namespace Emincmg\ConvoLite\Traits;

use Emincmg\ConvoLite\Http\Controllers\Models\Conversation;

trait GetsUserInstance
{
    private static function getUserInstance(int $userId)
    {
        return resolve(config('convo-lite.user_model'))->find($userId);
    }
}
