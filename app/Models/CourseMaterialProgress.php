<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseMaterialProgress extends Model
{
    protected $table = 'course_material_progress';
    protected $guarded = [];
    protected $casts = ['is_completed' => 'boolean', 'started_at' => 'datetime', 'completed_at' => 'datetime'];

    public function enrollment() { return $this->belongsTo(CourseEnrollment::class, 'enrollment_id'); }
    public function material() { return $this->belongsTo(CourseMaterial::class, 'material_id'); }
}
