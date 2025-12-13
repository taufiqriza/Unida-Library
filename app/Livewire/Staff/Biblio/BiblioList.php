<?php

namespace App\Livewire\Staff\Biblio;

use App\Models\Book;
use App\Models\Item;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class BiblioList extends Component
{
    use WithPagination;

    public $search = '';
    public $viewMode = 'list';
    public $activeTab = 'biblio'; // 'biblio' or 'items'
    public $deleteConfirmId = null;
    public $selectedItems = [];
    public $selectAll = false;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'viewMode' => ['except' => 'list'],
        'activeTab' => ['except' => 'biblio'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function updatingActiveTab()
    {
        $this->resetPage();
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function setViewMode($mode) { $this->viewMode = $mode; }
    public function setTab($tab) { $this->activeTab = $tab; $this->resetPage(); $this->selectedItems = []; }

    public function updatedSelectAll($value)
    {
        if ($value && $this->activeTab === 'items') {
            $this->selectedItems = $this->getItemsQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function confirmDelete($id) { $this->deleteConfirmId = $id; }
    public function cancelDelete() { $this->deleteConfirmId = null; }

    public function delete()
    {
        if ($this->deleteConfirmId) {
            $book = Book::find($this->deleteConfirmId);
            if ($book) {
                $user = auth()->user();
                if ($user->role === 'admin' || $book->branch_id === $user->branch_id) {
                    $book->delete();
                    session()->flash('success', 'Bibliografi berhasil dihapus.');
                }
            }
            $this->deleteConfirmId = null;
        }
    }

    public function printBarcodes()
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('notify', type: 'error', message: 'Pilih minimal 1 eksemplar');
            return;
        }
        
        // Redirect to print page with selected items
        return redirect()->route('print.barcodes', ['items' => implode(',', $this->selectedItems)]);
    }

    protected function getItemsQuery()
    {
        $userBranchId = auth()->user()->branch_id;
        
        return Item::with(['book', 'collectionType', 'location', 'itemStatus'])
            ->where('branch_id', $userBranchId)
            ->when($this->search, function (Builder $query) {
                $query->where(function($q) {
                    $q->where('barcode', 'like', '%' . $this->search . '%')
                      ->orWhere('inventory_code', 'like', '%' . $this->search . '%')
                      ->orWhere('call_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('book', fn($q) => $q->where('title', 'like', '%' . $this->search . '%'));
                });
            })
            ->latest();
    }

    public function render()
    {
        $user = auth()->user();
        $userBranchId = $user->branch_id;
        
        $baseQuery = Book::where('branch_id', $userBranchId);
        
        $stats = [
            'total_books' => (clone $baseQuery)->count(),
            'total_items' => Item::where('branch_id', $userBranchId)->count(),
            'recent_additions' => (clone $baseQuery)->where('created_at', '>=', now()->subDays(7))->count(),
            'books_without_items' => (clone $baseQuery)->doesntHave('items')->count(),
        ];
        
        if ($this->activeTab === 'items') {
            $items = $this->getItemsQuery()->paginate(15);
            $books = collect();
        } else {
            $books = Book::query()
                ->with(['branch', 'publisher', 'authors', 'items', 'user'])
                ->where('branch_id', $userBranchId)
                ->when($this->search, function (Builder $query) {
                    $query->where(function($q) {
                        $q->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('isbn', 'like', '%' . $this->search . '%')
                          ->orWhere('call_number', 'like', '%' . $this->search . '%')
                          ->orWhereHas('authors', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
                    });
                })
                ->latest('updated_at')
                ->paginate(12);
            $items = collect();
        }

        return view('livewire.staff.biblio.biblio-list', [
            'books' => $books,
            'items' => $items,
            'stats' => $stats,
            'userBranch' => $user->branch,
        ])->extends('staff.layouts.app')->section('content');
    }
}
