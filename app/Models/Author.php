<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = ['name', 'type', 'authority_file'];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_author');
    }
}
