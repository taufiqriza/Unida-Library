<?php

namespace App\Livewire\Opac;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseMaterial;
use App\Models\CourseMaterialProgress;
use Livewire\Component;

class Classroom extends Component
{
    public Course $course;
    public CourseEnrollment $enrollment;
    public ?CourseMaterial $currentMaterial = null;
    public ?int $currentMaterialId = null;
    public bool $showCurriculum = true;

    public function mount($slug)
    {
        $this->course = Course::with(['modules.materials', 'instructor'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $this->enrollment = CourseEnrollment::where('course_id', $this->course->id)
            ->where('member_id', auth('member')->id())
            ->whereIn('status', ['approved', 'completed'])
            ->firstOrFail();

        // Set first incomplete material or first material
        $this->setInitialMaterial();
    }

    protected function setInitialMaterial()
    {
        $completedIds = CourseMaterialProgress::where('enrollment_id', $this->enrollment->id)
            ->where('is_completed', true)
            ->pluck('material_id');

        foreach ($this->course->modules as $module) {
            foreach ($module->materials as $material) {
                if (!$completedIds->contains($material->id)) {
                    $this->selectMaterial($material->id);
                    return;
                }
            }
        }

        // All completed, show first
        $firstMaterial = $this->course->modules->first()?->materials->first();
        if ($firstMaterial) {
            $this->selectMaterial($firstMaterial->id);
        }
    }

    public function selectMaterial($materialId)
    {
        $this->currentMaterialId = $materialId;
        $this->currentMaterial = CourseMaterial::with('module')->find($materialId);
    }

    public function markComplete()
    {
        if (!$this->currentMaterial) return;

        CourseMaterialProgress::updateOrCreate(
            ['enrollment_id' => $this->enrollment->id, 'material_id' => $this->currentMaterial->id],
            ['is_completed' => true, 'completed_at' => now()]
        );

        $this->updateProgress();
        $this->dispatch('notify', type: 'success', message: 'Materi selesai!');
    }

    public function nextMaterial()
    {
        $allMaterials = $this->course->modules->flatMap->materials;
        $currentIndex = $allMaterials->search(fn($m) => $m->id === $this->currentMaterialId);
        
        if ($currentIndex !== false && isset($allMaterials[$currentIndex + 1])) {
            $this->selectMaterial($allMaterials[$currentIndex + 1]->id);
        }
    }

    public function prevMaterial()
    {
        $allMaterials = $this->course->modules->flatMap->materials;
        $currentIndex = $allMaterials->search(fn($m) => $m->id === $this->currentMaterialId);
        
        if ($currentIndex !== false && $currentIndex > 0) {
            $this->selectMaterial($allMaterials[$currentIndex - 1]->id);
        }
    }

    protected function updateProgress()
    {
        $totalMaterials = $this->course->modules->sum(fn($m) => $m->materials->count());
        $completedMaterials = CourseMaterialProgress::where('enrollment_id', $this->enrollment->id)
            ->where('is_completed', true)
            ->count();

        $progress = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100) : 0;
        
        $this->enrollment->update([
            'progress_percent' => $progress,
            'status' => $progress >= 100 ? 'completed' : 'approved',
            'completed_at' => $progress >= 100 ? now() : null,
        ]);

        $this->enrollment->refresh();
        
        // Generate certificate if completed
        if ($progress >= 100 && $this->course->has_certificate) {
            $this->generateCertificate();
        }
    }

    public function generateCertificate()
    {
        $existing = \App\Models\CourseCertificate::where('enrollment_id', $this->enrollment->id)->first();
        if ($existing) return $existing;

        $member = auth('member')->user();
        $certNumber = 'CERT-' . strtoupper(substr(md5($this->enrollment->id . time()), 0, 8));
        
        return \App\Models\CourseCertificate::create([
            'enrollment_id' => $this->enrollment->id,
            'certificate_number' => $certNumber,
            'member_name' => $member->name,
            'course_title' => $this->course->title,
            'issued_at' => now(),
        ]);
    }

    public function downloadCertificate()
    {
        $cert = \App\Models\CourseCertificate::where('enrollment_id', $this->enrollment->id)->first();
        if (!$cert) {
            $cert = $this->generateCertificate();
        }
        
        return redirect()->route('opac.elearning.certificate', $cert->certificate_number);
    }

    public function isMaterialCompleted($materialId): bool
    {
        return CourseMaterialProgress::where('enrollment_id', $this->enrollment->id)
            ->where('material_id', $materialId)
            ->where('is_completed', true)
            ->exists();
    }

    public function getCompletedMaterialIds()
    {
        return CourseMaterialProgress::where('enrollment_id', $this->enrollment->id)
            ->where('is_completed', true)
            ->pluck('material_id')
            ->toArray();
    }

    public function render()
    {
        return view('livewire.opac.classroom', [
            'completedIds' => $this->getCompletedMaterialIds(),
        ])->layout('components.opac.layout');
    }
}
