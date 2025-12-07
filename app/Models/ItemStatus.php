<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemStatus extends Model
{
    protected $fillable = ['name', 'code', 'rules', 'no_loan'];

    protected $casts = [
        'no_loan' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
