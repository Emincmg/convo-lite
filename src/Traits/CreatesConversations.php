<?php

namespace Emincmg\ConvoLite\Traits;

use Emincmg\ConvoLite\Http\Controllers\Models\Conversation;
use Illuminate\Http\Request;

trait CreatesConversations
{
    /**
     * Store the conversation request and return the stored conversation's id.
     * @param Request $request
     * @return integer
     */
    public function store(Request $request): int
    {
        $conversation = Conversation::create([
            'title' => $request['title'],
        ]);
        $user = User::find($request['user_id']);
        $receiver = User::find($request['receiver_id']);
        $request->merge(['conversation_id' => $conversation->id, 'sender_name' => $user['first_name'] . ' ' . $user['last_name']]);

        $this->update($request);
        $user->conversations()->save($conversation);
        $receiver->conversations()->save($conversation);

        return $conversation['id'];
    }
}
