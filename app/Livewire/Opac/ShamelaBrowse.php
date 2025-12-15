<?php

namespace App\Livewire\Opac;

use App\Services\ShamelaLocalService;
use Livewire\Component;
use Livewire\WithPagination;

class ShamelaBrowse extends Component
{
    use WithPagination;
    
    public ?int $categoryId = null;
    public string $search = '';
    public int $perPage = 24;
    
    public array $stats = [];
    public array $categories = [];
    public array $featuredBooks = [];
    public array $classicBooks = [];
    public bool $isAvailable = false;

    protected $queryString = [
        'categoryId' => ['except' => null, 'as' => 'cat'],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $localService = new ShamelaLocalService();
        $this->isAvailable = $localService->isAvailable();
        
        if ($this->isAvailable) {
            $this->stats = $localService->getStats();
            $this->categories = $localService->getCategories();
            $this->featuredBooks = $localService->getFeaturedBooks(12);
            $this->classicBooks = $localService->getClassicBooks(8);
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryId()
    {
        $this->resetPage();
    }

    public function setCategory(?int $id)
    {
        $this->categoryId = $id;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->categoryId = null;
        $this->resetPage();
    }

    public function getResultsProperty()
    {
        if (!$this->isAvailable) {
            return ['results' => [], 'total' => 0];
        }

        $localService = new ShamelaLocalService();
        $offset = ($this->getPage() - 1) * $this->perPage;

        if (!empty($this->search)) {
            return $localService->search($this->search, $this->perPage, $offset, $this->categoryId);
        }

        if ($this->categoryId) {
            return $localService->getBooksByCategory($this->categoryId, $this->perPage, $offset);
        }

        // Default: show featured books
        return [
            'results' => $this->featuredBooks,
            'total' => count($this->featuredBooks),
        ];
    }

    public function getCategoryNameProperty(): ?string
    {
        if (!$this->categoryId) return null;
        
        foreach ($this->categories as $cat) {
            if ($cat['id'] === $this->categoryId) {
                return $cat['name'];
            }
        }
        return null;
    }

    public function getTotalPagesProperty(): int
    {
        return ceil(($this->results['total'] ?? 0) / $this->perPage);
    }

    public function getPage(): int
    {
        return max(1, request()->query('page', 1));
    }

    public function render()
    {
        return view('livewire.opac.shamela-browse')
            ->layout('components.opac.layout', [
                'title' => 'المكتبة الشاملة - Maktabah Shamela',
            ]);
    }
}
