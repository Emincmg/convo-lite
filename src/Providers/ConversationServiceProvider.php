<?php

namespace Emincmg\ConvoLite\Providers;

use Emincmg\ConvoLite\Models\Conversation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class ConversationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Conversation::class, function (Application $app) {
            return new Conversation();
        });
        $this->mergeConfigFrom(
            __DIR__.'/config/convo-lite.php','convo-lite'
        );
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__.'/../config/convo-lite.php' => config_path('convo-lite.php'),
            ], 'config');

        }
    }
}
