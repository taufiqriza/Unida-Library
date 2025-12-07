<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fine extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'loan_id', 'member_id', 'branch_id', 'amount', 'paid_amount', 'description', 'is_paid'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'is_paid' => 'boolean',
    ];

    public function loan(): BelongsTo { return $this->belongsTo(Loan::class); }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }

    public function getRemainingAttribute(): float
    {
        return $this->amount - $this->paid_amount;
    }
}
