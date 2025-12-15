<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'member_id',
        'channel_database',
        'channel_email',
        'channel_whatsapp',
        'channel_push',
        'categories',
        'quiet_hours_enabled',
        'quiet_hours_start',
        'quiet_hours_end',
        'digest_mode',
        'digest_time',
    ];

    protected $casts = [
        'channel_database' => 'boolean',
        'channel_email' => 'boolean',
        'channel_whatsapp' => 'boolean',
        'channel_push' => 'boolean',
        'categories' => 'array',
        'quiet_hours_enabled' => 'boolean',
        'quiet_hours_start' => 'datetime:H:i',
        'quiet_hours_end' => 'datetime:H:i',
        'digest_time' => 'datetime:H:i',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function getEnabledChannels(): array
    {
        $channels = [];
        if ($this->channel_database) $channels[] = 'database';
        if ($this->channel_email) $channels[] = 'email';
        if ($this->channel_whatsapp) $channels[] = 'whatsapp';
        if ($this->channel_push) $channels[] = 'push';
        return $channels;
    }

    public function isChannelEnabled(string $channel): bool
    {
        return match ($channel) {
            'database' => $this->channel_database,
            'email' => $this->channel_email,
            'whatsapp' => $this->channel_whatsapp,
            'push' => $this->channel_push,
            default => false,
        };
    }

    public function isCategoryEnabled(string $category): bool
    {
        if (!$this->categories) return true;
        return $this->categories[$category]['enabled'] ?? true;
    }

    public function getCategoryChannels(string $category): array
    {
        if (!$this->categories || !isset($this->categories[$category])) {
            return $this->getEnabledChannels();
        }
        return $this->categories[$category]['channels'] ?? $this->getEnabledChannels();
    }

    public function isInQuietHours(): bool
    {
        if (!$this->quiet_hours_enabled) return false;
        
        $now = now()->format('H:i');
        $start = $this->quiet_hours_start?->format('H:i');
        $end = $this->quiet_hours_end?->format('H:i');
        
        if (!$start || !$end) return false;
        
        if ($start <= $end) {
            return $now >= $start && $now <= $end;
        }
        // Overnight quiet hours (e.g., 22:00 - 07:00)
        return $now >= $start || $now <= $end;
    }

    public static function getOrCreateForUser(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'channel_database' => true,
                'channel_email' => true,
                'channel_whatsapp' => false,
                'channel_push' => false,
                'digest_mode' => 'instant',
            ]
        );
    }
}
