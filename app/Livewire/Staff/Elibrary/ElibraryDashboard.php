<?php

namespace App\Livewire\Staff\Elibrary;

use App\Models\Ebook;
use App\Models\Ethesis;
use App\Models\PlagiarismCheck;
use App\Models\ThesisSubmission;
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

    public function updatingSearch() { $this->resetPage(); }
    public function updatingActiveTab() { $this->resetPage(); $this->search = ''; $this->statusFilter = ''; }
    public function updatingStatusFilter() { $this->resetPage(); }

    public function setTab($tab) { $this->activeTab = $tab; $this->resetPage(); $this->search = ''; $this->statusFilter = ''; }
    public function setStatusFilter($status) { $this->statusFilter = $status; $this->resetPage(); }

    public function isMainBranch(): bool
    {
        return auth()->user()->branch?->is_main ?? false;
    }

    /**
     * Check if current user can review thesis submissions
     * Only Super Admin and Admin can review (not regular Librarian)
     */
    public function canReviewThesis(): bool
    {
        $user = auth()->user();
        return $user->isAdmin(); // Super Admin or Admin only
    }

    public function viewDetail($id, $type)
    {
        $this->selectedType = $type;
        if ($type === 'submission') {
            $this->selectedItem = ThesisSubmission::with(['member', 'department', 'department.faculty', 'reviewer'])->find($id);
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
        
        // Auto-create clearance letter if member has no active loans and fines
        if ($member && !$hasWarnings) {
            \App\Models\ClearanceLetter::updateOrCreate(
                [
                    'member_id' => $member->id,
                    'thesis_submission_id' => $this->selectedItem->id,
                ],
                [
                    'letter_number' => \App\Models\ClearanceLetter::generateLetterNumber(),
                    'purpose' => 'Bebas Pustaka - ' . ucfirst($this->selectedItem->type),
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'notes' => 'Otomatis disetujui bersamaan dengan persetujuan tugas akhir.',
                ]
            );
        }
        
        $message = 'Submission disetujui';
        if ($hasWarnings) {
            $message .= ' (dengan catatan peminjaman/denda)';
        } else {
            $message .= ' dan surat bebas pustaka telah diterbitkan';
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
        
        // Auto send notification
        $this->sendNotificationEmail($this->selectedItem);
        
        $this->dispatch('notify', type: 'success', message: 'Berhasil dipublikasikan ke E-Thesis');
        $this->closeModal();
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
            \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($submission) {
                $message->to($submission->member->email)
                    ->subject('Karya Ilmiah Anda Telah Dipublikasikan - ' . config('app.name'))
                    ->html($this->getEmailTemplate($submission));
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send publish notification: ' . $e->getMessage());
        }
    }

    protected function getEmailTemplate($submission)
    {
        $portalUrl = route('opac.member.dashboard');
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;'>
                <h1 style='color: white; margin: 0;'>🎉 Selamat!</h1>
            </div>
            <div style='padding: 30px; background: #f8fafc;'>
                <p style='font-size: 16px; color: #334155;'>Halo <strong>{$submission->author}</strong>,</p>
                <p style='font-size: 16px; color: #334155;'>Karya ilmiah Anda telah berhasil dipublikasikan di E-Library:</p>
                <div style='background: white; border-radius: 12px; padding: 20px; margin: 20px 0; border-left: 4px solid #667eea;'>
                    <h3 style='margin: 0 0 10px 0; color: #1e293b;'>{$submission->title}</h3>
                    <p style='margin: 0; color: #64748b; font-size: 14px;'>Jenis: " . ucfirst($submission->type) . " • Tahun: {$submission->year}</p>
                </div>
                <p style='font-size: 16px; color: #334155;'>Anda dapat melihat dan mengunduh sertifikat publikasi melalui Member Portal.</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$portalUrl}' style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 14px 28px; text-decoration: none; border-radius: 8px; font-weight: bold;'>Buka Member Portal</a>
                </div>
            </div>
            <div style='padding: 20px; text-align: center; color: #94a3b8; font-size: 12px;'>
                " . config('app.name') . " • Email ini dikirim otomatis
            </div>
        </div>";
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
            $query = ThesisSubmission::with(['member', 'department', 'department.faculty'])
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
