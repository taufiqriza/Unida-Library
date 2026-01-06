<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseQuizAttempt extends Model
{
    protected $guarded = [];
    protected $casts = ['answers' => 'array', 'is_passed' => 'boolean', 'started_at' => 'datetime', 'submitted_at' => 'datetime'];

    public function enrollment() { return $this->belongsTo(CourseEnrollment::class, 'enrollment_id'); }
    public function quiz() { return $this->belongsTo(CourseQuiz::class, 'quiz_id'); }
}
