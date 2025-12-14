<?php

namespace App\Livewire\Opac\Journal;

use App\Models\JournalArticle;
use Livewire\Component;

class JournalShow extends Component
{
    public JournalArticle $article;
    public $related;
    public bool $isRepo = false;

    public function mount(JournalArticle $article)
    {
        $this->article = $article;
        $this->article->load('source');
        $this->article->incrementViews();
        $this->isRepo = $article->source_type === 'repo';

        // Related articles from same journal/source
        $this->related = JournalArticle::where('id', '!=', $article->id)
            ->when($article->source_type === 'repo', 
                fn($q) => $q->where('source_type', 'repo'),
                fn($q) => $q->where('journal_code', $article->journal_code)
            )
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        $view = $this->isRepo ? 'livewire.opac.journal.journal-show-repo' : 'livewire.opac.journal.journal-show';
        
        return view($view)
            ->layout('components.opac.layout', ['title' => $this->article->title]);
    }
}
