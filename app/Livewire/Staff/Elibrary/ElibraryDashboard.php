<?php

namespace App\Livewire\Staff\Elibrary;

use App\Models\Ebook;
use App\Models\Ethesis;
use App\Models\PlagiarismCheck;
use App\Models\ThesisSubmission;
use App\Notifications\ThesisStatusNotification;
use App\Notifications\ClearanceLetterNotification;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ElibraryDashboard extends Component
{
    use WithPagination;

    public $activeTab = 'ebook';
    public $search = '';
    public $statusFilter = '';
    
    // Modal
    public $showDetailModal = false;
    public $selectedItem = null;
    public $selectedType = '';
    
    // Review
    public $reviewNotes = '';

    protected $queryString = ['activeTab' => ['except' => 'ebook'], 'search' => ['except' => ''], 'statusFilter' => ['except' => '']];
    
    protected $listeners = ['approveSubmission', 'rejectSubmission', 'requestRevision', 'publishSubmission'];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingActiveTab() { $this->resetPage(); $this->search = ''; $this->statusFilter = ''; }
    public function updatingStatusFilter() { $this->resetPage(); }

    public function setTab($tab) { $this->activeTab = $tab; $this->resetPage(); $this->search = ''; $this->statusFilter = ''; }
    public function setStatusFilter($status) { $this->statusFilter = $status; $this->resetPage(); }

    public function isMainBranch(): bool
    {
        $user = auth()->user();
        // Super admin can see all data
        if ($user->role === 'super_admin') {
            return true;
        }
        return $user->branch?->is_main ?? false;
    }

    /**
     * Check if current user can review thesis submissions
     * Super Admin, Admin, and Librarian can review
     */
    public function canReviewThesis(): bool
    {
        $user = auth()->user();
        return in_array($user->role, ['super_admin', 'admin', 'librarian']);
    }

    public function viewDetail($id, $type)
    {
        $this->selectedType = $type;
        if ($type === 'submission') {
            $this->selectedItem = ThesisSubmission::with(['member', 'department', 'department.faculty', 'reviewer', 'clearanceLetter'])->find($id);
        } elseif ($type === 'plagiarism') {
            $this->selectedItem = PlagiarismCheck::with(['member', 'thesisSubmission'])->find($id);
        }
        $this->showDetailModal = true;
        $this->reviewNotes = '';
    }

    public function closeModal()
    {
        $this->showDetailModal = false;
        $this->selectedItem = null;
        $this->reviewNotes = '';
    }

    public function approveSubmission()
    {
        if (!$this->canReviewThesis() || !$this->selectedItem) return;
        
        // Check for active loans
        $member = $this->selectedItem->member;
        $hasWarnings = false;
        
        if ($member) {
            if ($member->hasOutstandingLoans()) {
                $loanCount = $member->outstanding_loans_count;
                $this->dispatch('notify', 
                    type: 'warning', 
                    message: "⚠️ Mahasiswa ini masih memiliki {$loanCount} peminjaman aktif. Pastikan semua buku dikembalikan sebelum memberikan surat bebas pustaka."
                );
                $hasWarnings = true;
            }
            
            if ($member->hasUnpaidFines()) {
                $fines = number_format($member->total_unpaid_fines, 0, ',', '.');
                $this->dispatch('notify', 
                    type: 'warning', 
                    message: "⚠️ Mahasiswa ini memiliki denda belum dibayar Rp {$fines}."
                );
                $hasWarnings = true;
            }
        }
        
        $this->selectedItem->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $this->reviewNotes,
        ]);
        
        // Send notification
        $member?->notify(new ThesisStatusNotification($this->selectedItem, 'approved'));
        
        $message = 'Submission disetujui';
        if ($hasWarnings) {
            $message .= ' (dengan catatan peminjaman/denda)';
        }
        $this->dispatch('notify', type: 'success', message: $message);
        $this->closeModal();
    }

    public function rejectSubmission()
    {
        if (!$this->canReviewThesis() || !$this->selectedItem) return;
        
        $this->selectedItem->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $this->reviewNotes,
        ]);
        
        $this->selectedItem->member?->notify(new ThesisStatusNotification($this->selectedItem, 'rejected'));
        
        $this->dispatch('notify', type: 'success', message: 'Submission ditolak');
        $this->closeModal();
    }

    public function requestRevision()
    {
        if (!$this->canReviewThesis() || !$this->selectedItem) return;
        
        $this->selectedItem->update([
            'status' => 'revision_required',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $this->reviewNotes,
        ]);
        
        $this->selectedItem->member?->notify(new ThesisStatusNotification($this->selectedItem, 'revision'));
        
        $this->dispatch('notify', type: 'success', message: 'Permintaan revisi dikirim');
        $this->closeModal();
    }

    public function publishSubmission()
    {
        if (!$this->canReviewThesis() || !$this->selectedItem) return;
        
        // Create ethesis from submission
        $ethesis = \App\Models\Ethesis::create([
            'branch_id' => auth()->user()->branch_id,
            'department_id' => $this->selectedItem->department_id,
            'title' => $this->selectedItem->title,
            'title_en' => $this->selectedItem->title_en,
            'abstract' => $this->selectedItem->abstract,
            'abstract_en' => $this->selectedItem->abstract_en,
            'author' => $this->selectedItem->author,
            'nim' => $this->selectedItem->nim,
            'advisor1' => $this->selectedItem->advisor1,
            'advisor2' => $this->selectedItem->advisor2,
            'year' => $this->selectedItem->year,
            'defense_date' => $this->selectedItem->defense_date,
            'type' => $this->selectedItem->type,
            'keywords' => $this->selectedItem->keywords,
            'cover_path' => $this->selectedItem->cover_file,
            'file_path' => $this->selectedItem->fulltext_file,
            'is_public' => true,
            'is_fulltext_public' => $this->selectedItem->fulltext_visible,
            'user_id' => auth()->id(),
        ]);

        $this->selectedItem->update([
            'status' => 'published',
            'ethesis_id' => $ethesis->id,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        
        // Auto-create clearance letter on publish
        try {
            $this->createClearanceLetter($this->selectedItem);
        } catch (\Exception $e) {
            \Log::error('Failed to create clearance letter: ' . $e->getMessage());
        }
        
        // Auto send notification
        $this->sendNotificationEmail($this->selectedItem);
        
        $this->dispatch('notify', type: 'success', message: 'Berhasil dipublikasikan ke E-Thesis');
        $this->closeModal();
    }
    
    public function quickPublish($id)
    {
        $this->selectedItem = ThesisSubmission::find($id);
        if ($this->selectedItem && $this->selectedItem->status === 'approved') {
            $this->publishSubmission();
        }
    }
    
    protected function createClearanceLetter($submission)
    {
        // Load member without branch scope
        $member = \App\Models\Member::withoutGlobalScope('branch')->find($submission->member_id);
        if (!$member) {
            \Log::error('Clearance: member not found for submission ' . $submission->id);
            return null;
        }
        
        // Check if already has clearance
        $existing = \App\Models\ClearanceLetter::where('thesis_submission_id', $submission->id)->first();
        if ($existing) return $existing;
        
        $letter = \App\Models\ClearanceLetter::create([
            'member_id' => $member->id,
            'thesis_submission_id' => $submission->id,
            'letter_number' => \App\Models\ClearanceLetter::generateLetterNumber(),
            'purpose' => 'Bebas Pustaka - ' . ucfirst($submission->type),
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => 'Diterbitkan otomatis saat publikasi E-Thesis.',
        ]);
        
        try {
            $member->notify(new \App\Notifications\ClearanceLetterNotification($letter));
        } catch (\Exception $e) {
            \Log::error('Failed to send clearance notification: ' . $e->getMessage());
        }
        
        return $letter;
    }

    public function sendPublishNotification()
    {
        if (!$this->selectedItem || !$this->selectedItem->member) {
            $this->dispatch('notify', type: 'error', message: 'Member tidak ditemukan');
            return;
        }
        
        $this->sendNotificationEmail($this->selectedItem);
        $this->dispatch('notify', type: 'success', message: 'Notifikasi email berhasil dikirim');
    }

    protected function sendNotificationEmail($submission)
    {
        if (!$submission->member?->email) return;
        
        try {
            $data = [
                'author' => $submission->author,
                'title' => $submission->title,
                'type' => ucfirst($submission->type),
                'year' => $submission->year,
                'nim' => $submission->nim ?? null,
                'portalUrl' => route('opac.member.dashboard'),
            ];
            
            \Illuminate\Support\Facades\Mail::send('emails.publication-approved', $data, function ($message) use ($submission) {
                $message->to($submission->member->email)
                    ->subject('🎉 Karya Ilmiah Anda Telah Dipublikasikan - UNIDA Library');
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send publish notification: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $isMain = $this->isMainBranch();
        $userBranchId = auth()->user()->branch_id;

        // Stats - E-Book & E-Thesis are global, submissions filtered by member's branch
        $submissionQuery = ThesisSubmission::query();
        $plagiarismQuery = PlagiarismCheck::query();
        
        // Non-main branch only sees submissions from members of their branch
        if (!$isMain) {
            $submissionQuery->whereHas('member', fn($q) => $q->where('branch_id', $userBranchId));
            $plagiarismQuery->whereHas('member', fn($q) => $q->where('branch_id', $userBranchId));
        }

        $stats = [
            'ebooks' => Ebook::count(),
            'ethesis' => Ethesis::count(),
            'submissions_pending' => (clone $submissionQuery)->where('status', 'submitted')->count(),
            'plagiarism_pending' => (clone $plagiarismQuery)->where('status', 'pending')->count(),
        ];

        // Submission stats by status
        $submissionStats = [
            'submitted' => (clone $submissionQuery)->where('status', 'submitted')->count(),
            'under_review' => (clone $submissionQuery)->where('status', 'under_review')->count(),
            'revision_required' => (clone $submissionQuery)->where('status', 'revision_required')->count(),
            'approved' => (clone $submissionQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $submissionQuery)->where('status', 'rejected')->count(),
            'published' => (clone $submissionQuery)->where('status', 'published')->count(),
        ];

        // Plagiarism stats
        $plagiarismStats = [
            'pending' => (clone $plagiarismQuery)->where('status', 'pending')->count(),
            'processing' => (clone $plagiarismQuery)->where('status', 'processing')->count(),
            'completed' => (clone $plagiarismQuery)->where('status', 'completed')->count(),
            'failed' => (clone $plagiarismQuery)->where('status', 'failed')->count(),
        ];

        $data = collect();
        
        if ($this->activeTab === 'ebook') {
            $data = Ebook::with(['branch', 'user'])
                ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
                ->latest()->paginate(10);
        } elseif ($this->activeTab === 'ethesis') {
            $data = Ethesis::with(['branch', 'department', 'user'])
                ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%")->orWhere('author', 'like', "%{$this->search}%"))
                ->latest()->paginate(10);
        } elseif ($this->activeTab === 'submissions') {
            $query = ThesisSubmission::with(['member', 'department', 'department.faculty', 'clearanceLetter'])
                ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%")->orWhere('author', 'like', "%{$this->search}%")->orWhere('nim', 'like', "%{$this->search}%"))
                ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter));
            
            // Branch filter for non-main branch
            if (!$isMain) {
                $query->whereHas('member', fn($q) => $q->where('branch_id', $userBranchId));
            }
            
            $data = $query->latest()->paginate(10);
        } elseif ($this->activeTab === 'plagiarism') {
            $query = PlagiarismCheck::with(['member', 'thesisSubmission'])
                ->when($this->search, fn($q) => $q->where('document_title', 'like', "%{$this->search}%"))
                ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter));
            
            // Branch filter for non-main branch
            if (!$isMain) {
                $query->whereHas('member', fn($q) => $q->where('branch_id', $userBranchId));
            }
            
            $data = $query->latest()->paginate(10);
        }

        return view('livewire.staff.elibrary.elibrary-dashboard', [
            'stats' => $stats,
            'submissionStats' => $submissionStats,
            'plagiarismStats' => $plagiarismStats,
            'data' => $data,
            'isMainBranch' => $isMain,
            'canReviewThesis' => $this->canReviewThesis(),
        ])->extends('staff.layouts.app')->section('content');
    }
}
