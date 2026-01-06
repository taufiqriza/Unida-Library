<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CourseCategory extends Model
{
    protected $guarded = [];
    protected $casts = ['is_active' => 'boolean'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($cat) => $cat->slug = $cat->slug ?: Str::slug($cat->name));
    }

    public function courses() { return $this->hasMany(Course::class, 'category_id'); }
}
