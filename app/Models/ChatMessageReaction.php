<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessageReaction extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['message_id', 'user_id', 'emoji', 'created_at'];

    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
