<?php

namespace App\Livewire\Opac;

use Livewire\Component;
use App\Services\KhastaraService;

class KhastaraDetail extends Component
{
    public $manuscriptId;
    public $manuscript;
    
    public function mount($id)
    {
        $this->manuscriptId = $id;
        $this->loadManuscript();
    }
    
    private function loadManuscript()
    {
        $khastaraService = new KhastaraService();
        $this->manuscript = $khastaraService->getManuscriptDetail($this->manuscriptId);
        
        if (!$this->manuscript) {
            abort(404, 'Naskah tidak ditemukan');
        }
    }
    
    public function render()
    {
        return view('livewire.opac.khastara-detail')
            ->layout('components.opac.layout', [
                'title' => ($this->manuscript['title'] ?? 'Detail Naskah') . ' - Khastara Perpusnas',
                'description' => 'Detail naskah ' . ($this->manuscript['title'] ?? '') . ' dari koleksi Khastara Perpustakaan Nasional'
            ]);
    }
}
