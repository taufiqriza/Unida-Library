<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class FinePayment extends Model
{
    protected $fillable = [
        'payment_code', 'member_id', 'branch_id', 'amount', 'payment_method', 'status',
        'external_id', 'payment_url', 'fine_ids', 'payment_data', 'paid_at', 'expired_at',
        'processed_by', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fine_ids' => 'array',
        'payment_data' => 'array',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->payment_code = $model->payment_code ?? 'PAY-' . strtoupper(Str::random(8));
        });
    }

    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function processor(): BelongsTo { return $this->belongsTo(User::class, 'processed_by'); }

    public function fines() { return Fine::whereIn('id', $this->fine_ids ?? [])->get(); }

    public function isPending(): bool { return $this->status === 'pending'; }
    public function isPaid(): bool { return $this->status === 'paid'; }
    public function isExpired(): bool { return $this->status === 'expired' || ($this->expired_at && now()->gt($this->expired_at)); }

    public function markAsPaid(string $method = null, array $data = []): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $method ?? $this->payment_method,
            'payment_data' => array_merge($this->payment_data ?? [], $data),
        ]);
        Fine::whereIn('id', $this->fine_ids)->update(['is_paid' => true, 'paid_at' => now()]);
    }
}
