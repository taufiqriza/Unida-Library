<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    protected $fillable = ['name', 'type', 'classification', 'authority_file'];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }
}
