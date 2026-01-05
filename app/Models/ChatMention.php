<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMention extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['message_id', 'user_id', 'is_read', 'created_at'];
    
    protected $casts = ['is_read' => 'boolean'];

    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
