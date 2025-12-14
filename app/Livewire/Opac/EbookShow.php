<?php

namespace App\Livewire\Opac;

use App\Models\Ebook;
use Livewire\Component;

class EbookShow extends Component
{
    public $ebook;
    public $relatedEbooks;

    public function mount($id)
    {
        $this->ebook = Ebook::where('is_active', true)->with('authors')->findOrFail($id);
        
        $this->relatedEbooks = Ebook::where('is_active', true)
            ->where('id', '!=', $this->ebook->id)
            ->latest()
            ->take(4)
            ->get();
    }

    public function render()
    {
        return view('livewire.opac.ebook-show')
            ->layout('components.opac.layout', ['title' => $this->ebook->title]);
    }
}
