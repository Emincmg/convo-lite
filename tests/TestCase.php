<?php

namespace Emincmg\ConvoLite\Tests;

use Emincmg\ConvoLite\Providers\ConversationServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            ConversationServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('convo-lite.user_model', \Illuminate\Foundation\Auth\User::class);
    }
}
