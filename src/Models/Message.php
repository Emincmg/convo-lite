<?php

namespace Emincmg\ConvoLite\Models;

use Emincmg\ConvoLite\Traits\Message\AttachesFiles;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use AttachesFiles;

    /**
     * Get the attributes that can be assigned.
     * @var string[]
     */
    protected $fillable=[
        'body',
        'user_id',
        'conversation_id',
        'files',
        'sender_name',
        'read_by_id',
    ];

    /**
     * Get the attributes that should be cast.
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'read_by_id' => 'array',
        ];
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(config('convo-lite.user_model'));
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
