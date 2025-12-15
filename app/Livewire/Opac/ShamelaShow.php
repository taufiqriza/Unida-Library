<?php

namespace App\Livewire\Opac;

use App\Services\ShamelaLocalService;
use App\Services\ShamelaService;
use Livewire\Component;

class ShamelaShow extends Component
{
    public int $bookId;
    public ?array $book = null;
    public array $relatedBooks = [];
    public array $categories = [];
    public bool $loading = true;
    public bool $isLocalDatabase = false;
    public ?string $error = null;

    public function mount(int $id)
    {
        $this->bookId = $id;
        $this->loadBook();
        $this->loadRelatedBooks();
        $this->loadCategories();
        $this->loading = false;
    }

    public function loadBook(): void
    {
        try {
            // Try local database first
            $localService = new ShamelaLocalService();
            if ($localService->isAvailable()) {
                $this->book = $localService->getBook($this->bookId);
                $this->isLocalDatabase = true;
            }
            
            // Fallback to web service
            if (!$this->book) {
                $shamelaService = new ShamelaService();
                $this->book = $shamelaService->getBook($this->bookId);
                $this->isLocalDatabase = false;
            }
            
            if (!$this->book) {
                $this->error = 'كتاب غير موجود';
            }
        } catch (\Exception $e) {
            $this->error = 'فشل تحميل الكتاب: ' . $e->getMessage();
        }
    }

    public function loadRelatedBooks(): void
    {
        if (!$this->book) return;
        
        $categoryId = $this->book['category_id'] ?? null;
        if (!$categoryId) return;
        
        $localService = new ShamelaLocalService();
        if ($localService->isAvailable()) {
            $result = $localService->getBooksByCategory($categoryId, 6);
            // Filter out current book
            $this->relatedBooks = collect($result['results'] ?? [])
                ->filter(fn($b) => $b['id'] !== $this->bookId)
                ->take(5)
                ->values()
                ->toArray();
        }
    }

    public function loadCategories(): void
    {
        $localService = new ShamelaLocalService();
        if ($localService->isAvailable()) {
            $this->categories = array_slice($localService->getCategories(), 0, 20);
        }
    }
    
    /**
     * Check if book has content in SQLite database (for reader)
     */
    public function getHasContentProperty(): bool
    {
        $contentService = new \App\Services\ShamelaContentService();
        return $contentService->hasContent($this->bookId);
    }

    public function render()
    {
        return view('livewire.opac.shamela-show')
            ->layout('components.opac.layout', [
                'title' => $this->book['title'] ?? 'كتاب شاملة',
            ]);
    }
}
