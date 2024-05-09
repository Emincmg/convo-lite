<?php

namespace Emincmg\ConvoLite\Tests;

use Emincmg\ConvoLite\Providers\ConversationServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--database' => 'testbench']);
    }

    protected function getPackageProviders($app)
    {
        return [
            ConversationServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return ['Conversations'=>'ConvoLite\Facades'];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('convo-lite', require __DIR__.'/../config/convo-lite.php');
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

    }

}
