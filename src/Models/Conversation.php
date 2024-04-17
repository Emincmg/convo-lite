<?php

namespace Emincmg\ConvoLite\Models;

use Emincmg\ConvoLite\Traits\SendsMessage;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use SendsMessage;

    protected $fillable=
        [
            'title',
            'status'
        ];

    public function users()
    {
        return $this->belongsToMany(config('convo-lite.user_model'));
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
