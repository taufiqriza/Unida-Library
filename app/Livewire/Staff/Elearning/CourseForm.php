<?php

namespace App\Livewire\Staff\Elearning;

use App\Models\Branch;
use App\Models\Course;
use App\Models\CourseCategory;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class CourseForm extends Component
{
    use WithFileUploads;

    public ?Course $course = null;
    public bool $editMode = false;

    // Form fields
    public string $title = '';
    public string $description = '';
    public ?int $category_id = null;
    public ?int $branch_id = null;
    public string $level = 'beginner';
    public ?int $duration_hours = null;
    public ?int $max_participants = null;
    public ?string $start_date = null;
    public ?string $end_date = null;
    public ?string $schedule_time = null;
    public array $schedule_days = [];
    public ?string $location = null;
    public bool $is_online = false;
    public ?string $meeting_link = null;
    public string $status = 'draft';
    public bool $requires_approval = false;
    public bool $has_certificate = true;
    public int $passing_score = 70;
    public $thumbnail = null;
    public ?string $existing_thumbnail = null;

    protected function rules()
    {
        return [
            'title' => 'required|max:255',
            'description' => 'nullable',
            'category_id' => 'nullable|exists:course_categories,id',
            'branch_id' => 'nullable|exists:branches,id',
            'level' => 'required|in:beginner,intermediate,advanced',
            'duration_hours' => 'nullable|integer|min:1',
            'max_participants' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'schedule_time' => 'nullable',
            'location' => 'nullable|max:255',
            'is_online' => 'boolean',
            'meeting_link' => 'nullable|url',
            'status' => 'required|in:draft,published,archived',
            'requires_approval' => 'boolean',
            'has_certificate' => 'boolean',
            'passing_score' => 'required|integer|min:0|max:100',
            'thumbnail' => 'nullable|image|max:2048',
        ];
    }

    public function mount($id = null)
    {
        $user = auth()->user();
        
        // Check permission - only admin/librarian can create/edit
        if (!in_array($user->role, ['super_admin', 'admin', 'librarian'])) {
            abort(403);
        }

        // Set default branch for non-super_admin
        if ($user->role !== 'super_admin') {
            $this->branch_id = $user->branch_id;
        }

        if ($id) {
            $this->course = Course::findOrFail($id);
            
            // Only super_admin or course creator can edit
            if ($user->role !== 'super_admin' && $this->course->instructor_id !== $user->id) {
                abort(403);
            }
            
            $this->editMode = true;
            $this->fill($this->course->toArray());
            $this->schedule_days = $this->course->schedule_days_array;
            $this->existing_thumbnail = $this->course->thumbnail;
            $this->start_date = $this->course->start_date?->format('Y-m-d');
            $this->end_date = $this->course->end_date?->format('Y-m-d');
        }
    }

    public function isSuperAdmin(): bool
    {
        return auth()->user()->role === 'super_admin';
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'slug' => Str::slug($this->title) . '-' . Str::random(6),
            'description' => $this->description,
            'category_id' => $this->category_id,
            'branch_id' => $this->branch_id,
            'level' => $this->level,
            'duration_hours' => $this->duration_hours,
            'max_participants' => $this->max_participants,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'schedule_time' => $this->schedule_time,
            'schedule_days' => json_encode($this->schedule_days),
            'location' => $this->location,
            'is_online' => $this->is_online,
            'meeting_link' => $this->meeting_link,
            'status' => $this->status,
            'requires_approval' => $this->requires_approval,
            'has_certificate' => $this->has_certificate,
            'passing_score' => $this->passing_score,
        ];

        if ($this->thumbnail) {
            $data['thumbnail'] = $this->thumbnail->store('courses/thumbnails', 'public');
        }

        if ($this->editMode) {
            unset($data['slug']);
            $this->course->update($data);
            $course = $this->course;
            $message = 'Kelas berhasil diperbarui';
        } else {
            $data['instructor_id'] = auth()->id();
            $course = Course::create($data);
            $message = 'Kelas berhasil dibuat';
        }

        $this->dispatch('notify', type: 'success', message: $message);
        return redirect()->route('staff.elearning.show', $course->id);
    }

    public function render()
    {
        return view('livewire.staff.elearning.course-form', [
            'categories' => CourseCategory::where('is_active', true)->orderBy('name')->get(),
            'branches' => Branch::where('is_active', true)->orderBy('name')->get(),
        ])->extends('staff.layouts.app')->section('content');
    }
}
