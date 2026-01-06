<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseAnnouncement extends Model
{
    protected $guarded = [];
    protected $casts = ['is_pinned' => 'boolean'];

    public function course() { return $this->belongsTo(Course::class); }
    public function user() { return $this->belongsTo(User::class); }
}
