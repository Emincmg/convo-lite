<?php

namespace Emincmg\ConvoLite\Providers;

use Emincmg\ConvoLite\Commands\PublishMigrationsCommand;
use Emincmg\ConvoLite\Convo;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class ConversationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->singleton('convo', function () {
            return new Convo();
        });
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/convo-lite.php', 'convo-lite'
        );
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/convo-lite.php' => config_path('convo-lite.php'),
            ], 'config');

            $now = now();
            $this->publishes([
                __DIR__ . '/../../migrations/create_conversations_table.php' =>
                    $this->app->databasePath('migrations' .
                        DIRECTORY_SEPARATOR . $now->format('Y_m_d_His') . '_create_conversations_table.php'),
            ], 'migrations');

            $now->addSecond();
            $this->publishes([
                __DIR__ . '/../../migrations/create_conversation_user_table.php' =>
                    $this->app->databasePath('migrations' .
                        DIRECTORY_SEPARATOR . $now->format('Y_m_d_His') . '_create_conversation_user_table.php'),
            ], 'migrations');

            $now->addSecond();
            $this->publishes([
                __DIR__ . '/../../migrations/create_messages_table.php' =>
                    $this->app->databasePath('migrations' .
                        DIRECTORY_SEPARATOR . $now->format('Y_m_d_His') . '_create_messages_table.php'),], 'migrations');

            $now->addSecond();
            $this->publishes([
                __DIR__ . '/../../migrations/create_attachments_table.php' =>
                    $this->app->databasePath('migrations' .
                        DIRECTORY_SEPARATOR . $now->format('Y_m_d_His') . '_create_attachments_table.php'),], 'migrations');

            $now->addSecond();
            $this->publishes([
                __DIR__ . '/../../migrations/create_convo_read_by_table.php' =>
                    $this->app->databasePath('migrations' .
                        DIRECTORY_SEPARATOR . $now->format('Y_m_d_His') . '_create_convo_read_by_table.php'),], 'migrations');

            $this->loadTranslationsFrom(__DIR__ . '../../lang', 'convo-lite');

            $this->publishes([
                __DIR__ . '/../../lang' => $this->app->langPath('vendor/convo-lite'),
            ]);
        }
    }
}
