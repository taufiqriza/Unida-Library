<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StaffNotification extends Model
{
    use HasUuids;

    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'category',
        'priority',
        'title',
        'body',
        'action_url',
        'action_label',
        'icon',
        'color',
        'image_url',
        'data',
        'read_at',
        'clicked_at',
        'dismissed_at',
        'channels_sent',
        'channels_delivered',
        'channels_failed',
    ];

    protected $casts = [
        'data' => 'array',
        'channels_sent' => 'array',
        'channels_delivered' => 'array',
        'channels_failed' => 'array',
        'read_at' => 'datetime',
        'clicked_at' => 'datetime',
        'dismissed_at' => 'datetime',
    ];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function markAsClicked(): void
    {
        $this->update([
            'read_at' => $this->read_at ?? now(),
            'clicked_at' => now(),
        ]);
    }

    public function markAsDismissed(): void
    {
        $this->update(['dismissed_at' => now()]);
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('notifiable_type', User::class)
                     ->where('notifiable_id', $userId);
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Helpers for display
    public function getIconClass(): string
    {
        $icons = [
            'chat' => 'fa-comments',
            'loan' => 'fa-book-reader',
            'task' => 'fa-clipboard-list',
            'member' => 'fa-user-check',
            'system' => 'fa-cog',
            'announcement' => 'fa-bullhorn',
        ];
        
        return $this->icon ?? ($icons[$this->category] ?? 'fa-bell');
    }

    public function getColorClass(): string
    {
        $colors = [
            'low' => 'bg-gray-100 text-gray-600',
            'normal' => 'bg-blue-100 text-blue-600',
            'high' => 'bg-orange-100 text-orange-600',
            'urgent' => 'bg-red-100 text-red-600',
        ];
        
        return $this->color ?? ($colors[$this->priority] ?? 'bg-blue-100 text-blue-600');
    }

    public function getCategoryLabel(): string
    {
        $labels = [
            'chat' => 'Chat',
            'loan' => 'Sirkulasi',
            'task' => 'Tugas',
            'member' => 'Anggota',
            'system' => 'Sistem',
            'announcement' => 'Pengumuman',
        ];
        
        return $labels[$this->category] ?? 'Notifikasi';
    }

    public function getTimeAgo(): string
    {
        return $this->created_at->locale('id')->diffForHumans();
    }
}
