<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionType extends Model
{
    protected $fillable = ['name', 'code'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
