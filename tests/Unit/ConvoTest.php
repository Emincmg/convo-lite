<?php

namespace Emincmg\ConvoLite\Tests\Unit;

use Emincmg\ConvoLite\Convo;
use Emincmg\ConvoLite\Exceptions\ConversationNotFoundException;
use Emincmg\ConvoLite\Exceptions\UserNotFoundException;
use Emincmg\ConvoLite\Exceptions\InvalidParticipantException;
use Emincmg\ConvoLite\Models\Conversation;
use Emincmg\ConvoLite\Tests\TestCase;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConvoTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser(array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => 'Test User',
            'email' => 'test' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ], $attributes));
    }

    public function test_can_create_conversation(): void
    {
        $creator = $this->createUser();
        $receiver = $this->createUser();

        $conversations = Convo::createConversation($creator->id, $receiver->id);

        $this->assertCount(1, $conversations);
        $this->assertInstanceOf(Conversation::class, $conversations->first());
    }

    public function test_can_create_conversation_with_title(): void
    {
        $creator = $this->createUser();
        $receiver = $this->createUser();

        $conversations = Convo::createConversation($creator->id, $receiver->id, 'Test Title');

        $this->assertEquals('Test Title', $conversations->first()->title);
    }

    public function test_throws_exception_when_creator_not_found(): void
    {
        $this->expectException(UserNotFoundException::class);

        Convo::createConversation(9999, 1);
    }

    public function test_throws_exception_when_creator_is_receiver(): void
    {
        $user = $this->createUser();

        $this->expectException(InvalidParticipantException::class);

        Convo::createConversation($user->id, $user->id);
    }

    public function test_can_send_message(): void
    {
        $creator = $this->createUser();
        $receiver = $this->createUser();

        $conversations = Convo::createConversation($creator->id, $receiver->id);
        $conversation = $conversations->first();

        $message = Convo::sendMessage($conversation, $creator, 'Hello!');

        $this->assertEquals('Hello!', $message->body);
        $this->assertEquals($creator->id, $message->user_id);
    }

    public function test_can_add_participators(): void
    {
        $creator = $this->createUser();
        $receiver = $this->createUser();
        $newUser = $this->createUser();

        $conversations = Convo::createConversation($creator->id, $receiver->id);
        $conversation = $conversations->first();

        Convo::addParticipators($conversation, $newUser->id);

        $this->assertCount(3, $conversation->fresh()->users);
    }

    public function test_can_remove_participators(): void
    {
        $creator = $this->createUser();
        $receiver = $this->createUser();

        $conversations = Convo::createConversation($creator->id, $receiver->id);
        $conversation = $conversations->first();

        Convo::removeParticipators($conversation, $receiver->id);

        $this->assertCount(1, $conversation->fresh()->users);
    }

    public function test_can_get_conversation_by_id(): void
    {
        $creator = $this->createUser();
        $receiver = $this->createUser();

        $conversations = Convo::createConversation($creator->id, $receiver->id);
        $conversation = $conversations->first();

        $found = Convo::getConversationById($conversation->id);

        $this->assertEquals($conversation->id, $found->id);
    }

    public function test_throws_exception_when_conversation_not_found(): void
    {
        $this->expectException(ConversationNotFoundException::class);

        Convo::getConversationById(9999);
    }

    public function test_can_mark_message_as_read(): void
    {
        $creator = $this->createUser();
        $receiver = $this->createUser();

        $conversations = Convo::createConversation($creator->id, $receiver->id);
        $conversation = $conversations->first();

        $message = Convo::sendMessage($conversation, $creator, 'Hello!');
        $readBy = Convo::markMessageAsRead($message, $receiver);

        $this->assertEquals($receiver->id, $readBy->user_id);
        $this->assertEquals($message->id, $readBy->message_id);
    }
}
