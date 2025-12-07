<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = ['code', 'name', 'address', 'city', 'phone', 'email', 'is_main', 'is_active'];

    protected $casts = ['is_main' => 'boolean', 'is_active' => 'boolean'];

    public function books(): HasMany { return $this->hasMany(Book::class); }
    public function items(): HasMany { return $this->hasMany(Item::class); }
    public function members(): HasMany { return $this->hasMany(Member::class); }
    public function loans(): HasMany { return $this->hasMany(Loan::class); }
    public function locations(): HasMany { return $this->hasMany(Location::class); }
}
