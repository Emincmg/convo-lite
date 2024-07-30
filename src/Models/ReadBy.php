<?php

namespace Emincmg\ConvoLite\Models;

use Illuminate\Database\Eloquent\Model;

class ReadBy extends Model
{
    protected $table = 'convo_read_by';

    protected $fillable = [
        'conversation_id',
        'attachment_id',
        'message_id',
        'user_id',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function attachment()
    {
        return $this->belongsTo(Attachment::class);
    }

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function user()
    {
        return $this->belongsTo(config('convo-lite.user_model'));
    }
}
