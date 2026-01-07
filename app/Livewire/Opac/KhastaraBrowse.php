<?php

namespace App\Livewire\Opac;

use Livewire\Component;
use App\Services\KhastaraService;
use Livewire\WithPagination;

class KhastaraBrowse extends Component
{
    use WithPagination;
    
    public $search = '';
    public $searchType = 'title';
    public $selectedLanguage = '';
    public $selectedType = '';
    
    public $languages = [];
    public $types = [];
    
    protected $queryString = [
        'search' => ['except' => ''],
        'searchType' => ['except' => 'title'],
        'selectedLanguage' => ['except' => ''],
        'selectedType' => ['except' => '']
    ];
    
    public function mount()
    {
        $this->loadFilters();
    }
    
    private function loadFilters()
    {
        $khastaraService = new KhastaraService();
        $stats = $khastaraService->getStatistics();
        
        if ($stats && isset($stats['data'])) {
            $this->languages = collect($stats['data']['language_name'] ?? [])->pluck('val')->toArray();
            $this->types = collect($stats['data']['worksheet_name'] ?? [])->pluck('val')->toArray();
        }
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedSelectedLanguage()
    {
        $this->resetPage();
    }
    
    public function updatedSelectedType()
    {
        $this->resetPage();
    }
    
    public function clearFilters()
    {
        $this->search = '';
        $this->selectedLanguage = '';
        $this->selectedType = '';
        $this->resetPage();
    }
    
    public function getManuscriptsProperty()
    {
        $khastaraService = new KhastaraService();
        
        $filters = [];
        
        if ($this->search) {
            $filters[$this->searchType] = $this->search;
        }
        
        if ($this->selectedLanguage) {
            $filters['language_name'] = $this->selectedLanguage;
        }
        
        if ($this->selectedType) {
            $filters['worksheet_name'] = $this->selectedType;
        }
        
        $data = $khastaraService->getCollectionList($filters, $this->getPage(), 12);
        
        if ($data && isset($data['data'])) {
            return [
                'data' => collect($data['data'])->map(function ($item) {
                    return [
                        'id' => $item['catalog_id'] ?? '',
                        'title' => $item['title'] ?? 'Tanpa Judul',
                        'cover' => $item['cover_utama'] ?? '/assets/images/placeholder/manuscript.jpg',
                        'date' => $item['create_date'] ?? '',
                        'type' => $item['worksheet_name'] ?? 'Naskah',
                        'language' => $item['language_name'] ?? '',
                        'url' => 'https://khastara.perpusnas.go.id/koleksi-digital/detail?catId=' . ($item['catalog_id'] ?? '')
                    ];
                }),
                'total' => $data['meta']['total'] ?? 0,
                'per_page' => $data['meta']['per_page'] ?? 12
            ];
        }
        
        return [
            'data' => collect(),
            'total' => 0,
            'per_page' => 12
        ];
    }
    
    public function render()
    {
        return view('livewire.opac.khastara-browse')
            ->layout('components.opac.layout', [
                'title' => 'Naskah Nusantara - Khastara Perpusnas',
                'description' => 'Jelajahi koleksi naskah kuno dan warisan dokumenter Nusantara dari Perpustakaan Nasional'
            ]);
    }
}
