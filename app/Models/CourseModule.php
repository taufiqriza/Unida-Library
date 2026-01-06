<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseModule extends Model
{
    protected $guarded = [];
    protected $casts = ['is_published' => 'boolean'];

    public function course() { return $this->belongsTo(Course::class); }
    public function materials() { return $this->hasMany(CourseMaterial::class, 'module_id')->orderBy('sort_order'); }
}
