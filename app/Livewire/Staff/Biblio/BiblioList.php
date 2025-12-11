<?php

namespace App\Livewire\Staff\Biblio;

use App\Models\Book;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class BiblioList extends Component
{
    use WithPagination;

    public $search = '';
    public $viewMode = 'list'; // 'list' or 'grid'
    public $deleteConfirmId = null;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'viewMode' => ['except' => 'list'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function confirmDelete($id)
    {
        $this->deleteConfirmId = $id;
    }

    public function cancelDelete()
    {
        $this->deleteConfirmId = null;
    }

    public function delete()
    {
        if ($this->deleteConfirmId) {
            $book = Book::find($this->deleteConfirmId);
            if ($book) {
                // Check if user can delete (same branch)
                $user = auth()->user();
                if ($user->role === 'admin' || $book->branch_id === $user->branch_id) {
                    $book->delete();
                    session()->flash('success', 'Bibliografi berhasil dihapus.');
                } else {
                    session()->flash('error', 'Anda tidak memiliki akses untuk menghapus buku ini.');
                }
            }
            $this->deleteConfirmId = null;
        }
    }

    public function render()
    {
        $user = auth()->user();
        $userBranchId = $user->branch_id;
        
        // Stats for cards - scoped to user's branch
        $baseQuery = Book::where('branch_id', $userBranchId);
        
        $stats = [
            'total_books' => (clone $baseQuery)->count(),
            'total_items' => \App\Models\Item::whereHas('book', fn($q) => $q->where('branch_id', $userBranchId))->count(),
            'recent_additions' => (clone $baseQuery)->where('created_at', '>=', now()->subDays(7))->count(),
            'books_without_items' => (clone $baseQuery)->doesntHave('items')->count(),
        ];
        
        // Only show books from user's branch
        $books = Book::query()
            ->with(['branch', 'publisher', 'authors', 'items', 'user'])
            ->where('branch_id', $userBranchId)
            ->when($this->search, function (Builder $query) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('isbn', 'like', '%' . $this->search . '%')
                      ->orWhere('call_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('authors', function($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->latest('updated_at')
            ->paginate(12);

        return view('livewire.staff.biblio.biblio-list', [
            'books' => $books,
            'stats' => $stats,
            'userBranch' => $user->branch,
        ])->extends('staff.layouts.app')->section('content');
    }
}
