<?php

namespace App\Livewire;

use App\Models\DdcClassification;
use Livewire\Component;
use Livewire\Attributes\On;

class DdcLookup extends Component
{
    public string $search = '';
    public array $results = [];
    public bool $isOpen = false;
    public ?string $selectedCode = null;
    public ?string $selectedDescription = null;

    protected $listeners = ['openDdcModal' => 'openModal'];

    public function openModal()
    {
        $this->isOpen = true;
        $this->search = '';
        $this->results = [];
        $this->selectedCode = null;
        $this->selectedDescription = null;
    }

    public function closeModal()
    {
        $this->isOpen = false;
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

    public function searchByClass($code)
    {
        $this->search = $code;
        $this->updatedSearch();
    }

    public function selectDdc($code, $description)
    {
        $this->selectedCode = $code;
        $this->selectedDescription = $description;
    }

    public function confirmSelection()
    {
        if ($this->selectedCode) {
            $this->dispatch('ddc-selected', code: $this->selectedCode);
            $this->closeModal();
        }
    }

    public function quickSelect($code)
    {
        $this->dispatch('ddc-selected', code: $code);
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.ddc-lookup');
    }
}
