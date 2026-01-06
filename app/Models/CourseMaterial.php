<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseMaterial extends Model
{
    protected $guarded = [];
    protected $casts = ['is_mandatory' => 'boolean', 'is_published' => 'boolean'];

    public function module() { return $this->belongsTo(CourseModule::class, 'module_id'); }
    public function quiz() { return $this->hasOne(CourseQuiz::class, 'material_id'); }
    public function progress() { return $this->hasMany(CourseMaterialProgress::class, 'material_id'); }
}
