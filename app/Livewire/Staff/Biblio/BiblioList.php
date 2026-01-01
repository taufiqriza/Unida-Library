<?php

namespace App\Livewire\Staff\Biblio;

use App\Models\Book;
use App\Models\Item;
use App\Models\Branch;
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
    public $quickViewId = null;
    public $showPrintModal = false;
    public $selectedItems = [];
    public $selectAll = false;
    public $filterBranch = '';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'viewMode' => ['except' => 'list'],
        'activeTab' => ['except' => 'biblio'],
        'filterBranch' => ['except' => ''],
    ];

    public function mount()
    {
        $user = auth()->user();
        // For super_admin, don't default to any branch (show all)
        // For others, default to their branch
        if ($user->role !== 'super_admin' && $user->branch_id) {
            $this->filterBranch = $user->branch_id;
        }
    }

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
    
    public function quickView($id) { $this->quickViewId = $id; }
    public function closeQuickView() { $this->quickViewId = null; }
    
    public function getQuickViewBookProperty()
    {
        if (!$this->quickViewId) return null;
        return Book::with(['authors', 'subjects', 'publisher', 'place', 'mediaType', 'items'])->find($this->quickViewId);
    }

    public function delete()
    {
        if ($this->deleteConfirmId) {
            $book = Book::find($this->deleteConfirmId);
            if ($book) {
                $user = auth()->user();
                if ($user->role === 'super_admin' || $user->role === 'admin' || $book->branch_id === $user->branch_id) {
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
        $this->showPrintModal = true;
    }

    public function closePrintModal()
    {
        $this->showPrintModal = false;
    }

    public function confirmPrint()
    {
        $this->showPrintModal = false;
        return redirect()->route('print.barcodes', ['items' => implode(',', $this->selectedItems)]);
    }

    public function getSelectedItemsDataProperty()
    {
        if (empty($this->selectedItems)) return collect();
        return \App\Models\Item::with('book')->whereIn('id', $this->selectedItems)->get();
    }

    protected function getBranchFilter()
    {
        $user = auth()->user();
        
        // Super admin can see all or filter by branch
        if ($user->role === 'super_admin') {
            return $this->filterBranch ?: null; // null means all branches
        }
        
        // Others can only see their branch
        return $user->branch_id;
    }

    protected function getItemsQuery()
    {
        $branchFilter = $this->getBranchFilter();
        
        return Item::query()
            ->select(['id', 'book_id', 'barcode', 'inventory_code', 'call_number', 'branch_id', 'collection_type_id', 'location_id', 'item_status_id', 'created_at'])
            ->with([
                'book:id,title,isbn',
                'collectionType:id,name',
                'location:id,name',
                'itemStatus:id,name'
            ])
            ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
            ->when($this->search, function ($query) {
                $search = $this->search;
                $query->where(function($q) use ($search) {
                    $q->where('barcode', 'like', "%{$search}%")
                      ->orWhere('inventory_code', 'like', "%{$search}%")
                      ->orWhere('call_number', 'like', "%{$search}%");
                });
                // Only search book title if search length >= 3
                if (strlen($search) >= 3) {
                    $query->orWhereHas('book', fn($q) => $q->where('title', 'like', "%{$search}%"));
                }
            })
            ->latest();
    }

    public function render()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $branchFilter = $this->getBranchFilter();
        
        // Cache stats for 5 minutes to reduce COUNT queries
        $cacheKey = "biblio_stats_{$branchFilter}";
        $stats = cache()->remember($cacheKey, 300, function () use ($branchFilter) {
            $baseQuery = Book::query()->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter));
            $itemBaseQuery = Item::query()->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter));
            
            return [
                'total_books' => (clone $baseQuery)->count(),
                'total_items' => (clone $itemBaseQuery)->count(),
                'recent_additions' => (clone $baseQuery)->where('created_at', '>=', now()->subDays(7))->count(),
                'books_without_items' => (clone $baseQuery)->whereDoesntHave('items')->count(),
            ];
        });
        
        if ($this->activeTab === 'items') {
            $items = $this->getItemsQuery()->paginate(15);
            $books = collect();
        } else {
            $books = Book::query()
                ->select(['id', 'branch_id', 'title', 'isbn', 'image', 'call_number', 'publisher_id', 'publish_year', 'created_at', 'updated_at', 'user_id'])
                ->with([
                    'branch:id,name',
                    'publisher:id,name',
                    'authors:id,name',
                    'user:id,name'
                ])
                ->withCount('items')
                ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
                ->when($this->search, function (Builder $query) {
                    $search = $this->search;
                    $query->where(function($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                          ->orWhere('isbn', 'like', "%{$search}%")
                          ->orWhere('call_number', 'like', "%{$search}%");
                    });
                    // Author search is slow - only do if specific search pattern
                    if (strlen($this->search) >= 3) {
                        $query->orWhereHas('authors', fn($q) => $q->where('name', 'like', "%{$search}%"));
                    }
                })
                ->latest('updated_at')
                ->paginate(12);
            $items = collect();
        }

        // Cache branches list
        $branches = $isSuperAdmin 
            ? cache()->remember('all_branches', 3600, fn() => Branch::select(['id', 'name'])->orderBy('name')->get()) 
            : collect();

        return view('livewire.staff.biblio.biblio-list', [
            'books' => $books,
            'items' => $items,
            'stats' => $stats,
            'userBranch' => $user->branch,
            'branches' => $branches,
            'isSuperAdmin' => $isSuperAdmin,
        ])->extends('staff.layouts.app')->section('content');
    }
}
