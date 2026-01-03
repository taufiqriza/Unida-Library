<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'branch_id', 'member_id', 'book_id', 'item_id', 'status', 'queue_position',
        'notified_at', 'ready_at', 'pickup_deadline', 'fulfilled_at', 'cancelled_at', 'cancel_reason',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
        'ready_at' => 'datetime',
        'pickup_deadline' => 'datetime',
        'fulfilled_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function book(): BelongsTo { return $this->belongsTo(Book::class); }
    public function item(): BelongsTo { return $this->belongsTo(Item::class); }

    public function scopeActive($query) { return $query->whereIn('status', ['pending', 'ready']); }
    public function scopePending($query) { return $query->where('status', 'pending'); }
    public function scopeReady($query) { return $query->where('status', 'ready'); }

    public function isPending(): bool { return $this->status === 'pending'; }
    public function isReady(): bool { return $this->status === 'ready'; }
    public function isExpired(): bool { return $this->pickup_deadline && now()->gt($this->pickup_deadline); }

    public function markAsReady(Item $item, int $pickupDays = 3): void
    {
        $this->update([
            'status' => 'ready',
            'item_id' => $item->id,
            'ready_at' => now(),
            'pickup_deadline' => now()->addDays($pickupDays),
        ]);
    }

    public function fulfill(): void
    {
        $this->update(['status' => 'fulfilled', 'fulfilled_at' => now()]);
    }

    public function cancel(string $reason = null): void
    {
        $this->update(['status' => 'cancelled', 'cancelled_at' => now(), 'cancel_reason' => $reason]);
    }

    public function expire(): void
    {
        $this->update(['status' => 'expired', 'cancelled_at' => now(), 'cancel_reason' => 'Batas waktu pengambilan terlewat']);
    }
}
