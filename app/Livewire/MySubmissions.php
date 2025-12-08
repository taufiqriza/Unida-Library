<?php

namespace App\Livewire;

use App\Models\ThesisSubmission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MySubmissions extends Component
{
    use WithPagination;

    public string $filter = 'all';
    public ?int $memberId = null;

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
