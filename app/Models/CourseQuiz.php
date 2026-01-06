<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseQuiz extends Model
{
    protected $guarded = [];
    protected $casts = ['shuffle_questions' => 'boolean', 'show_correct_answers' => 'boolean'];

    public function material() { return $this->belongsTo(CourseMaterial::class, 'material_id'); }
    public function questions() { return $this->hasMany(CourseQuizQuestion::class, 'quiz_id')->orderBy('sort_order'); }
    public function attempts() { return $this->hasMany(CourseQuizAttempt::class, 'quiz_id'); }
}
