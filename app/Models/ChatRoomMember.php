<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoomMember extends Model
{
    protected $fillable = [
        'chat_room_id', 'user_id', 'role', 'is_muted', 'joined_at', 'last_read_at', 'cleared_at'
    ];

    protected $casts = [
        'is_muted' => 'boolean',
        'joined_at' => 'datetime',
        'last_read_at' => 'datetime',
        'cleared_at' => 'datetime',
    ];

    // Relationships
    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helpers
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function markAsRead(): void
    {
        $this->update(['last_read_at' => now()]);
    }

    public function toggleMute(): void
    {
        $this->update(['is_muted' => !$this->is_muted]);
    }
}
