<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Member extends Authenticatable
{
    use BelongsToBranch, HasApiTokens;

    protected $fillable = [
        'branch_id', 'faculty_id', 'department_id', 'member_id', 'name', 'gender', 'birth_date', 
        'address', 'city', 'phone', 'email', 'password', 'member_type_id', 'register_date',
        'expire_date', 'photo', 'is_active', 'profile_completed', 'notes'
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'birth_date' => 'date',
        'register_date' => 'date',
        'expire_date' => 'date',
        'is_active' => 'boolean',
        'profile_completed' => 'boolean',
        'password' => 'hashed',
    ];

    public function memberType(): BelongsTo { return $this->belongsTo(MemberType::class); }
    public function faculty(): BelongsTo { return $this->belongsTo(Faculty::class); }
    public function department(): BelongsTo { return $this->belongsTo(Department::class); }
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

    public function thesisSubmissions(): HasMany
    {
        return $this->hasMany(ThesisSubmission::class);
    }

    public function plagiarismChecks(): HasMany
    {
        return $this->hasMany(PlagiarismCheck::class);
    }

    public function hasOutstandingLoans(): bool
    {
        return $this->loans()->where('is_returned', false)->exists();
    }

    public function hasOverdueLoans(): bool
    {
        return $this->loans()
            ->where('is_returned', false)
            ->where('due_date', '<', now())
            ->exists();
    }

    public function hasUnpaidFines(): bool
    {
        return $this->fines()->where('is_paid', false)->exists();
    }

    public function canRequestClearanceLetter(): bool
    {
        return !$this->hasOutstandingLoans() && !$this->hasUnpaidFines();
    }

    public function getOutstandingLoansCountAttribute(): int
    {
        return $this->loans()->where('is_returned', false)->count();
    }

    public function getTotalUnpaidFinesAttribute(): float
    {
        return $this->fines()->where('is_paid', false)->sum('amount');
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }
}
