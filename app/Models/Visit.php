<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    protected $fillable = [
        'branch_id', 'member_id', 'guest_name', 'guest_institution',
        'visitor_type', 'purpose', 'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function getVisitorNameAttribute(): string
    {
        return $this->visitor_type === 'member' 
            ? ($this->member?->name ?? 'Unknown') 
            : $this->guest_name;
    }
}
