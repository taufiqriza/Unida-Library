<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberType extends Model
{
    protected $fillable = ['name', 'loan_limit', 'loan_period', 'fine_per_day', 'membership_period'];

    protected $casts = [
        'fine_per_day' => 'decimal:2',
    ];

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}
