<?php

namespace App\Livewire;

use App\Models\ClearanceLetter;
use App\Models\Member;
use App\Models\ThesisSubmission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MySubmissions extends Component
{
    use WithPagination;

    public string $filter = 'all';
    public ?int $memberId = null;
    public bool $showClearanceModal = false;
    public ?int $selectedSubmissionId = null;
    public string $clearanceError = '';

    public function mount(): void
    {
        $member = Auth::guard('member')->user();
        $this->memberId = $member?->id;
        
        if (!$this->memberId) {
            $this->redirect(route('opac.login'));
        }
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function deleteSubmission(int $id): void
    {
        if (!$this->memberId) return;
        
        $submission = ThesisSubmission::where('id', $id)
            ->where('member_id', $this->memberId)
            ->where('status', 'draft')
            ->first();

        if ($submission) {
            $submission->delete();
            session()->flash('success', 'Draft berhasil dihapus');
        }
    }

    public function openClearanceModal(int $submissionId): void
    {
        $this->selectedSubmissionId = $submissionId;
        $this->clearanceError = '';
        
        $member = Member::find($this->memberId);
        
        // Check for outstanding loans
        if ($member->hasOutstandingLoans()) {
            $this->clearanceError = 'Anda masih memiliki ' . $member->outstanding_loans_count . ' buku yang belum dikembalikan. Silakan kembalikan terlebih dahulu.';
        }
        
        // Check for unpaid fines
        if ($member->hasUnpaidFines()) {
            $fineAmount = number_format($member->total_unpaid_fines, 0, ',', '.');
            $this->clearanceError .= ($this->clearanceError ? ' ' : '') . 'Anda memiliki denda yang belum dibayar sebesar Rp ' . $fineAmount . '.';
        }
        
        $this->showClearanceModal = true;
    }

    public function closeClearanceModal(): void
    {
        $this->showClearanceModal = false;
        $this->selectedSubmissionId = null;
        $this->clearanceError = '';
    }

    public function requestClearanceLetter(): void
    {
        if (!$this->memberId || !$this->selectedSubmissionId) return;
        
        $member = Member::find($this->memberId);
        
        // Final check
        if (!$member->canRequestClearanceLetter()) {
            session()->flash('error', 'Tidak dapat mengajukan surat bebas pustaka. Pastikan tidak ada tunggakan.');
            $this->closeClearanceModal();
            return;
        }
        
        // Check if already requested
        $existing = ClearanceLetter::where('member_id', $this->memberId)
            ->where('thesis_submission_id', $this->selectedSubmissionId)
            ->whereIn('status', ['pending', 'approved'])
            ->first();
            
        if ($existing) {
            session()->flash('info', 'Anda sudah mengajukan surat bebas pustaka untuk tugas akhir ini.');
            $this->closeClearanceModal();
            return;
        }
        
        // Create clearance letter request
        ClearanceLetter::create([
            'member_id' => $this->memberId,
            'thesis_submission_id' => $this->selectedSubmissionId,
            'letter_number' => ClearanceLetter::generateLetterNumber(),
            'purpose' => 'graduation',
            'status' => 'pending',
        ]);
        
        session()->flash('success', 'Pengajuan surat bebas pustaka berhasil dikirim. Silakan tunggu persetujuan dari petugas.');
        $this->closeClearanceModal();
    }

    public function getMemberProperty()
    {
        return Member::find($this->memberId);
    }

    public function getClearanceLettersProperty()
    {
        if (!$this->memberId) return collect();
        
        return ClearanceLetter::where('member_id', $this->memberId)
            ->with('thesisSubmission')
            ->latest()
            ->get();
    }

    public function render()
    {
        if (!$this->memberId) {
            return view('livewire.my-submissions', [
                'submissions' => collect(),
                'counts' => ['all' => 0, 'draft' => 0, 'submitted' => 0, 'revision_required' => 0, 'approved' => 0, 'rejected' => 0],
            ]);
        }

        $query = ThesisSubmission::where('member_id', $this->memberId)
            ->with(['department', 'reviewer'])
            ->latest();

        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        $submissions = $query->paginate(10);

        $counts = [
            'all' => ThesisSubmission::where('member_id', $this->memberId)->count(),
            'draft' => ThesisSubmission::where('member_id', $this->memberId)->where('status', 'draft')->count(),
            'submitted' => ThesisSubmission::where('member_id', $this->memberId)->whereIn('status', ['submitted', 'under_review'])->count(),
            'revision_required' => ThesisSubmission::where('member_id', $this->memberId)->where('status', 'revision_required')->count(),
            'approved' => ThesisSubmission::where('member_id', $this->memberId)->whereIn('status', ['approved', 'published'])->count(),
            'rejected' => ThesisSubmission::where('member_id', $this->memberId)->where('status', 'rejected')->count(),
        ];

        return view('livewire.my-submissions', compact('submissions', 'counts'));
    }
}
