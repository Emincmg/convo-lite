<?php

namespace Emincmg\ConvoLite\Providers;

use Emincmg\ConvoLite\Events\MessageSent;
use Emincmg\ConvoLite\Listeners\SendMessageNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        MessageSent::class=>[
            SendMessageNotification::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
