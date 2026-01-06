<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Course extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_online' => 'boolean',
        'requires_approval' => 'boolean',
        'has_certificate' => 'boolean',
        'settings' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title) . '-' . Str::random(6);
            }
        });
    }

    public function category() { return $this->belongsTo(CourseCategory::class); }
    public function instructor() { return $this->belongsTo(User::class, 'instructor_id'); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function modules() { return $this->hasMany(CourseModule::class)->orderBy('sort_order'); }
    public function enrollments() { return $this->hasMany(CourseEnrollment::class); }
    public function announcements() { return $this->hasMany(CourseAnnouncement::class)->latest(); }
    
    public function materials()
    {
        return $this->hasManyThrough(CourseMaterial::class, CourseModule::class, 'course_id', 'module_id');
    }

    public function approvedEnrollments() { return $this->enrollments()->where('status', 'approved'); }
    public function completedEnrollments() { return $this->enrollments()->where('status', 'completed'); }
    
    public function getTotalMaterialsAttribute()
    {
        return $this->materials()->count();
    }

    public function getScheduleDaysArrayAttribute()
    {
        return $this->schedule_days ? json_decode($this->schedule_days, true) : [];
    }
}
