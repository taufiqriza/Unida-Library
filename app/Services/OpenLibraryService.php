<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenLibraryService
{
    protected const API_URL = 'https://openlibrary.org';
    protected const CACHE_TTL = 3600; // 1 hour
    
    /**
     * Check if Open Library integration is enabled
     */
    public function isEnabled(): bool
    {
        return (bool) Setting::get('openlibrary_enabled', true);
    }
    
    /**
     * Get search limit from settings
     */
    public function getSearchLimit(): int
    {
        return (int) Setting::get('openlibrary_search_limit', 10);
    }
    
    /**
     * Search books from Open Library API
     * 
     * @param string $query Search query
     * @param int|null $limit Override default limit
     * @return Collection
     */
    public function search(string $query, ?int $limit = null): Collection
    {
        if (!$this->isEnabled()) {
            return collect();
        }
        
        if (empty(trim($query))) {
            return collect();
        }
        
        $limit = $limit ?? $this->getSearchLimit();
        $cacheKey = 'openlibrary_search_' . md5($query . '_' . $limit);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($query, $limit) {
            return $this->fetchFromApi($query, $limit);
        });
    }
    
    /**
     * Fetch books from Open Library API
     */
    protected function fetchFromApi(string $query, int $limit): Collection
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'UNIDA-Library/1.0 (library@unida.gontor.ac.id)',
                ])
                ->get(self::API_URL . '/search.json', [
                    'q' => $query,
                    'limit' => $limit,
                    'fields' => 'key,title,author_name,first_publish_year,cover_i,isbn,publisher,subject,language',
                ]);
            
            if (!$response->successful()) {
                Log::warning('Open Library API error', [
                    'status' => $response->status(),
                    'query' => $query,
                ]);
                return collect();
            }
            
            $data = $response->json();
            $docs = $data['docs'] ?? [];
            
            return collect($docs)->map(function ($book) {
                return $this->transformBook($book);
            })->filter();
            
        } catch (\Exception $e) {
            Log::error('Open Library API exception', [
                'message' => $e->getMessage(),
                'query' => $query,
            ]);
            return collect();
        }
    }
    
    /**
     * Transform Open Library response to our format
     */
    protected function transformBook(array $book): ?array
    {
        $title = $book['title'] ?? null;
        if (!$title) {
            return null;
        }
        
        $coverId = $book['cover_i'] ?? null;
        $coverUrl = $coverId 
            ? "https://covers.openlibrary.org/b/id/{$coverId}-M.jpg"
            : null;
        
        $isbn = $book['isbn'][0] ?? null;
        $key = $book['key'] ?? null;
        
        // Build read URL
        $readUrl = $key ? self::API_URL . $key : null;
        if ($isbn) {
            $readUrl = self::API_URL . "/isbn/{$isbn}";
        }
        
        return [
            'type' => 'external',
            'source' => 'openlibrary',
            'id' => $key ?? $isbn ?? uniqid(),
            'title' => $title,
            'author' => is_array($book['author_name'] ?? null) 
                ? implode(', ', array_slice($book['author_name'], 0, 3))
                : ($book['author_name'] ?? '-'),
            'cover' => $coverUrl,
            'year' => $book['first_publish_year'] ?? null,
            'publisher' => is_array($book['publisher'] ?? null) 
                ? ($book['publisher'][0] ?? null)
                : ($book['publisher'] ?? null),
            'isbn' => $isbn,
            'badge' => 'Open Library',
            'badgeColor' => 'cyan',
            'icon' => 'fa-globe',
            'url' => $readUrl,
            'description' => null,
            'meta' => [
                'source' => 'Open Library',
                'languages' => is_array($book['language'] ?? null) 
                    ? array_slice($book['language'], 0, 3)
                    : [],
                'subjects' => is_array($book['subject'] ?? null) 
                    ? array_slice($book['subject'], 0, 5)
                    : [],
            ],
        ];
    }
    
    /**
     * Get book by ISBN
     */
    public function getByIsbn(string $isbn): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }
        
        $cacheKey = 'openlibrary_isbn_' . $isbn;
        
        return Cache::remember($cacheKey, self::CACHE_TTL * 24, function () use ($isbn) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders([
                        'User-Agent' => 'UNIDA-Library/1.0 (library@unida.gontor.ac.id)',
                    ])
                    ->get(self::API_URL . "/isbn/{$isbn}.json");
                
                if ($response->successful()) {
                    return $this->transformBook($response->json());
                }
                
                return null;
            } catch (\Exception $e) {
                Log::error('Open Library ISBN lookup failed', [
                    'isbn' => $isbn,
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        });
    }
}
