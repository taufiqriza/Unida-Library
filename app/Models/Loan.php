<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'branch_id', 'member_id', 'item_id', 'loan_date', 'due_date',
        'return_date', 'is_returned', 'extend_count'
    ];

    protected $casts = [
        'loan_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'is_returned' => 'boolean',
    ];

    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function item(): BelongsTo { return $this->belongsTo(Item::class); }
    public function fines(): HasMany { return $this->hasMany(Fine::class); }

    public function isOverdue(): bool
    {
        return !$this->is_returned && $this->due_date < now();
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->isOverdue()) return 0;
        return now()->diffInDays($this->due_date);
    }
}
