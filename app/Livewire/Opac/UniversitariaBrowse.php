<?php

namespace App\Livewire\Opac;

use App\Models\DigitalCategory;
use App\Models\Ebook;
use Livewire\Component;
use Livewire\WithPagination;

class UniversitariaBrowse extends Component
{
    use WithPagination;

    public ?string $selectedCategory = null;
    public string $search = '';
    public string $sortBy = 'publish_year';
    public string $sortOrder = 'desc';

    protected $queryString = [
        'selectedCategory' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function selectCategory(?string $slug)
    {
        $this->selectedCategory = $slug;
        $this->resetPage();
    }

    public function getCategoriesProperty()
    {
        return DigitalCategory::active()
            ->withCount(['ebooks' => fn($q) => $q->universitaria()->active()])
            ->get();
    }

    public function getEbooksProperty()
    {
        return Ebook::universitaria()
            ->active()
            ->when($this->selectedCategory, function ($query) {
                $category = DigitalCategory::where('slug', $this->selectedCategory)->first();
                return $query->where('digital_category_id', $category?->id);
            })
            ->when($this->search, function ($query) {
                return $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortOrder)
            ->paginate(12);
    }

    public function render()
    {
        return view('livewire.opac.universitaria-browse', [
            'categories' => $this->categories,
            'ebooks' => $this->ebooks,
        ])->layout('opac.layouts.app', [
            'title' => 'Universitaria - Warisan Intelektual PMDG',
            'description' => 'Koleksi berharga warisan intelektual dan sejarah Pondok Modern Darussalam Gontor',
        ]);
    }
}
