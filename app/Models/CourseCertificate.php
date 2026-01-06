<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseCertificate extends Model
{
    protected $guarded = [];
    protected $casts = ['issued_at' => 'datetime'];

    public function enrollment() { return $this->belongsTo(CourseEnrollment::class, 'enrollment_id'); }
    public function issuedBy() { return $this->belongsTo(User::class, 'issued_by'); }
}
