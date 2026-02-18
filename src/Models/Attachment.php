<?php

namespace Emincmg\ConvoLite\Models;

use Emincmg\ConvoLite\Traits\Relationships\HasReadBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use HasReadBy, SoftDeletes;

    protected $fillable = [
        'name',
        'user_id',
        'conversation_id',
        'message_id',
        'full_path',
        'storage_path',
        'public_path',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(config('convo-lite.user_model'));
    }

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
