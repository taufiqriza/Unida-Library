<?php

namespace App\Livewire\Staff\Biblio;

use App\Models\Book;
use App\Models\Item;
use App\Models\Branch;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Subject;
use App\Models\Location;
use App\Models\Place;
use App\Models\MediaType;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class BiblioList extends Component
{
    use WithPagination;

    public $search = '';
    public $viewMode = 'list';
    public $activeTab = 'biblio';
    public $deleteConfirmId = null;
    public $deleteType = null;
    public $quickViewId = null;
    public $showPrintModal = false;
    public $selectedItems = [];
    public $selectAll = false;
    public $filterBranch = '';
    public $showBulkDeleteModal = false;
    
    // Modal for master data CRUD
    public $showModal = false;
    public $modalMode = 'create'; // create or edit
    public $editId = null;
    public $formName = '';
    public $formLocationBranch = '';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'activeTab' => ['except' => 'biblio'],
        'filterBranch' => ['except' => ''],
    ];

    public function mount()
    {
        $user = auth()->user();
        if ($user->role !== 'super_admin' && $user->branch_id) {
            $this->filterBranch = $user->branch_id;
        }
        $this->formLocationBranch = $this->filterBranch ?: ($user->branch_id ?? '');
    }

    public function updatingSearch() { $this->resetPage(); $this->selectedItems = []; $this->selectAll = false; }
    public function updatingActiveTab() { $this->resetPage(); $this->selectedItems = []; $this->selectAll = false; }
    public function setViewMode($mode) { $this->viewMode = $mode; }
    public function setTab($tab) { $this->activeTab = $tab; $this->resetPage(); $this->selectedItems = []; $this->search = ''; }

    public function updatedSelectAll($value)
    {
        if ($value) {
            if ($this->activeTab === 'items') {
                $this->selectedItems = $this->getItemsQuery()->paginate(15)->pluck('id')->map(fn($id) => (string) $id)->toArray();
            } elseif ($this->activeTab === 'biblio') {
                $this->selectedItems = $this->getBooksQuery()->paginate(12)->pluck('id')->map(fn($id) => (string) $id)->toArray();
            }
        } else {
            $this->selectedItems = [];
        }
    }

    public function clearSelection()
    {
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function toggleBookSelection($id)
    {
        $id = (string) $id;
        if (in_array($id, $this->selectedItems)) {
            $this->selectedItems = array_diff($this->selectedItems, [$id]);
        } else {
            $this->selectedItems[] = $id;
        }
        $this->selectAll = false;
    }

    public function confirmBulkDelete()
    {
        if (!empty($this->selectedItems)) {
            $this->showBulkDeleteModal = true;
        }
    }

    public function bulkDeleteBooks()
    {
        if (empty($this->selectedItems)) return;
        
        $deleted = 0;
        $failed = 0;
        
        foreach ($this->selectedItems as $id) {
            try {
                $book = Book::find($id);
                if ($book) {
                    $book->delete();
                    $deleted++;
                }
            } catch (\Exception $e) {
                $failed++;
            }
        }
        
        $this->selectedItems = [];
        $this->selectAll = false;
        $this->showBulkDeleteModal = false;
        
        session()->flash('message', "Berhasil menghapus {$deleted} buku." . ($failed > 0 ? " {$failed} gagal dihapus." : ''));
    }

    // Quick View
    public function quickView($id) { $this->quickViewId = $id; }
    public function closeQuickView() { $this->quickViewId = null; }
    
    public function getQuickViewBookProperty()
    {
        if (!$this->quickViewId) return null;
        return Book::with(['authors', 'subjects', 'publisher', 'place', 'mediaType', 'items'])->find($this->quickViewId);
    }

    // Delete confirmation
    public function confirmDelete($id, $type = 'book') { $this->deleteConfirmId = $id; $this->deleteType = $type; }
    public function cancelDelete() { $this->deleteConfirmId = null; $this->deleteType = null; }
    
    public function delete()
    {
        if (!$this->deleteConfirmId) return;
        
        $model = match($this->deleteType) {
            'book' => Book::class,
            'author' => Author::class,
            'publisher' => Publisher::class,
            'subject' => Subject::class,
            'location' => Location::class,
            'place' => Place::class,
            'gmd' => MediaType::class,
            default => null
        };
        
        if ($model) {
            $item = $model::find($this->deleteConfirmId);
            if ($item) {
                // Check if has related books (except for book itself)
                if ($this->deleteType !== 'book' && $this->deleteType !== 'location') {
                    $relation = match($this->deleteType) {
                        'author' => $item->books()->count(),
                        'publisher' => Book::where('publisher_id', $item->id)->count(),
                        'subject' => $item->books()->count(),
                        'place' => Book::where('place_id', $item->id)->count(),
                        'gmd' => Book::where('gmd_id', $item->id)->count(),
                        default => 0
                    };
                    if ($relation > 0) {
                        $this->dispatch('notify', type: 'error', message: 'Tidak bisa dihapus, masih ada ' . $relation . ' buku terkait');
                        $this->deleteConfirmId = null;
                        return;
                    }
                }
                $item->delete();
                $this->dispatch('notify', type: 'success', message: 'Data berhasil dihapus');
            }
        }
        $this->deleteConfirmId = null;
        $this->deleteType = null;
    }

    // Modal CRUD for master data
    public function openModal($mode = 'create', $id = null)
    {
        $this->modalMode = $mode;
        $this->editId = $id;
        $this->formName = '';
        
        if ($mode === 'edit' && $id) {
            $model = match($this->activeTab) {
                'authors' => Author::find($id),
                'publishers' => Publisher::find($id),
                'subjects' => Subject::find($id),
                'locations' => Location::find($id),
                'places' => Place::find($id),
                'gmd' => MediaType::find($id),
                default => null
            };
            if ($model) {
                $this->formName = $model->name;
                if ($this->activeTab === 'locations') {
                    $this->formLocationBranch = $model->branch_id ?? '';
                }
            }
        }
        $this->showModal = true;
    }
    
    public function closeModal() { $this->showModal = false; $this->editId = null; $this->formName = ''; }
    
    public function saveModal()
    {
        $this->validate(['formName' => 'required|min:2|max:255']);
        
        $model = match($this->activeTab) {
            'authors' => Author::class,
            'publishers' => Publisher::class,
            'subjects' => Subject::class,
            'locations' => Location::class,
            'places' => Place::class,
            'gmd' => MediaType::class,
            default => null
        };
        
        if (!$model) return;
        
        $data = ['name' => $this->formName];
        if ($this->activeTab === 'locations') {
            $data['branch_id'] = $this->formLocationBranch ?: auth()->user()->branch_id;
        }
        
        if ($this->modalMode === 'edit' && $this->editId) {
            $model::where('id', $this->editId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Data berhasil diupdate');
        } else {
            $model::create($data);
            $this->dispatch('notify', type: 'success', message: 'Data berhasil ditambahkan');
        }
        
        $this->closeModal();
    }

    // Print barcodes
    public function printBarcodes()
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('notify', type: 'error', message: 'Pilih minimal 1 eksemplar');
            return;
        }
        $this->showPrintModal = true;
    }
    public function closePrintModal() { $this->showPrintModal = false; }
    public function confirmPrint()
    {
        $this->showPrintModal = false;
        return redirect()->route('print.barcodes', ['items' => implode(',', $this->selectedItems)]);
    }
    public function getSelectedItemsDataProperty()
    {
        if (empty($this->selectedItems)) return collect();
        return Item::with('book')->whereIn('id', $this->selectedItems)->get();
    }

    protected function getBranchFilter()
    {
        $user = auth()->user();
        if ($user->role === 'super_admin') return $this->filterBranch ?: null;
        return $user->branch_id;
    }

    protected function getBooksQuery()
    {
        $branchFilter = $this->getBranchFilter();
        return Book::query()
            ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
            ->when($this->search, function ($q) {
                $s = $this->search;
                $q->where(fn($q) => $q->where('title', 'like', "%{$s}%")->orWhere('isbn', 'like', "%{$s}%")->orWhere('call_number', 'like', "%{$s}%"));
            });
    }

    protected function getItemsQuery()
    {
        $branchFilter = $this->getBranchFilter();
        return Item::query()
            ->select(['id', 'book_id', 'barcode', 'inventory_code', 'call_number', 'branch_id', 'collection_type_id', 'location_id', 'item_status_id', 'created_at'])
            ->with(['book:id,title,isbn', 'collectionType:id,name', 'location:id,name', 'itemStatus:id,name'])
            ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
            ->when($this->search, function ($q) {
                $s = $this->search;
                $q->where(fn($q) => $q->where('barcode', 'like', "%{$s}%")->orWhere('inventory_code', 'like', "%{$s}%")->orWhere('call_number', 'like', "%{$s}%"));
                if (strlen($s) >= 3) $q->orWhereHas('book', fn($q) => $q->where('title', 'like', "%{$s}%"));
            })
            ->latest();
    }

    public function render()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $branchFilter = $this->getBranchFilter();
        
        $data = ['books' => collect(), 'items' => collect(), 'masterData' => collect()];
        
        // Stats
        $stats = cache()->remember("biblio_stats_{$branchFilter}_v2", 300, function () use ($branchFilter) {
            $bq = Book::query()->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter));
            $iq = Item::query()->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter));
            return [
                'total_books' => (clone $bq)->count(),
                'total_items' => (clone $iq)->count(),
                'total_authors' => Author::count(),
                'active_loans' => \App\Models\Loan::whereNull('return_date')->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))->count(),
                'recent_additions' => (clone $bq)->where('created_at', '>=', now()->subDays(7))->count(),
                'books_without_items' => (clone $bq)->whereDoesntHave('items')->count(),
            ];
        });

        // Tab content
        switch ($this->activeTab) {
            case 'items':
                $data['items'] = $this->getItemsQuery()->paginate(15);
                break;
            case 'authors':
                $data['masterData'] = Author::withCount('books')
                    ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orderBy('name')->paginate(20);
                break;
            case 'publishers':
                $data['masterData'] = Publisher::withCount('books')
                    ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orderBy('name')->paginate(20);
                break;
            case 'subjects':
                $data['masterData'] = Subject::withCount('books')
                    ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orderBy('name')->paginate(20);
                break;
            case 'locations':
                $data['masterData'] = Location::withCount('items')
                    ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
                    ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orderBy('name')->paginate(20);
                break;
            case 'places':
                $data['masterData'] = Place::withCount('books')
                    ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orderBy('name')->paginate(20);
                break;
            case 'gmd':
                $data['masterData'] = MediaType::withCount('books')
                    ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orderBy('name')->paginate(20);
                break;
            default: // biblio
                $data['books'] = Book::query()
                    ->select(['id', 'branch_id', 'title', 'isbn', 'image', 'call_number', 'publisher_id', 'media_type_id', 'publish_year', 'created_at', 'updated_at', 'user_id'])
                    ->with(['branch:id,name', 'publisher:id,name', 'authors:id,name', 'user:id,name', 'mediaType:id,name'])
                    ->withCount('items')
                    ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
                    ->when($this->search, function ($q) {
                        $s = $this->search;
                        $q->where(fn($q) => $q->where('title', 'like', "%{$s}%")->orWhere('isbn', 'like', "%{$s}%")->orWhere('call_number', 'like', "%{$s}%"));
                        if (strlen($s) >= 3) $q->orWhereHas('authors', fn($q) => $q->where('name', 'like', "%{$s}%"));
                    })
                    ->latest('updated_at')->paginate(12);
        }

        $branches = $isSuperAdmin ? cache()->remember('all_branches', 3600, fn() => Branch::select(['id', 'name'])->orderBy('name')->get()) : collect();

        return view('livewire.staff.biblio.biblio-list', array_merge($data, [
            'stats' => $stats,
            'userBranch' => $user->branch,
            'branches' => $branches,
            'isSuperAdmin' => $isSuperAdmin,
        ]))->extends('staff.layouts.app')->section('content');
    }
}
