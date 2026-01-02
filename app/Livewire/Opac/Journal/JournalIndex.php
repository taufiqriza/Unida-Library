<?php

namespace App\Livewire\Opac\Journal;

use App\Models\JournalArticle;
use App\Models\JournalSource;
use Livewire\Component;
use Livewire\WithPagination;

class JournalIndex extends Component
{
    use WithPagination;

    public string $q = '';
    public string $journal = '';
    public string $year = '';
    public string $type = '';

    protected $queryString = [
        'q' => ['except' => ''],
        'journal' => ['except' => ''],
        'year' => ['except' => ''],
        'type' => ['except' => ''],
    ];

    public function updatingQ()
    {
        $this->resetPage();
    }

    public function updatingJournal()
    {
        $this->resetPage();
    }

    public function updatingYear()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function render()
    {
        $sources = JournalSource::where('is_active', true)
            ->withCount('articles')
            ->orderBy('name')
            ->get();

        $query = JournalArticle::query()->with('source');

        if ($this->q) {
            $search = $this->q;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('abstract', 'like', "%{$search}%");
            });
        }

        if ($this->journal) {
            $query->where('journal_code', $this->journal);
        }

        if ($this->year) {
            $query->where('publish_year', $this->year);
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        $articles = $query->orderByDesc('published_at')->paginate(20);

        $years = JournalArticle::selectRaw('DISTINCT publish_year')
            ->whereNotNull('publish_year')
            ->orderByDesc('publish_year')
            ->pluck('publish_year');

        $types = [
            'article' => 'Artikel Jurnal',
            'conference' => 'Prosiding',
            'book' => 'Buku',
            'book_section' => 'Bab Buku',
            'patent' => 'Paten/HKI',
        ];

        $typeCounts = JournalArticle::selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return view('livewire.opac.journal.journal-index', [
            'sources' => $sources,
            'articles' => $articles,
            'years' => $years,
            'types' => $types,
            'typeCounts' => $typeCounts,
        ])->layout('components.opac.layout', ['title' => 'Jurnal']);
    }
}
