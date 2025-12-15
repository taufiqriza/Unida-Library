<?php

namespace App\Livewire\Opac;

use App\Models\News;
use App\Models\NewsCategory;
use Livewire\Component;
use Livewire\WithPagination;

class NewsList extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $categoryId = null;
    public string $sortBy = 'latest';

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryId' => ['except' => null, 'as' => 'category'],
        'sortBy' => ['except' => 'latest', 'as' => 'sort'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryId()
    {
        $this->resetPage();
    }

    public function setCategory(?int $id)
    {
        $this->categoryId = $id;
        $this->resetPage();
    }

    public function render()
    {
        $query = News::with(['category', 'author'])
            ->published();

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('excerpt', 'like', "%{$this->search}%")
                  ->orWhere('content', 'like', "%{$this->search}%");
            });
        }

        // Category filter
        if ($this->categoryId) {
            $query->where('news_category_id', $this->categoryId);
        }

        // Sorting
        switch ($this->sortBy) {
            case 'oldest':
                $query->orderBy('published_at', 'asc');
                break;
            case 'popular':
                $query->orderBy('views', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('published_at', 'desc');
                break;
        }

        return view('livewire.opac.news-list', [
            'news' => $query->paginate(12),
            'categories' => NewsCategory::withCount(['news' => fn($q) => $q->published()])->orderBy('sort_order')->get(),
            'featuredNews' => News::published()->featured()->latest('published_at')->take(3)->get(),
            'pinnedNews' => News::published()->pinned()->latest('published_at')->first(),
            'totalNews' => News::published()->count(),
        ])->layout('components.opac.layout', ['title' => 'Berita & Pengumuman']);
    }
}
