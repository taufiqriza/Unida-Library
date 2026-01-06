<?php

namespace App\Livewire\Opac\Member;

use App\Models\CourseEnrollment;
use Livewire\Component;

class MyCourses extends Component
{
    public function render()
    {
        $enrollments = CourseEnrollment::with(['course.category', 'course.instructor', 'course.modules'])
            ->where('member_id', auth('member')->id())
            ->latest()
            ->get();

        return view('livewire.opac.member.my-courses', [
            'enrollments' => $enrollments,
        ])->layout('components.opac.layout');
    }
}
