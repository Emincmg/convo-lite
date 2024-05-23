<?php

namespace Emincmg\ConvoLite\Tests\Unit;

use Emincmg\ConvoLite\Facades\Conversation;
use Emincmg\ConvoLite\Tests\TestCase;

class CreateConversationTest extends TestCase
{
    public function test_create_conversation()
    {
        Conversation::create(1,'2','Conversation creation test.');
    }
}
