<?php

namespace Emincmg\ConvoLite\Models;

use Emincmg\ConvoLite\Traits\Message\AttachesFiles;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use AttachesFiles;

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
        'read_by_user_id',
    ];

    /**
     * Get the attributes that should be cast.
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'read_by_user_id' => 'array',
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

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
