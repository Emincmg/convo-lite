<?php

namespace Emincmg\ConvoLite\Tests;

use Emincmg\ConvoLite\Facades\Conversations;
use Emincmg\ConvoLite\Providers\ConversationServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
    }

    protected function getPackageProviders($app)
    {
        return [
            ConversationServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }

    public function test_can_create_conversation()
    {

        Conversations::create(1,2,'Selam');
    }
}
