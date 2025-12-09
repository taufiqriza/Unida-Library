<?php

namespace App\Livewire;

use App\Services\DdcService;
use Livewire\Component;

class DdcLookup extends Component
{
    public string $search = '';
    public array $results = [];
    public ?string $selectedCode = null;
    public ?string $selectedDesc = null;

    public function updatedSearch()
    {
        if (strlen($this->search) >= 2) {
            $ddcService = app(DdcService::class);
            $this->results = $ddcService->search($this->search, 25);
        } else {
            $this->results = [];
        }
    }

    public function searchByClass(string $code)
    {
        $this->search = $code;
        $this->updatedSearch();
    }

    public function selectResult(string $code, string $desc)
    {
        $this->selectedCode = $code;
        $this->selectedDesc = $desc;
    }

    public function applySelection()
    {
        if ($this->selectedCode) {
            $this->dispatch('ddc-selected', code: $this->selectedCode);
        }
    }

    public function render()
    {
        return view('livewire.ddc-lookup');
    }
}
