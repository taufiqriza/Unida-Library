<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseQuizQuestion extends Model
{
    protected $guarded = [];
    protected $casts = ['options' => 'array'];

    public function quiz() { return $this->belongsTo(CourseQuiz::class, 'quiz_id'); }
}
