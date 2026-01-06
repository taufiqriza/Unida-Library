<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    protected $guarded = [];
    
    protected $casts = [
        'enrolled_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function course() { return $this->belongsTo(Course::class); }
    public function member() { return $this->belongsTo(Member::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
    public function materialProgress() { return $this->hasMany(CourseMaterialProgress::class, 'enrollment_id'); }
    public function quizAttempts() { return $this->hasMany(CourseQuizAttempt::class, 'enrollment_id'); }
    public function certificate() { return $this->hasOne(CourseCertificate::class, 'enrollment_id'); }

    public function updateProgress()
    {
        $totalMaterials = $this->course->materials()->where('is_mandatory', true)->count();
        if ($totalMaterials === 0) return;
        
        $completedMaterials = $this->materialProgress()->where('is_completed', true)->count();
        $this->progress_percent = round(($completedMaterials / $totalMaterials) * 100);
        $this->save();
    }
}
