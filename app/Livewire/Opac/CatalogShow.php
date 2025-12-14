<?php

namespace App\Livewire\Opac;

use App\Models\Book;
use Livewire\Component;

class CatalogShow extends Component
{
    public $book;
    public $relatedBooks;

    public function mount($id)
    {
        $this->book = Book::withoutGlobalScopes()
            ->with(['publisher', 'items' => fn($q) => $q->withoutGlobalScopes()->with('branch'), 'authors', 'subjects'])
            ->findOrFail($id);

        $this->relatedBooks = collect();
        if ($this->book->subjects->isNotEmpty()) {
            $this->relatedBooks = Book::withoutGlobalScopes()
                ->with('authors')
                ->whereHas('subjects', fn($q) => $q->whereIn('subjects.id', $this->book->subjects->pluck('id')))
                ->where('id', '!=', $this->book->id)
                ->take(4)
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.opac.catalog-show')
            ->layout('components.opac.layout', ['title' => $this->book->title]);
    }
}
