<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessageRead extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['message_id', 'user_id', 'read_at'];
    
    protected $casts = ['read_at' => 'datetime'];

    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
