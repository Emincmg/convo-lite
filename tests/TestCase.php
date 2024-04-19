<?php

namespace Emincmg\ConvoLite\Tests;

use Emincmg\ConvoLite\Providers\ConversationServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->turnStubsToMigrations();


        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        $this->turnMigrationsToStubs();

    }

    protected function getPackageProviders($app)
    {
        return [
            ConversationServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

    }

    protected function turnStubsToMigrations(): void
    {
        $migrationsFolder = __DIR__ . '/../migrations/';
        $migrations = scandir($migrationsFolder);
        foreach ($migrations as $migration) {
            if ($migration === '.' || $migration === '..') continue;

            $newName = str_replace('.php.stub', '.php', $migration);

            rename($migrationsFolder . $migration, $migrationsFolder . $newName);
        }
    }

    protected function turnMigrationsToStubs(): void
    {
        $migrationsFolder = __DIR__ . '/../migrations/';
        $migrations = scandir($migrationsFolder);
        foreach ($migrations as $migration) {
            if ($migration === '.' || $migration === '..') continue;

            $newName = str_replace('.php', '.php.stub', $migration);

            rename($migrationsFolder . $migration, $migrationsFolder . $newName);
        }
    }

}
