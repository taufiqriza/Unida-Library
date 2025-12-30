<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    protected $fillable = [
        'type', 'name', 'description', 'icon', 'color', 'branch_id', 'is_archived',
        'member_id', 'topic', 'status', 'last_staff_id'
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function member()
    {
        return $this->belongsTo(\App\Models\Member::class, 'member_id');
    }

    public function lastStaff()
    {
        return $this->belongsTo(User::class, 'last_staff_id');
    }

    public function members()
    {
        return $this->hasMany(ChatRoomMember::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_room_members')
            ->withPivot('role', 'is_muted', 'last_read_at')
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class)->where('is_deleted', false);
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class)->where('is_deleted', false)->latest();
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('members', fn($q) => $q->where('user_id', $userId));
    }

    public function scopeGroups($query)
    {
        return $query->whereIn('type', ['branch', 'global']);
    }

    public function scopeDirect($query)
    {
        return $query->where('type', 'direct');
    }

    // Helpers
    public function isGlobal(): bool
    {
        return $this->type === 'global';
    }

    public function isBranch(): bool
    {
        return $this->type === 'branch';
    }

    public function isDirect(): bool
    {
        return $this->type === 'direct';
    }

    public function isGroup(): bool
    {
        return in_array($this->type, ['branch', 'global']);
    }

    /**
     * Get display name - for direct chats, show other user's name
     */
    public function getDisplayNameFor(int $userId): string
    {
        if ($this->isDirect()) {
            $otherUser = $this->users()->where('users.id', '!=', $userId)->first();
            return $otherUser?->name ?? 'Unknown';
        }
        return $this->name ?? 'Group';
    }

    /**
     * Get other user in direct chat (with branch eager loaded)
     */
    public function getOtherUser(int $userId): ?User
    {
        if (!$this->isDirect()) return null;
        return $this->users()
            ->where('users.id', '!=', $userId)
            ->with('branch:id,name')
            ->first();
    }

    public function getIconClass(): string
    {
        if ($this->icon) return $this->icon;
        
        return match($this->type) {
            'global' => 'fa-globe',
            'branch' => 'fa-building',
            default => 'fa-user',
        };
    }

    public function getColorClass(): string
    {
        if ($this->color) return $this->color;
        
        return match($this->type) {
            'global' => 'bg-gradient-to-br from-blue-500 to-indigo-600',
            'branch' => 'bg-gradient-to-br from-green-500 to-emerald-600',
            default => 'bg-gradient-to-br from-gray-400 to-gray-500',
        };
    }

    /**
     * Get unread count for user
     */
    public function getUnreadCountFor(int $userId): int
    {
        $member = $this->members()->where('user_id', $userId)->first();
        
        $query = $this->messages()->where('sender_id', '!=', $userId);
        
        if ($member && $member->last_read_at) {
            $query->where('created_at', '>', $member->last_read_at);
        }
        
        return $query->count();
    }

    /**
     * Check if user is member
     */
    public function hasMember(int $userId): bool
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(int $userId): bool
    {
        return $this->members()->where('user_id', $userId)->where('role', 'admin')->exists();
    }
}
