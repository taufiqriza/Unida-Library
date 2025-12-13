<?php

namespace App\Livewire\Staff\News;

use App\Models\Branch;
use App\Models\News;
use App\Models\NewsCategory;
use Livewire\Component;
use Livewire\WithPagination;

class NewsList extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $branch_id = '';
    public $deleteConfirmId = null;

    protected $queryString = ['search' => ['except' => ''], 'status' => ['except' => ''], 'branch_id' => ['except' => '']];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }
    public function updatingBranchId() { $this->resetPage(); }

    public function confirmDelete($id) { $this->deleteConfirmId = $id; }
    public function cancelDelete() { $this->deleteConfirmId = null; }

    public function delete()
    {
        if ($this->deleteConfirmId) {
            $news = News::where('branch_id', auth()->user()->branch_id)->find($this->deleteConfirmId);
            if ($news) {
                $news->delete();
                $this->dispatch('notify', type: 'success', message: 'Berita berhasil dihapus');
            }
            $this->deleteConfirmId = null;
        }
    }

    public function publish($id)
    {
        $news = News::where('branch_id', auth()->user()->branch_id)->find($id);
        if ($news) {
            $news->update(['status' => 'published', 'published_at' => now()]);
            $this->dispatch('notify', type: 'success', message: 'Berita berhasil dipublikasikan');
        }
    }

    public function render()
    {
        $userBranchId = auth()->user()->branch_id;

        $stats = [
            'total' => News::where('branch_id', $userBranchId)->count(),
            'published' => News::where('branch_id', $userBranchId)->where('status', 'published')->count(),
            'draft' => News::where('branch_id', $userBranchId)->where('status', 'draft')->count(),
            'views' => News::where('branch_id', $userBranchId)->sum('views'),
        ];

        $news = News::with(['category', 'author', 'branch'])
            ->when($this->branch_id, fn($q) => $q->where('branch_id', $this->branch_id))
            ->when($this->search, fn($q) => $q->where('title', 'like', '%'.$this->search.'%'))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(10);

        return view('livewire.staff.news.news-list', [
            'newsList' => $news,
            'stats' => $stats,
            'branches' => Branch::where('is_active', true)->pluck('name', 'id'),
            'userBranchId' => $userBranchId,
        ])->extends('staff.layouts.app')->section('content');
    }
}
