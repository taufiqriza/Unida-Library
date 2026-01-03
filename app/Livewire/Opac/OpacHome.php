<?php

namespace App\Livewire\Opac;

use App\Models\Book;
use App\Models\Branch;
use App\Models\Ebook;
use App\Models\Ethesis;
use App\Models\Item;
use App\Models\JournalArticle;
use App\Models\News;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class OpacHome extends Component
{
    public array $stats = [];
    public $newBooks;
    public $popularBooks;
    public $latestEbooks;
    public $latestJournals;
    public $latestEtheses;
    public $news;
    public $branches;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->stats = [
            'books' => Book::withoutGlobalScopes()->count(),
            'items' => Item::withoutGlobalScopes()->count(),
            'journals' => JournalArticle::count(),
            'ebooks' => Ebook::count(),
            'etheses' => $this->getEthesisCount(),
        ];

        $this->newBooks = Book::withoutGlobalScopes()
            ->with('authors')
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->latest()
            ->take(16)
            ->get()
            ->map(fn($b) => [
                'id' => $b->id,
                'title' => $b->title,
                'authors' => $b->author_names ?: '-',
                'cover' => $b->cover_url,
                'publish_year' => $b->publish_year,
            ]);

        $this->popularBooks = Book::withoutGlobalScopes()
            ->with('authors', 'publisher')
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->inRandomOrder()
            ->take(16)
            ->get()
            ->map(fn($b) => [
                'id' => $b->id,
                'title' => $b->title,
                'authors' => $b->author_names ?: '-',
                'cover' => $b->cover_url,
                'publisher' => $b->publisher?->name,
                'year' => $b->publish_year,
            ]);

        $this->latestEbooks = Ebook::latest()->take(4)->get();
        $this->latestJournals = JournalArticle::latest()->take(4)->get();
        $this->latestEtheses = Ethesis::latest()->take(4)->get();

        $this->news = News::published()
            ->with('category')
            ->latest('published_at')
            ->take(8)
            ->get()
            ->map(fn($n) => [
                'slug' => $n->slug,
                'title' => $n->title,
                'excerpt' => $n->excerpt,
                'image' => $n->image_url,
                'category' => $n->category?->name,
                'published_at' => $n->published_at?->format('d M Y'),
            ]);

        $this->branches = Branch::all()->map(fn($b) => [
            'name' => $b->name,
            'address' => $b->address ?? '',
        ]);
    }

    protected function getEthesisCount(): int
    {
        return Cache::remember('ethesis_total_count', 3600, function () {
            return Ethesis::count();
        });
    }

    public function render()
    {
        return view('livewire.opac.opac-home')
            ->layout('components.opac.layout', ['title' => 'Beranda']);
    }
}
