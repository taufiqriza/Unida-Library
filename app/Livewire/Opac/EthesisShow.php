<?php

namespace App\Livewire\Opac;

use App\Models\Ethesis;
use Livewire\Component;

class EthesisShow extends Component
{
    public $thesis;
    public $relatedTheses;
    public string $viewName;

    public function mount($id)
    {
        $this->thesis = Ethesis::where('is_public', true)->with('department')->findOrFail($id);
        $this->thesis->increment('views');

        $this->relatedTheses = Ethesis::where('is_public', true)
            ->where('id', '!=', $this->thesis->id)
            ->when($this->thesis->source_type === 'repo',
                fn($q) => $q->where('source_type', 'repo'),
                fn($q) => $q->where('department_id', $this->thesis->department_id)
            )
            ->latest()
            ->take(4)
            ->get();

        // Determine which view to use
        $this->viewName = $this->thesis->source_type === 'repo' 
            ? 'livewire.opac.ethesis-show-repo' 
            : 'livewire.opac.ethesis-show';
    }

    public function render()
    {
        return view($this->viewName)
            ->layout('components.opac.layout', ['title' => $this->thesis->title]);
    }
}
