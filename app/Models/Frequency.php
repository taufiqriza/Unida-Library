<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Frequency extends Model
{
    protected $fillable = ['name', 'time_increment', 'time_unit', 'language_prefix'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
