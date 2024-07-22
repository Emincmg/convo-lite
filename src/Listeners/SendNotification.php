<?php

namespace Emincmg\ConvoLite\Listeners;

use Emincmg\ConvoLite\Events\MessageSent;
use Emincmg\ConvoLite\Notifications\NewMessageReceived;

class SendNotification
{
    public function handle(MessageSent $event)
    {
        if (config('convo-lite.send_message_notifications')){
            $user = $event->message->user();
            $user->notify(new NewMessageReceived());
        }
    }
}
