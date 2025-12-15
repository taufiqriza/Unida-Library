<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'sort_order', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function ebooks()
    {
        return $this->hasMany(Ebook::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
