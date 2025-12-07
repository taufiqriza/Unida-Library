<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentType extends Model
{
    protected $fillable = ['name', 'code'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
