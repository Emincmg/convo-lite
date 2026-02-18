<?php

namespace Emincmg\ConvoLite\Models;

use Emincmg\ConvoLite\Traits\Message\AttachesFiles;
use Emincmg\ConvoLite\Traits\Relationships\HasReadBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use AttachesFiles, HasReadBy, SoftDeletes;

    /**
     * Get the attributes that can be assigned.
     * @var string[]
     */
    protected $fillable = [
        'body',
        'user_id',
        'conversation_id',
        'reply_to_id',
    ];


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

    public function replyTo()
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'reply_to_id');
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function receivers()
    {
        return $this->conversation->users()->where('id', '!=', $this->user_id)->get();
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('withReadBy', function ($query) {
            $query->with(['readBy']);
        });
    }
}
