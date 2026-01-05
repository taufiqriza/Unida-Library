<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'chat_room_id', 'sender_id', 'message', 'attachment', 
        'attachment_type', 'attachment_name', 'type', 'is_deleted', 'task_id', 'book_id',
        'voice_path', 'voice_duration', 'reply_to_id'
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
        'voice_duration' => 'integer',
    ];

    // Relationships
    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function replyTo()
    {
        return $this->belongsTo(ChatMessage::class, 'reply_to_id');
    }

    public function reads()
    {
        return $this->hasMany(ChatMessageRead::class, 'message_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    // Helpers
    public function isFromUser(int $userId): bool
    {
        return $this->sender_id === $userId;
    }

    public function isSystem(): bool
    {
        return $this->type === 'system';
    }

    public function hasAttachment(): bool
    {
        return !empty($this->attachment);
    }

    public function isImage(): bool
    {
        return $this->attachment_type === 'image';
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment ? asset('storage/' . $this->attachment) : null;
    }

    public function getTimeAgo(): string
    {
        $diff = $this->created_at->diffForHumans();
        return str_replace(['dari sekarang', 'yang lalu'], ['', 'lalu'], $diff);
    }

    public function getFormattedTime(): string
    {
        if ($this->created_at->isToday()) {
            return $this->created_at->format('H:i');
        }
        if ($this->created_at->isYesterday()) {
            return 'Kemarin ' . $this->created_at->format('H:i');
        }
        if ($this->created_at->isCurrentYear()) {
            return $this->created_at->format('d M H:i');
        }
        return $this->created_at->format('d M Y H:i');
    }

    /**
     * Soft delete message
     */
    public function softDelete(): void
    {
        $this->update(['is_deleted' => true]);
    }
}
