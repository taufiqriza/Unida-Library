<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanRenewal extends Model
{
    protected $fillable = [
        'loan_id', 'member_id', 'old_due_date', 'new_due_date', 
        'renewal_number', 'source', 'processed_by',
    ];

    protected $casts = [
        'old_due_date' => 'date',
        'new_due_date' => 'date',
    ];

    public function loan(): BelongsTo { return $this->belongsTo(Loan::class); }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function processor(): BelongsTo { return $this->belongsTo(User::class, 'processed_by'); }
}
