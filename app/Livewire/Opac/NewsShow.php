<?php

namespace App\Livewire\Opac;

use App\Models\News;
use Livewire\Component;

class NewsShow extends Component
{
    public $news;
    public $relatedNews;

    public function mount($slug)
    {
        $this->news = News::where('slug', $slug)->published()->firstOrFail();
        $this->news->increment('views');
        
        $this->relatedNews = News::published()
            ->where('id', '!=', $this->news->id)
            ->latest('published_at')
            ->take(4)
            ->get();
    }

    public function render()
    {
        return view('livewire.opac.news-show')
            ->layout('components.opac.layout', ['title' => $this->news->title]);
    }
}
