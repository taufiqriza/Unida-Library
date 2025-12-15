<?php

namespace App\Livewire\Opac;

use App\Services\ShamelaContentService;
use App\Services\ShamelaLocalService;
use Livewire\Component;

class ShamelaReader extends Component
{
    public int $bookId;
    public int $currentPage = 1;
    public int $totalPages = 0;
    public ?array $pageData = null;
    public ?array $bookInfo = null;
    public bool $loading = true;
    public bool $showModal = false;
    public string $error = '';
    
    protected $listeners = ['openReader' => 'openBook'];
    
    public function mount(int $bookId = 0)
    {
        $this->bookId = $bookId;
    }
    
    public function openBook(int $bookId, int $page = 1)
    {
        $this->bookId = $bookId;
        $this->currentPage = $page;
        $this->showModal = true;
        $this->loadPage();
    }
    
    public function loadPage()
    {
        $this->loading = true;
        $this->error = '';
        
        $contentService = new ShamelaContentService();
        $localService = new ShamelaLocalService();
        
        if (!$contentService->isAvailable()) {
            $this->error = 'Database konten belum tersedia. Silakan tunggu proses konversi selesai.';
            $this->loading = false;
            return;
        }
        
        // Get book info
        $this->bookInfo = $localService->getBook($this->bookId);
        
        if (!$this->bookInfo) {
            $this->error = 'Buku tidak ditemukan.';
            $this->loading = false;
            return;
        }
        
        // Get page range
        $range = $contentService->getBookPageRange($this->bookId);
        $this->totalPages = $range['max'];
        
        // Clamp current page
        $this->currentPage = max($range['min'], min($this->currentPage, $range['max']));
        
        // Get page content
        $this->pageData = $contentService->getPage($this->bookId, $this->currentPage);
        
        if (!$this->pageData) {
            // Try to find nearest page
            $pages = $contentService->getBookPages($this->bookId, 1);
            if (!empty($pages)) {
                $this->pageData = $pages[0];
                $this->currentPage = $this->pageData['page_num'];
            } else {
                $this->error = 'Konten halaman tidak ditemukan untuk buku ini.';
            }
        }
        
        $this->loading = false;
    }
    
    public function nextPage()
    {
        if ($this->currentPage < $this->totalPages) {
            $this->currentPage++;
            $this->loadPage();
        }
    }
    
    public function prevPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            $this->loadPage();
        }
    }
    
    public function goToPage(int $page)
    {
        $this->currentPage = $page;
        $this->loadPage();
    }
    
    public function closeModal()
    {
        $this->showModal = false;
        $this->pageData = null;
    }
    
    public function render()
    {
        return view('livewire.opac.shamela-reader');
    }
}
