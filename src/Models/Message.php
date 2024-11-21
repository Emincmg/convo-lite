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

    public function broadcastWith()
    {
        $this->message->load(['readBy', 'conversation']);
        return [
            'created_at'=>$this->message->created_at,
            'body' => $this->message->body,
            'read_by'=>$this->message->readBy->map(function ($user) {
                return $user->name;
            }),
            'conversation'=>[
                'id'=>$this->message->conversation->id,
                'title'=>$this->message->conversation->title,
            ]
        ];
    }
}
