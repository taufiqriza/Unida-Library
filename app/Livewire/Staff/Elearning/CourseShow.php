<?php

namespace App\Livewire\Staff\Elearning;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseMaterial;
use App\Models\CourseEnrollment;
use App\Models\CourseCertificate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class CourseShow extends Component
{
    use WithFileUploads;

    public Course $course;
    public string $tab = 'overview';
    
    // Module form
    public bool $showModuleModal = false;
    public ?int $editingModuleId = null;
    public string $moduleTitle = '';
    public string $moduleDescription = '';

    // Material form
    public bool $showMaterialModal = false;
    public ?int $editingMaterialId = null;
    public ?int $selectedModuleId = null;
    public string $materialTitle = '';
    public string $materialContent = '';
    public string $materialType = 'text';
    public $materialFile = null;
    public ?string $materialVideoUrl = null;
    public ?string $materialExternalLink = null;
    public ?int $materialDuration = null;

    // Enrollment
    public string $enrollmentSearch = '';
    public string $enrollmentStatus = '';

    protected $listeners = ['refreshCourse' => '$refresh'];

    public function mount($id)
    {
        $this->course = Course::with(['category', 'instructor', 'branch', 'modules.materials'])->findOrFail($id);
    }

    // Module Methods
    public function openModuleModal($moduleId = null)
    {
        $this->resetModuleForm();
        if ($moduleId) {
            $module = CourseModule::find($moduleId);
            $this->editingModuleId = $moduleId;
            $this->moduleTitle = $module->title;
            $this->moduleDescription = $module->description ?? '';
        }
        $this->showModuleModal = true;
    }

    public function saveModule()
    {
        $this->validate([
            'moduleTitle' => 'required|max:255',
            'moduleDescription' => 'nullable',
        ]);

        if ($this->editingModuleId) {
            CourseModule::find($this->editingModuleId)->update([
                'title' => $this->moduleTitle,
                'description' => $this->moduleDescription,
            ]);
        } else {
            $maxOrder = $this->course->modules()->max('sort_order') ?? 0;
            $this->course->modules()->create([
                'title' => $this->moduleTitle,
                'description' => $this->moduleDescription,
                'sort_order' => $maxOrder + 1,
            ]);
        }

        $this->showModuleModal = false;
        $this->resetModuleForm();
        $this->course->refresh();
        $this->dispatch('notify', type: 'success', message: 'Modul berhasil disimpan');
    }

    public function deleteModule($moduleId)
    {
        CourseModule::find($moduleId)?->delete();
        $this->course->refresh();
        $this->dispatch('notify', type: 'success', message: 'Modul berhasil dihapus');
    }

    protected function resetModuleForm()
    {
        $this->editingModuleId = null;
        $this->moduleTitle = '';
        $this->moduleDescription = '';
    }

    // Material Methods
    public function openMaterialModal($moduleId, $materialId = null)
    {
        $this->resetMaterialForm();
        $this->selectedModuleId = $moduleId;
        
        if ($materialId) {
            $material = CourseMaterial::find($materialId);
            $this->editingMaterialId = $materialId;
            $this->materialTitle = $material->title;
            $this->materialContent = $material->content ?? '';
            $this->materialType = $material->type;
            $this->materialVideoUrl = $material->video_url;
            $this->materialExternalLink = $material->external_link;
            $this->materialDuration = $material->duration_minutes;
        }
        $this->showMaterialModal = true;
    }

    public function saveMaterial()
    {
        $this->validate([
            'materialTitle' => 'required|max:255',
            'materialType' => 'required|in:text,video,document,link,quiz',
            'materialFile' => 'nullable|file|max:51200',
        ]);

        $data = [
            'title' => $this->materialTitle,
            'content' => $this->materialContent,
            'type' => $this->materialType,
            'video_url' => $this->materialVideoUrl,
            'external_link' => $this->materialExternalLink,
            'duration_minutes' => $this->materialDuration,
        ];

        if ($this->materialFile) {
            $data['file_path'] = $this->materialFile->store('courses/materials', 'public');
        }

        if ($this->editingMaterialId) {
            CourseMaterial::find($this->editingMaterialId)->update($data);
        } else {
            $maxOrder = CourseMaterial::where('module_id', $this->selectedModuleId)->max('sort_order') ?? 0;
            $data['module_id'] = $this->selectedModuleId;
            $data['sort_order'] = $maxOrder + 1;
            CourseMaterial::create($data);
        }

        $this->showMaterialModal = false;
        $this->resetMaterialForm();
        $this->course->refresh();
        $this->dispatch('notify', type: 'success', message: 'Materi berhasil disimpan');
    }

    public function deleteMaterial($materialId)
    {
        CourseMaterial::find($materialId)?->delete();
        $this->course->refresh();
        $this->dispatch('notify', type: 'success', message: 'Materi berhasil dihapus');
    }

    protected function resetMaterialForm()
    {
        $this->editingMaterialId = null;
        $this->selectedModuleId = null;
        $this->materialTitle = '';
        $this->materialContent = '';
        $this->materialType = 'text';
        $this->materialFile = null;
        $this->materialVideoUrl = null;
        $this->materialExternalLink = null;
        $this->materialDuration = null;
    }

    // Enrollment Methods
    public function approveEnrollment($enrollmentId)
    {
        $enrollment = CourseEnrollment::find($enrollmentId);
        $enrollment->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);
        $this->dispatch('notify', type: 'success', message: 'Peserta disetujui');
    }

    public function rejectEnrollment($enrollmentId)
    {
        CourseEnrollment::find($enrollmentId)->update(['status' => 'rejected']);
        $this->dispatch('notify', type: 'success', message: 'Peserta ditolak');
    }

    public function markAsCompleted($enrollmentId)
    {
        $enrollment = CourseEnrollment::find($enrollmentId);
        $enrollment->update([
            'status' => 'completed',
            'completed_at' => now(),
            'progress_percent' => 100,
        ]);
        $this->dispatch('notify', type: 'success', message: 'Peserta ditandai lulus');
    }

    public function issueCertificate($enrollmentId)
    {
        $enrollment = CourseEnrollment::find($enrollmentId);
        
        if ($enrollment->certificate) {
            $this->dispatch('notify', type: 'error', message: 'Sertifikat sudah diterbitkan');
            return;
        }

        CourseCertificate::create([
            'enrollment_id' => $enrollmentId,
            'certificate_number' => 'CERT-' . strtoupper(Str::random(8)),
            'issued_at' => now(),
            'issued_by' => auth()->id(),
        ]);

        $this->dispatch('notify', type: 'success', message: 'Sertifikat berhasil diterbitkan');
    }

    public function render()
    {
        $enrollmentsQuery = $this->course->enrollments()->with('member');
        
        if ($this->enrollmentSearch) {
            $enrollmentsQuery->whereHas('member', fn($q) => 
                $q->where('name', 'like', "%{$this->enrollmentSearch}%")
                  ->orWhere('member_id', 'like', "%{$this->enrollmentSearch}%")
            );
        }
        
        if ($this->enrollmentStatus) {
            $enrollmentsQuery->where('status', $this->enrollmentStatus);
        }

        return view('livewire.staff.elearning.course-show', [
            'enrollments' => $enrollmentsQuery->latest()->paginate(20),
            'stats' => [
                'total_modules' => $this->course->modules()->count(),
                'total_materials' => $this->course->materials()->count(),
                'total_enrollments' => $this->course->enrollments()->count(),
                'pending_enrollments' => $this->course->enrollments()->where('status', 'pending')->count(),
                'completed_enrollments' => $this->course->enrollments()->where('status', 'completed')->count(),
            ],
        ])->extends('staff.layouts.app')->section('content');
    }
}
