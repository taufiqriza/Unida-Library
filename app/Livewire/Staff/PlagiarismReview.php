<?php

namespace App\Livewire\Staff;

use App\Models\PlagiarismCheck;
use App\Services\Plagiarism\CertificateGenerator;
use Livewire\Component;
use Livewire\WithPagination;

class PlagiarismReview extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $typeFilter = '';
    
    public ?PlagiarismCheck $selectedCheck = null;
    public bool $showModal = false;
    public string $reviewNotes = '';

    protected $queryString = ['search' => ['except' => ''], 'statusFilter' => ['except' => ''], 'typeFilter' => ['except' => '']];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingTypeFilter() { $this->resetPage(); }

    public function viewDetail($id)
    {
        $this->selectedCheck = PlagiarismCheck::with(['member', 'reviewer'])->find($id);
        $this->reviewNotes = $this->selectedCheck->review_notes ?? '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedCheck = null;
        $this->reviewNotes = '';
    }

    public function approveCheck()
    {
        if (!$this->selectedCheck) return;

        $this->selectedCheck->update([
            'status' => 'completed',
            'review_notes' => $this->reviewNotes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'completed_at' => now(),
        ]);

        // Generate certificate
        try {
            $generator = app(CertificateGenerator::class);
            $generator->generate($this->selectedCheck);
            
            // Notify member
            if ($this->selectedCheck->member) {
                $this->selectedCheck->member->notify(new \App\Notifications\PlagiarismCertificateNotification($this->selectedCheck));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to generate plagiarism certificate: ' . $e->getMessage());
        }

        $this->dispatch('notify', type: 'success', message: 'Pengajuan disetujui dan sertifikat diterbitkan');
        $this->closeModal();
    }

    public function rejectCheck()
    {
        if (!$this->selectedCheck || empty($this->reviewNotes)) {
            $this->dispatch('notify', type: 'error', message: 'Catatan review wajib diisi untuk penolakan');
            return;
        }

        $this->selectedCheck->update([
            'status' => 'failed',
            'review_notes' => $this->reviewNotes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'error_message' => 'Ditolak: ' . $this->reviewNotes,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Pengajuan ditolak');
        $this->closeModal();
    }

    public function getCountsProperty()
    {
        return [
            'all' => PlagiarismCheck::count(),
            'pending' => PlagiarismCheck::where('status', 'pending')->count(),
            'external_pending' => PlagiarismCheck::where('check_type', 'external')->where('status', 'pending')->count(),
            'completed' => PlagiarismCheck::where('status', 'completed')->count(),
            'failed' => PlagiarismCheck::where('status', 'failed')->count(),
        ];
    }

    public function render()
    {
        $checks = PlagiarismCheck::with(['member'])
            ->when($this->search, fn($q) => $q->where('document_title', 'like', "%{$this->search}%")
                ->orWhereHas('member', fn($q2) => $q2->where('name', 'like', "%{$this->search}%")))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->typeFilter, fn($q) => $q->where('check_type', $this->typeFilter))
            ->latest()
            ->paginate(15);

        return view('livewire.staff.plagiarism-review', [
            'checks' => $checks,
            'counts' => $this->counts,
        ])->layout('staff.layouts.app', ['title' => 'Review Cek Plagiasi']);
    }
}
