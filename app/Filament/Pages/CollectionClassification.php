<?php

namespace App\Filament\Pages;

use App\Models\Author;
use App\Models\Book;
use App\Models\CollectionType;
use App\Models\Item;
use App\Models\MediaType;
use App\Models\Publisher;
use App\Models\Subject;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class CollectionClassification extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Klasifikasi Koleksi';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.collection-classification';

    public ?string $filterType = null;
    public ?string $filterValue = null;
    public ?string $filterLabel = null;
    public string $search = '';

    public function getStats(): array
    {
        return [
            'total_books' => Book::count(),
            'total_items' => Item::count(),
            'total_media_types' => MediaType::whereHas('books')->count(),
            'total_collection_types' => CollectionType::whereHas('items')->count(),
        ];
    }

    public function getByMediaType(): array
    {
        return MediaType::withCount('books')
            ->having('books_count', '>', 0)
            ->orderByDesc('books_count')
            ->get()
            ->map(fn($m) => ['id' => $m->id, 'name' => $m->name, 'count' => $m->books_count])
            ->toArray();
    }

    public function getByCollectionType(): array
    {
        return CollectionType::withCount('items')
            ->having('items_count', '>', 0)
            ->orderByDesc('items_count')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id, 
                'name' => $c->name, 
                'count' => $c->items_count,
                'books' => Item::where('collection_type_id', $c->id)->distinct('book_id')->count('book_id')
            ])
            ->toArray();
    }

    public function getByClassification(): array
    {
        return Book::select('classification', DB::raw('COUNT(*) as count'))
            ->whereNotNull('classification')
            ->where('classification', '!=', '')
            ->groupBy('classification')
            ->orderByDesc('count')
            ->limit(20)
            ->get()
            ->toArray();
    }

    public function getByLanguage(): array
    {
        return Book::select('language', DB::raw('COUNT(*) as count'))
            ->whereNotNull('language')
            ->where('language', '!=', '')
            ->groupBy('language')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function getByPublisher(): array
    {
        return Publisher::withCount('books')
            ->having('books_count', '>', 0)
            ->orderByDesc('books_count')
            ->limit(12)
            ->get()
            ->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'count' => $p->books_count])
            ->toArray();
    }

    public function getByYear(): array
    {
        return Book::select('publish_year', DB::raw('COUNT(*) as count'))
            ->whereNotNull('publish_year')
            ->where('publish_year', '!=', '')
            ->groupBy('publish_year')
            ->orderByDesc('publish_year')
            ->limit(20)
            ->get()
            ->toArray();
    }

    public function getBySubject(): array
    {
        return Subject::withCount('books')
            ->having('books_count', '>', 0)
            ->orderByDesc('books_count')
            ->limit(15)
            ->get()
            ->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'count' => $s->books_count])
            ->toArray();
    }

    public function getByAuthor(): array
    {
        return Author::withCount('books')
            ->having('books_count', '>', 0)
            ->orderByDesc('books_count')
            ->limit(12)
            ->get()
            ->map(fn($a) => ['id' => $a->id, 'name' => $a->name, 'count' => $a->books_count])
            ->toArray();
    }

    public function openFilter(string $type, string $value, string $label): void
    {
        $this->filterType = $type;
        $this->filterValue = $value;
        $this->filterLabel = $label;
        $this->search = '';
    }

    public function closeFilter(): void
    {
        $this->filterType = null;
        $this->filterValue = null;
        $this->filterLabel = null;
        $this->search = '';
    }

    public function getFilteredBooks(): array
    {
        if (!$this->filterType) return [];

        $query = Book::with(['authors', 'publisher', 'items']);

        switch ($this->filterType) {
            case 'media_type':
                $query->where('media_type_id', $this->filterValue);
                break;
            case 'collection_type':
                $query->whereHas('items', fn($q) => $q->where('collection_type_id', $this->filterValue));
                break;
            case 'classification':
                $query->where('classification', 'like', "%{$this->filterValue}%");
                break;
            case 'language':
                $query->where('language', $this->filterValue);
                break;
            case 'publisher':
                $query->where('publisher_id', $this->filterValue);
                break;
            case 'year':
                $query->where('publish_year', $this->filterValue);
                break;
            case 'subject':
                $query->whereHas('subjects', fn($q) => $q->where('subjects.id', $this->filterValue));
                break;
            case 'author':
                $query->whereHas('authors', fn($q) => $q->where('authors.id', $this->filterValue));
                break;
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('isbn', 'like', "%{$this->search}%")
                  ->orWhereHas('authors', fn($a) => $a->where('name', 'like', "%{$this->search}%"));
            });
        }

        return $query->orderByDesc('updated_at')->limit(50)->get()->toArray();
    }
}
