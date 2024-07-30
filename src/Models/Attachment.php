<?php

namespace Emincmg\ConvoLite\Models;

use Emincmg\ConvoLite\Traits\Message\AttachesFiles;
use Emincmg\ConvoLite\Traits\Relationships\HasReadBy;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use AttachesFiles, HasReadBy;

    /**
     * Get the attributes that can be assigned.
     * @var string[]
     */
    protected $fillable=[
        'name',
        'user_id',
        'conversation_id',
        'message_id',
        'name',
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
