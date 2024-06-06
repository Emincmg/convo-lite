<?php

namespace Emincmg\ConvoLite\Models;

use Emincmg\ConvoLite\Traits\Conversation\SetsStatus;
use Emincmg\ConvoLite\Traits\Conversation\SetsTitle;
use Emincmg\ConvoLite\Traits\Message\SendsMessage;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use SendsMessage, SetsTitle, SetsStatus;

    protected $fillable=
        [
            'title',
            'status',
            'has_new_message'
        ];

    public function users()
    {
        return $this->belongsToMany(config('convo-lite.user_model'));
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
