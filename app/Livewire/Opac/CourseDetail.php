<?php

namespace App\Livewire\Opac;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Livewire\Component;

class CourseDetail extends Component
{
    public Course $course;
    public bool $showEnrollModal = false;
    public ?CourseEnrollment $myEnrollment = null;

    public function mount($slug)
    {
        $this->course = Course::with(['category', 'instructor', 'branch', 'modules.materials'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        if (auth('member')->check()) {
            $this->myEnrollment = CourseEnrollment::where('course_id', $this->course->id)
                ->where('member_id', auth('member')->id())
                ->first();
        }
    }

    public function enroll()
    {
        if (!auth('member')->check()) {
            return redirect()->route('login');
        }

        if ($this->myEnrollment) {
            $this->dispatch('notify', type: 'info', message: 'Anda sudah terdaftar di kelas ini');
            return;
        }

        // Check max participants
        if ($this->course->max_participants) {
            $enrolled = $this->course->enrollments()->whereIn('status', ['approved', 'pending'])->count();
            if ($enrolled >= $this->course->max_participants) {
                $this->dispatch('notify', type: 'error', message: 'Kuota kelas sudah penuh');
                return;
            }
        }

        $this->myEnrollment = CourseEnrollment::create([
            'course_id' => $this->course->id,
            'member_id' => auth('member')->id(),
            'status' => $this->course->requires_approval ? 'pending' : 'approved',
            'enrolled_at' => now(),
            'approved_at' => $this->course->requires_approval ? null : now(),
        ]);

        $this->showEnrollModal = false;
        $message = $this->course->requires_approval 
            ? 'Pendaftaran berhasil! Menunggu persetujuan admin.'
            : 'Selamat! Anda berhasil terdaftar di kelas ini.';
        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function render()
    {
        return view('livewire.opac.course-detail')
            ->layout('components.opac.layout', ['title' => $this->course->title]);
    }
}
