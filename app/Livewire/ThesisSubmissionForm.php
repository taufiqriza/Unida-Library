<?php

namespace App\Livewire;

use App\Enums\ThesisType;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\ThesisSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ThesisSubmissionForm extends Component
{
    use WithFileUploads;

    public int $step = 1;
    public int $totalSteps = 5;

    // Step 1: Informasi Dasar
    public string $type = 'skripsi';
    public string $title = '';
    public ?string $title_en = null;
    public string $abstract = '';
    public ?string $abstract_en = null;
    public ?string $keywords = null;
    public int $year;
    public ?string $defense_date = null;

    // Step 2: Data Penulis
    public string $author = '';
    public string $nim = '';
    public ?int $faculty_id = null;
    public ?int $department_id = null;

    // Step 3: Pembimbing & Penguji
    public string $advisor1 = '';
    public ?string $advisor2 = null;
    public ?string $examiner1 = null;
    public ?string $examiner2 = null;
    public ?string $examiner3 = null;

    // Step 4: Files
    public $cover_file = null;
    public $approval_file = null;
    public $preview_file = null;  // BAB 1-3
    public $fulltext_file = null;
    public bool $allow_fulltext_public = false;

    // Step 5: Confirmation
    public bool $agreement = false;

    // Edit mode
    public ?ThesisSubmission $submission = null;
    public bool $isEdit = false;

    protected $listeners = ['refreshComponent' => '$refresh'];
    
    public ?int $memberId = null;

    // Validation rules per step
    protected function rules(): array
    {
        return [
            'type' => 'required|in:skripsi,tesis,disertasi',
            'title' => 'required|min:10|max:500',
            'abstract' => 'required|min:100',
            'year' => 'required|numeric|min:2000|max:' . (date('Y') + 1),
            'author' => 'required|max:255',
            'nim' => 'required|max:50',
            'department_id' => 'required|exists:departments,id',
            'advisor1' => 'required|max:255',
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'Judul wajib diisi',
            'title.min' => 'Judul minimal 10 karakter',
            'abstract.required' => 'Abstrak wajib diisi',
            'abstract.min' => 'Abstrak minimal 100 karakter',
            'author.required' => 'Nama penulis wajib diisi',
            'nim.required' => 'NIM wajib diisi',
            'department_id.required' => 'Program studi wajib dipilih',
            'advisor1.required' => 'Pembimbing 1 wajib diisi',
            'cover_file.required' => 'Cover wajib diunggah',
            'cover_file.image' => 'Cover harus berupa gambar (JPG/PNG)',
            'cover_file.max' => 'Cover maksimal 2MB',
            'approval_file.required' => 'Lembar pengesahan wajib diunggah',
            'approval_file.mimes' => 'Lembar pengesahan harus berupa PDF',
            'approval_file.max' => 'Lembar pengesahan maksimal 5MB',
            'preview_file.required' => 'File BAB 1-3 wajib diunggah',
            'preview_file.mimes' => 'File BAB 1-3 harus berupa PDF',
            'preview_file.max' => 'File BAB 1-3 maksimal 20MB',
            'fulltext_file.mimes' => 'Full text harus berupa PDF',
            'fulltext_file.max' => 'Full text maksimal 50MB',
            'agreement.accepted' => 'Anda harus menyetujui pernyataan keaslian',
        ];
    }

    public function mount(?int $submissionId = null)
    {
        $this->year = date('Y');
        
        // Get member from Auth guard
        $member = Auth::guard('member')->user();
        $this->memberId = $member?->id;
        
        if (!$this->memberId) {
            $this->redirect(route('login'));
            return;
        }
        
        // Auto-fill from member
        $this->author = $member->name;
        $this->nim = $member->member_id ?? $member->nim_nidn;
        $this->faculty_id = $member->faculty_id;
        $this->department_id = $member->department_id;

        // Load existing submission for edit
        if ($submissionId) {
            $this->submission = ThesisSubmission::where('id', $submissionId)
                ->where('member_id', $this->memberId)
                ->whereIn('status', ['draft', 'revision_required'])
                ->first();

            if ($this->submission) {
                $this->isEdit = true;
                $this->loadSubmissionData();
            }
        }
    }

    protected function loadSubmissionData(): void
    {
        $s = $this->submission;
        $this->type = $s->type;
        $this->title = $s->title;
        $this->title_en = $s->title_en;
        $this->abstract = $s->abstract;
        $this->abstract_en = $s->abstract_en;
        $this->keywords = $s->keywords;
        $this->year = $s->year;
        $this->defense_date = $s->defense_date?->format('Y-m-d');
        $this->author = $s->author;
        $this->nim = $s->nim;
        $this->department_id = $s->department_id;
        $this->faculty_id = $s->department?->faculty_id;
        $this->advisor1 = $s->advisor1;
        $this->advisor2 = $s->advisor2;
        $this->examiner1 = $s->examiner1;
        $this->examiner2 = $s->examiner2;
        $this->examiner3 = $s->examiner3;
        $this->allow_fulltext_public = $s->allow_fulltext_public;
    }

    public function updatedFacultyId($value): void
    {
        $this->department_id = null;
    }

    public function selectType(string $type): void
    {
        if (in_array($type, ['skripsi', 'tesis', 'disertasi'])) {
            $this->type = $type;
        }
    }

    public function nextStep(): void
    {
        // Validate current step before proceeding
        if ($this->validateCurrentStep()) {
            if ($this->step < $this->totalSteps) {
                $this->step++;
            }
        }
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function goToStep(int $step): void
    {
        // Allow going back to any previous step
        if ($step >= 1 && $step < $this->step) {
            $this->step = $step;
        }
    }

    protected function validateCurrentStep(): bool
    {
        $rules = [];
        $messages = $this->messages();

        switch ($this->step) {
            case 1:
                $rules = [
                    'type' => 'required|in:skripsi,tesis,disertasi',
                    'title' => 'required|min:10|max:500',
                    'abstract' => 'required|min:100',
                    'year' => 'required|numeric|min:2000|max:' . (date('Y') + 1),
                ];
                break;
            case 2:
                $rules = [
                    'author' => 'required|max:255',
                    'nim' => 'required|max:50',
                    'department_id' => 'required|exists:departments,id',
                ];
                break;
            case 3:
                $rules = [
                    'advisor1' => 'required|max:255',
                ];
                break;
            case 4:
                return $this->validateFilesStep();
            case 5:
                $rules = [
                    'agreement' => 'accepted',
                ];
                break;
        }

        if (!empty($rules)) {
            $this->validate($rules, $messages);
        }

        return true;
    }

    protected function validateFilesStep(): bool
    {
        $rules = [];

        // Cover - required if new or not exists
        if (!$this->isEdit || !$this->submission?->cover_file) {
            $rules['cover_file'] = 'required|image|max:2048';
        } else {
            $rules['cover_file'] = 'nullable|image|max:2048';
        }

        // Approval - required if new or not exists
        if (!$this->isEdit || !$this->submission?->approval_file) {
            $rules['approval_file'] = 'required|mimes:pdf|max:5120';
        } else {
            $rules['approval_file'] = 'nullable|mimes:pdf|max:5120';
        }

        // Preview (BAB 1-3) - required if new or not exists
        if (!$this->isEdit || !$this->submission?->preview_file) {
            $rules['preview_file'] = 'required|mimes:pdf|max:20480';
        } else {
            $rules['preview_file'] = 'nullable|mimes:pdf|max:20480';
        }

        // Fulltext - always optional
        $rules['fulltext_file'] = 'nullable|mimes:pdf|max:51200';

        $this->validate($rules, $this->messages());

        return true;
    }

    // Keep old method for backward compatibility
    protected function validateStep(): void
    {
        $this->validateCurrentStep();
    }

    public function saveDraft(): void
    {
        // Ensure memberId is fresh from Auth
        $this->refreshMemberId();
        
        if (!$this->memberId) {
            $this->redirect(route('login'));
            return;
        }
        
        // Minimal validation for draft - only require title
        $this->validate([
            'title' => 'required|min:3',
        ], [
            'title.required' => 'Judul wajib diisi untuk menyimpan draft',
            'title.min' => 'Judul minimal 3 karakter',
        ]);

        // Check if department_id is set, if not set a default or skip
        if (!$this->department_id) {
            // Get first department as fallback for draft
            $firstDept = Department::first();
            if ($firstDept) {
                $this->department_id = $firstDept->id;
            }
        }

        $this->saveSubmission('draft');
        session()->flash('success', 'Draft berhasil disimpan!');
        $this->redirect(route('opac.member.submissions'));
    }

    public function submit(): void
    {
        // Ensure memberId is fresh from Auth
        $this->refreshMemberId();
        
        if (!$this->memberId) {
            $this->redirect(route('login'));
            return;
        }
        
        // Show loading indicator
        $this->dispatch('showLoading', ['message' => 'Menyimpan tugas akhir...', 'title' => '']);
        
        $this->validateStep();
        $submission = $this->saveSubmission('submitted');
        $submission->submit($this->memberId);
        
        // Close loading and show success
        $this->dispatch('closeAlert');
        $this->dispatch('showSuccess', [
            'title' => '🎉 Berhasil!', 
            'message' => 'Tugas akhir berhasil diajukan. Silakan tunggu proses verifikasi dari pustakawan.'
        ]);
        
        session()->flash('success', 'Tugas akhir berhasil diajukan! Silakan tunggu proses verifikasi dari pustakawan.');
        $this->redirect(route('opac.member.submissions'));
    }
    
    /**
     * Refresh memberId from Auth guard to prevent hydration issues
     */
    protected function refreshMemberId(): void
    {
        $member = Auth::guard('member')->user();
        $this->memberId = $member?->id;
    }

    protected function saveSubmission(string $status): ThesisSubmission
    {
        $data = [
            'member_id' => $this->memberId,
            'department_id' => $this->department_id,
            'type' => $this->type,
            'title' => $this->title,
            'title_en' => $this->title_en,
            'abstract' => $this->abstract,
            'abstract_en' => $this->abstract_en,
            'keywords' => $this->keywords,
            'author' => $this->author,
            'nim' => $this->nim,
            'advisor1' => $this->advisor1,
            'advisor2' => $this->advisor2,
            'examiner1' => $this->examiner1,
            'examiner2' => $this->examiner2,
            'examiner3' => $this->examiner3,
            'year' => $this->year,
            'defense_date' => $this->defense_date,
            'allow_fulltext_public' => false, // Always locked - only UNIDA members can access
            'status' => $status,
        ];

        // Handle file uploads - store in private thesis disk for security
        $storageDisk = 'thesis';
        $needsPreviewWatermark = false;
        
        if ($this->cover_file) {
            $data['cover_file'] = $this->cover_file->store('covers', $storageDisk);
        }
        if ($this->approval_file) {
            $data['approval_file'] = $this->approval_file->store('approvals', $storageDisk);
        }
        if ($this->preview_file) {
            $data['preview_file'] = $this->preview_file->store('previews', $storageDisk);
            $needsPreviewWatermark = true;
        }
        if ($this->fulltext_file) {
            $data['fulltext_file'] = $this->fulltext_file->store('fulltext', $storageDisk);
        }

        if ($this->isEdit && $this->submission) {
            $this->submission->update($data);
            $submission = $this->submission;
        } else {
            $submission = ThesisSubmission::create($data);
        }

        // Watermark only preview (BAB 1-3) - public access file
        if ($needsPreviewWatermark) {
            \App\Jobs\WatermarkPdfJob::dispatch($submission->fresh());
        }

        return $submission;
    }

    // Computed properties
    public function getFacultiesProperty()
    {
        return Faculty::orderBy('name')->pluck('name', 'id');
    }

    public function getDepartmentsProperty()
    {
        if (!$this->faculty_id) {
            return collect();
        }
        return Department::where('faculty_id', $this->faculty_id)->orderBy('name')->pluck('name', 'id');
    }

    public function getThesisTypesProperty(): array
    {
        return ThesisType::cases();
    }

    public function getSelectedTypeProperty(): ?ThesisType
    {
        return ThesisType::tryFrom($this->type);
    }

    public function getCompletionPercentageProperty(): int
    {
        $completed = 0;
        $total = 5;

        // Step 1 fields
        if ($this->type && $this->title && strlen($this->abstract) >= 100) $completed++;
        // Step 2 fields
        if ($this->author && $this->nim && $this->department_id) $completed++;
        // Step 3 fields
        if ($this->advisor1) $completed++;
        // Step 4 files
        $hasFiles = ($this->cover_file || ($this->isEdit && $this->submission?->cover_file))
            && ($this->approval_file || ($this->isEdit && $this->submission?->approval_file))
            && ($this->preview_file || ($this->isEdit && $this->submission?->preview_file));
        if ($hasFiles) $completed++;
        // Step 5
        if ($this->agreement) $completed++;

        return (int) (($completed / $total) * 100);
    }

    public function render()
    {
        return view('livewire.thesis-submission-form');
    }
}
