<?php

namespace App\Livewire\Staff\Elearning;

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
    
    // Stats
    public array $stats = [];
    public bool $isLoading = true;

    protected $queryString = ['search', 'statusFilter', 'categoryFilter', 'tab'];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $user = auth()->user();
        $branchId = $user->branch_id;
        $isSuperAdmin = $user->role === 'super_admin';

        $coursesQuery = Course::query();
        if (!$isSuperAdmin && $branchId) {
            $coursesQuery->where(fn($q) => $q->where('branch_id', $branchId)->orWhereNull('branch_id'));
        }

        $this->stats = [
            'total_courses' => (clone $coursesQuery)->count(),
            'published_courses' => (clone $coursesQuery)->where('status', 'published')->count(),
            'total_enrollments' => CourseEnrollment::whereIn('course_id', (clone $coursesQuery)->pluck('id'))->count(),
            'pending_enrollments' => CourseEnrollment::whereIn('course_id', (clone $coursesQuery)->pluck('id'))->where('status', 'pending')->count(),
            'completed_enrollments' => CourseEnrollment::whereIn('course_id', (clone $coursesQuery)->pluck('id'))->where('status', 'completed')->count(),
            'active_courses' => (clone $coursesQuery)->where('status', 'published')
                ->where(fn($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()))->count(),
        ];
        
        $this->isLoading = false;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        $branchId = $user->branch_id;
        $isSuperAdmin = $user->role === 'super_admin';

        $coursesQuery = Course::with(['category', 'instructor', 'branch'])
            ->withCount(['enrollments', 'modules']);

        if (!$isSuperAdmin && $branchId) {
            $coursesQuery->where(fn($q) => $q->where('branch_id', $branchId)->orWhereNull('branch_id'));
        }

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

        // Pending enrollments for approval tab
        $pendingEnrollments = [];
        if ($this->tab === 'enrollments') {
            $pendingEnrollments = CourseEnrollment::with(['course', 'member'])
                ->where('status', 'pending')
                ->latest()
                ->paginate(20);
        }

        return view('livewire.staff.elearning.elearning-dashboard', [
            'courses' => $courses,
            'categories' => $categories,
            'pendingEnrollments' => $pendingEnrollments,
        ])->extends('staff.layouts.app')->section('content');
    }
}
