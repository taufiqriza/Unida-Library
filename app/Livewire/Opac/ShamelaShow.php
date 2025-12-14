<?php

namespace App\Livewire\Opac;

use App\Services\ShamelaService;
use Livewire\Component;

class ShamelaShow extends Component
{
    public int $bookId;
    public ?array $book = null;
    public bool $loading = true;
    public ?string $error = null;

    public function mount(int $id)
    {
        $this->bookId = $id;
        $this->loadBook();
    }

    public function loadBook(): void
    {
        $this->loading = true;
        
        try {
            $service = app(ShamelaService::class);
            $this->book = $service->getBook($this->bookId);
            
            if (!$this->book) {
                $this->error = 'Kitab tidak ditemukan';
            }
        } catch (\Exception $e) {
            $this->error = 'Gagal memuat data kitab';
        }
        
        $this->loading = false;
    }

    public function render()
    {
        return view('livewire.opac.shamela-show')
            ->layout('components.opac.layout', [
                'title' => $this->book['title'] ?? 'Kitab Shamela',
            ]);
    }
}
