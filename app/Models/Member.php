<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'branch_id', 'member_id', 'name', 'gender', 'birth_date', 'identity_number',
        'address', 'city', 'phone', 'email', 'member_type_id', 'register_date',
        'expire_date', 'photo', 'is_active', 'notes'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'register_date' => 'date',
        'expire_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function memberType(): BelongsTo { return $this->belongsTo(MemberType::class); }
    public function loans(): HasMany { return $this->hasMany(Loan::class); }
    public function fines(): HasMany { return $this->hasMany(Fine::class); }

    public function isExpired(): bool
    {
        return $this->expire_date < now();
    }

    public function getActiveLoansCountAttribute(): int
    {
        return $this->loans()->where('is_returned', false)->count();
    }
}
