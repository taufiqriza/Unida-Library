<?php

namespace App\Livewire\Staff\Elearning;

use App\Models\Branch;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use Livewire\Component;
use Livewire\WithPagination;

class ElearningDashboard extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $categoryFilter = '';
    public string $tab = 'courses';
    public ?int $selectedBranchId = null;
    
    public array $stats = [];

    protected $queryString = ['search', 'statusFilter', 'categoryFilter', 'tab', 'selectedBranchId'];

    public function mount()
    {
        $this->loadStats();
    }

    // Permission helpers
    public function canCreate(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'librarian']);
    }

    public function canEdit(Course $course): bool
    {
        $user = auth()->user();
        if ($user->role === 'super_admin') return true;
        if (in_array($user->role, ['admin', 'librarian'])) {
            return $course->branch_id === $user->branch_id || $course->instructor_id === $user->id;
        }
        return false;
    }

    public function loadStats()
    {
        $courseIds = $this->getAccessibleCourseIds();

        $this->stats = [
            'total_courses' => Course::whereIn('id', $courseIds)->count(),
            'published_courses' => Course::whereIn('id', $courseIds)->where('status', 'published')->count(),
            'total_enrollments' => CourseEnrollment::whereIn('course_id', $courseIds)->count(),
            'pending_enrollments' => CourseEnrollment::whereIn('course_id', $courseIds)->where('status', 'pending')->count(),
            'completed_enrollments' => CourseEnrollment::whereIn('course_id', $courseIds)->where('status', 'completed')->count(),
            'active_courses' => Course::whereIn('id', $courseIds)->where('status', 'published')
                ->where(fn($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()))->count(),
        ];
    }

    protected function getAccessibleCourseIds()
    {
        $user = auth()->user();
        
        // Super admin with branch filter
        if ($user->role === 'super_admin') {
            if ($this->selectedBranchId) {
                return Course::where('branch_id', $this->selectedBranchId)->pluck('id');
            }
            return Course::pluck('id');
        }
        
        // Admin/librarian sees their branch + global courses
        if (in_array($user->role, ['admin', 'librarian'])) {
            return Course::where(fn($q) => $q->where('branch_id', $user->branch_id)->orWhereNull('branch_id'))->pluck('id');
        }
        
        // Staff sees their branch courses only (read-only)
        return Course::where('branch_id', $user->branch_id)->pluck('id');
    }

    public function isSuperAdmin(): bool
    {
        return auth()->user()->role === 'super_admin';
    }

    public function updatedSelectedBranchId()
    {
        $this->resetPage();
        $this->loadStats();
    }

    public function approveEnrollment($id)
    {
        if (!$this->canCreate()) return;
        CourseEnrollment::find($id)?->update(['status' => 'approved', 'approved_at' => now(), 'approved_by' => auth()->id()]);
        $this->dispatch('notify', type: 'success', message: 'Peserta disetujui');
    }

    public function rejectEnrollment($id)
    {
        if (!$this->canCreate()) return;
        CourseEnrollment::find($id)?->update(['status' => 'rejected']);
        $this->dispatch('notify', type: 'success', message: 'Peserta ditolak');
    }

    public function updatedSearch() { $this->resetPage(); }

    public function render()
    {
        $courseIds = $this->getAccessibleCourseIds();

        $coursesQuery = Course::with(['category', 'instructor', 'branch'])
            ->withCount(['enrollments', 'modules'])
            ->whereIn('id', $courseIds);

        if ($this->search) {
            $coursesQuery->where(fn($q) => $q->where('title', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%"));
        }

        if ($this->statusFilter) {
            $coursesQuery->where('status', $this->statusFilter);
        }

        if ($this->categoryFilter) {
            $coursesQuery->where('category_id', $this->categoryFilter);
        }

        $courses = $coursesQuery->latest()->paginate(12);
        $categories = CourseCategory::where('is_active', true)->orderBy('sort_order')->get();

        $pendingEnrollments = $this->tab === 'enrollments' 
            ? CourseEnrollment::with(['course', 'member'])->whereIn('course_id', $courseIds)->where('status', 'pending')->latest()->paginate(20)
            : collect();

        return view('livewire.staff.elearning.elearning-dashboard', [
            'courses' => $courses,
            'categories' => $categories,
            'pendingEnrollments' => $pendingEnrollments,
            'canCreate' => $this->canCreate(),
            'branches' => Branch::where('is_active', true)->orderBy('name')->get(),
            'isSuperAdmin' => $this->isSuperAdmin(),
        ])->extends('staff.layouts.app')->section('content');
    }
}
