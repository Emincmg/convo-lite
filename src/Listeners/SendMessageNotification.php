<?php

namespace Emincmg\ConvoLite\Listeners;

use Emincmg\ConvoLite\Events\MessageSent;
use Emincmg\ConvoLite\Notifications\NewMessageReceived;

class SendMessageNotification
{
    public function handle(MessageSent $event)
    {
        if (config('convo-lite.send_message_notifications')){
            $receivers = $event->message->receivers();
            foreach ($receivers as $receiver){
                $receiver->notify(new NewMessageReceived($event->message));
            }
        }
    }
}
