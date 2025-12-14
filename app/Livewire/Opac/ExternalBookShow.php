<?php

namespace App\Livewire\Opac;

use App\Services\OpenLibraryService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class ExternalBookShow extends Component
{
    public ?string $source = null;
    public ?string $bookId = null;
    public ?array $book = null;
    public bool $loading = true;
    public ?string $error = null;

    public function mount(string $source, string $id)
    {
        $this->source = $source;
        $this->bookId = $id;
        $this->loadBook();
    }

    public function loadBook(): void
    {
        $this->loading = true;
        $this->error = null;

        try {
            if ($this->source === 'openlibrary') {
                $this->book = $this->loadFromOpenLibrary();
            }
            
            if (!$this->book) {
                $this->error = 'Buku tidak ditemukan';
            }
        } catch (\Exception $e) {
            $this->error = 'Gagal memuat data: ' . $e->getMessage();
        }

        $this->loading = false;
    }

    protected function loadFromOpenLibrary(): ?array
    {
        $cacheKey = 'openlibrary_book_' . $this->bookId;
        
        return Cache::remember($cacheKey, 3600, function () {
            // Try work endpoint first
            if (str_starts_with($this->bookId, '/works/')) {
                $response = Http::timeout(15)
                    ->withHeaders(['User-Agent' => 'UNIDA-Library/1.0'])
                    ->get("https://openlibrary.org{$this->bookId}.json");
                    
                if ($response->successful()) {
                    return $this->parseOpenLibraryWork($response->json());
                }
            }
            
            // Try ISBN endpoint
            if (strlen(preg_replace('/[^0-9X]/', '', $this->bookId)) >= 10) {
                $isbn = preg_replace('/[^0-9X]/', '', $this->bookId);
                $response = Http::timeout(15)
                    ->withHeaders(['User-Agent' => 'UNIDA-Library/1.0'])
                    ->get("https://openlibrary.org/isbn/{$isbn}.json");
                    
                if ($response->successful()) {
                    return $this->parseOpenLibraryEdition($response->json());
                }
            }
            
            // Fallback: search by ID
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'UNIDA-Library/1.0'])
                ->get("https://openlibrary.org/search.json", [
                    'q' => $this->bookId,
                    'limit' => 1,
                ]);
                
            if ($response->successful()) {
                $docs = $response->json()['docs'] ?? [];
                if (!empty($docs)) {
                    return $this->parseOpenLibrarySearch($docs[0]);
                }
            }
            
            return null;
        });
    }

    protected function parseOpenLibraryWork(array $data): array
    {
        $coverId = $data['covers'][0] ?? null;
        
        // Get authors
        $authors = [];
        if (!empty($data['authors'])) {
            foreach (array_slice($data['authors'], 0, 5) as $author) {
                $authorKey = $author['author']['key'] ?? null;
                if ($authorKey) {
                    $authorData = Cache::remember("openlibrary_author_{$authorKey}", 3600, function () use ($authorKey) {
                        $resp = Http::timeout(10)
                            ->withHeaders(['User-Agent' => 'UNIDA-Library/1.0'])
                            ->get("https://openlibrary.org{$authorKey}.json");
                        return $resp->successful() ? $resp->json() : null;
                    });
                    if ($authorData) {
                        $authors[] = $authorData['name'] ?? 'Unknown';
                    }
                }
            }
        }

        return [
            'title' => $data['title'] ?? 'Unknown',
            'authors' => $authors,
            'description' => is_array($data['description'] ?? null) 
                ? ($data['description']['value'] ?? '') 
                : ($data['description'] ?? ''),
            'subjects' => array_slice($data['subjects'] ?? [], 0, 10),
            'cover_url' => $coverId ? "https://covers.openlibrary.org/b/id/{$coverId}-L.jpg" : null,
            'cover_medium' => $coverId ? "https://covers.openlibrary.org/b/id/{$coverId}-M.jpg" : null,
            'first_publish_date' => $data['first_publish_date'] ?? null,
            'key' => $data['key'] ?? null,
            'url' => "https://openlibrary.org" . ($data['key'] ?? ''),
            'source' => 'Open Library',
        ];
    }

    protected function parseOpenLibraryEdition(array $data): array
    {
        $coverId = $data['covers'][0] ?? null;
        
        return [
            'title' => $data['title'] ?? 'Unknown',
            'authors' => [], // Would need additional API call
            'description' => is_array($data['description'] ?? null) 
                ? ($data['description']['value'] ?? '') 
                : ($data['description'] ?? 'Tidak ada deskripsi.'),
            'subjects' => array_slice($data['subjects'] ?? [], 0, 10),
            'cover_url' => $coverId ? "https://covers.openlibrary.org/b/id/{$coverId}-L.jpg" : null,
            'cover_medium' => $coverId ? "https://covers.openlibrary.org/b/id/{$coverId}-M.jpg" : null,
            'isbn_10' => $data['isbn_10'][0] ?? null,
            'isbn_13' => $data['isbn_13'][0] ?? null,
            'publishers' => $data['publishers'] ?? [],
            'publish_date' => $data['publish_date'] ?? null,
            'number_of_pages' => $data['number_of_pages'] ?? null,
            'key' => $data['key'] ?? null,
            'url' => "https://openlibrary.org" . ($data['key'] ?? ''),
            'source' => 'Open Library',
        ];
    }

    protected function parseOpenLibrarySearch(array $data): array
    {
        $coverId = $data['cover_i'] ?? null;
        $key = $data['key'] ?? null;
        
        return [
            'title' => $data['title'] ?? 'Unknown',
            'authors' => $data['author_name'] ?? [],
            'description' => null,
            'subjects' => array_slice($data['subject'] ?? [], 0, 10),
            'cover_url' => $coverId ? "https://covers.openlibrary.org/b/id/{$coverId}-L.jpg" : null,
            'cover_medium' => $coverId ? "https://covers.openlibrary.org/b/id/{$coverId}-M.jpg" : null,
            'isbn' => $data['isbn'][0] ?? null,
            'publishers' => $data['publisher'] ?? [],
            'first_publish_year' => $data['first_publish_year'] ?? null,
            'key' => $key,
            'url' => $key ? "https://openlibrary.org{$key}" : null,
            'source' => 'Open Library',
            'ebook_access' => $data['ebook_access'] ?? null,
            'has_fulltext' => $data['has_fulltext'] ?? false,
            'ia' => $data['ia'] ?? [], // Internet Archive IDs
        ];
    }

    public function getReadUrlProperty(): ?string
    {
        if (!$this->book) {
            return null;
        }
        
        // Check for Internet Archive access
        if (!empty($this->book['ia'])) {
            $iaId = $this->book['ia'][0];
            return "https://archive.org/details/{$iaId}";
        }
        
        // Check for ebook access
        if (($this->book['ebook_access'] ?? null) === 'borrowable') {
            return $this->book['url'] ?? null;
        }
        
        return $this->book['url'] ?? null;
    }

    public function getBorrowableProperty(): bool
    {
        return ($this->book['ebook_access'] ?? null) === 'borrowable' 
            || ($this->book['has_fulltext'] ?? false)
            || !empty($this->book['ia']);
    }

    public function render()
    {
        return view('livewire.opac.external-book-show')
            ->layout('components.opac.layout', [
                'title' => $this->book['title'] ?? 'Detail Buku External',
            ]);
    }
}
