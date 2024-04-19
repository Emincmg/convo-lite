<?php

namespace Emincmg\ConvoLite\Providers;

use Emincmg\ConvoLite\Conversations;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class ConversationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('conversations', "Emincmg\\ConvoLite\\Conversations");

        $this->app->singleton(Conversations::class, function (Application $app) {
            return new Conversations();
        });

//        $this->mergeConfigFrom(
//            base_path().'/config/convo-lite.php','convo-lite'
//        );
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__) . '/../migrations/';
        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateConversationsTable')){
                $this->publishes([
                    __DIR__.'/../database/migrations/create_conversations_table.php.stub' =>
                        database_path('migrations/' . date('Y_m_d_His', time()) . '_create_conversations_table.php'),
                ],'migrations');
            }
            $this->publishes([
                __DIR__.'/../config/convo-lite.php' => config_path('convo-lite.php'),
            ], 'config');
        }
    }
}
