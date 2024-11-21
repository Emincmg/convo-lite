<?php

namespace Emincmg\ConvoLite\Models;

use Emincmg\ConvoLite\Traits\Message\AttachesFiles;
use Emincmg\ConvoLite\Traits\Relationships\HasReadBy;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use AttachesFiles, HasReadBy;

    /**
     * Get the attributes that can be assigned.
     * @var string[]
     */
    protected $fillable=[
        'body',
        'user_id',
        'conversation_id',
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

    public function receivers()
    {
        return $this->conversation->users()->where('id','!=',$this->user_id)->get();
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('withReadBy', function ($query) {
            $query->with(['readBy']);
        });
    }

    /**
     * Get the data to broadcast for the model.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(string $event): array
    {
        $this->load('readBy');
        return $this->toArray();
    }
}
