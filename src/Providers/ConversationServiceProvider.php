<?php

namespace Emincmg\ConvoLite\Providers;

use Emincmg\ConvoLite\Convo;
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
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'convo-lite');

        if ($this->app->runningInConsole()) {
            $this->registerPublishables();
        }
    }

    private function registerPublishables(): void
    {
        // Config
        $this->publishes([
            __DIR__ . '/../../config/convo-lite.php' => config_path('convo-lite.php'),
        ], 'convo-lite-config');

        // Migrations
        $now = now();
        $migrations = [
            'create_conversations_table',
            'create_conversation_user_table',
            'create_messages_table',
            'create_attachments_table',
            'create_convo_read_by_table',
            'create_convo_reactions_table',
        ];

        foreach ($migrations as $migration) {
            $this->publishes([
                __DIR__ . "/../../migrations/{$migration}.php" =>
                    $this->app->databasePath('migrations/' . $now->format('Y_m_d_His') . "_{$migration}.php"),
            ], 'convo-lite-migrations');
            $now->addSecond();
        }

        // Language files
        $this->publishes([
            __DIR__ . '/../../lang' => $this->app->langPath('vendor/convo-lite'),
        ], 'convo-lite-lang');

        // Vue components
        $this->publishes([
            __DIR__ . '/../../resources/js/components/ConvoLite' => resource_path('js/components/ConvoLite'),
        ], 'convo-lite-vue');

        // Composables
        $this->publishes([
            __DIR__ . '/../../resources/js/composables' => resource_path('js/composables'),
        ], 'convo-lite-vue');

        // Entry point
        $this->publishes([
            __DIR__ . '/../../resources/js/convo-lite.js' => resource_path('js/convo-lite.js'),
        ], 'convo-lite-vue');

        // Routes (for customization)
        $this->publishes([
            __DIR__ . '/../../routes/api.php' => base_path('routes/convo-lite.php'),
        ], 'convo-lite-routes');
    }
}
