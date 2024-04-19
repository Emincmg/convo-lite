<?php

namespace Emincmg\ConvoLite\Tests\Unit;

use Emincmg\ConvoLite\Facades\Conversations;
use Emincmg\ConvoLite\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateConversationTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_conversation()
    {
        Conversations::create(1,'2','Selamm');
    }
}
