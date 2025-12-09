<?php

namespace App\Livewire;

use App\Models\DdcClassification;
use Livewire\Component;

class DdcLookup extends Component
{
    public string $search = '';
    public array $results = [];
    public string $targetField = 'classification';

    public function mount(string $targetField = 'classification')
    {
        $this->targetField = $targetField;
    }

    public function updatedSearch()
    {
        if (strlen($this->search) >= 2) {
            $this->results = DdcClassification::where('code', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%")
                ->orderBy('code')
                ->limit(30)
                ->get()
                ->toArray();
        } else {
            $this->results = [];
        }
    }

    public function selectDdc($code)
    {
        $this->dispatch('ddc-selected', code: $code, field: $this->targetField);
    }

    public function render()
    {
        return view('livewire.ddc-lookup');
    }
}
