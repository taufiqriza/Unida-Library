<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KubukuService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $cacheTtl;
    protected int $searchLimit;
    protected bool $enabled;

    public function __construct()
    {
        // Read from database Setting first, fallback to env
        $this->enabled = (bool) Setting::get('kubuku_enabled', config('services.kubuku.enabled', true));
        $this->baseUrl = Setting::get('kubuku_api_url') ?: config('services.kubuku.base_url', 'https://kubuku.id/api/wl');
        $this->apiKey = Setting::get('kubuku_api_key') ?: config('services.kubuku.api_key', '');
        $this->cacheTtl = config('services.kubuku.cache_ttl', 3600);
        $this->searchLimit = config('services.kubuku.search_limit', 20);
    }

    /**
     * Check if KUBUKU integration is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->apiKey);
    }

    /**
     * Make authenticated API request
     */
    protected function request(string $method, string $endpoint, array $data = []): ?array
    {
        try {
            $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');

            $request = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => $this->apiKey,
                    'Accept' => 'application/json',
                ]);

            $response = match (strtoupper($method)) {
                'POST' => $request->post($url, $data),
                default => $request->get($url, $data),
            };

            if ($response->successful()) {
                $json = $response->json();
                
                // Check API response code
                if (isset($json['code']) && $json['code'] != 200) {
                    Log::warning('KUBUKU API error response', [
                        'endpoint' => $endpoint,
                        'code' => $json['code'],
                    ]);
                    return null;
                }
                
                return $json;
            }

            Log::warning('KUBUKU API request failed', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('KUBUKU API exception', [
                'endpoint' => $endpoint,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Search e-books by keyword
     * Endpoint: /content/search/{keyword}/{page}
     */
    public function search(string $query, int $page = 1): Collection
    {
        if (!$this->isEnabled() || empty(trim($query))) {
            return collect();
        }

        $cacheKey = 'kubuku_search_' . md5($query . '_' . $page);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($query, $page) {
            $endpoint = "/content/search/" . urlencode($query) . "/{$page}";
            $response = $this->request('GET', $endpoint);

            if (!$response || !isset($response['data'])) {
                return collect();
            }

            return collect($response['data'])->map(fn($item) => $this->transformBook($item));
        });
    }

    /**
     * Get search results count
     */
    public function getSearchCount(string $query): int
    {
        if (!$this->isEnabled() || empty(trim($query))) {
            return 0;
        }

        $cacheKey = 'kubuku_search_count_' . md5($query);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($query) {
            // Search first page to get total
            $endpoint = "/content/search/" . urlencode($query) . "/1";
            $response = $this->request('GET', $endpoint);

            return $response['total'] ?? count($response['data'] ?? []);
        });
    }

    /**
     * Get all e-books (paginated)
     * Endpoint: /content/all/{page}
     */
    public function getAll(int $page = 1): Collection
    {
        if (!$this->isEnabled()) {
            return collect();
        }

        $cacheKey = 'kubuku_all_' . $page;

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($page) {
            $response = $this->request('GET', "/content/all/{$page}");

            if (!$response || !isset($response['data'])) {
                return collect();
            }

            return collect($response['data'])->map(fn($item) => $this->transformBook($item));
        });
    }

    /**
     * Get total content count
     * Endpoint: /totalContent
     */
    public function getTotalCount(): int
    {
        if (!$this->isEnabled()) {
            return 0;
        }

        return Cache::remember('kubuku_total_count', $this->cacheTtl * 6, function () {
            $response = $this->request('GET', '/totalContent');
            return $response['total'] ?? $response['data'] ?? 0;
        });
    }

    /**
     * Get e-book by ID (search in paginated results)
     */
    public function getById(string $id): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $cacheKey = 'kubuku_book_' . $id;

        return Cache::remember($cacheKey, $this->cacheTtl * 24, function () use ($id) {
            // Try to find in first few pages
            for ($page = 1; $page <= 10; $page++) {
                $response = $this->request('GET', "/content/all/{$page}");
                
                if (!$response || !isset($response['data'])) {
                    break;
                }

                foreach ($response['data'] as $item) {
                    if (($item['id'] ?? null) == $id || ($item['id_konten'] ?? null) == $id) {
                        return $this->transformBook($item, true);
                    }
                }

                // No more pages
                if (count($response['data']) < 20) {
                    break;
                }
            }

            return null;
        });
    }

    /**
     * Get reading URL for authenticated user
     * Endpoint: /content/read (POST)
     * 
     * Note: User must have KUBUKU desktop app installed
     */
    public function getReadUrl(string $contentId, string $email, string $fullname): ?string
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $response = $this->request('POST', '/content/read', [
            'username' => $email,
            'id_konten' => $contentId,
            'fullname' => $fullname,
        ]);

        return $response['url'] ?? $response['data']['url'] ?? null;
    }

    /**
     * Get all categories
     * Endpoint: /category/all
     */
    public function getCategories(): Collection
    {
        if (!$this->isEnabled()) {
            return collect();
        }

        return Cache::remember('kubuku_categories', $this->cacheTtl * 24, function () {
            $response = $this->request('GET', '/category/all');
            return collect($response['data'] ?? []);
        });
    }

    /**
     * Get all subcategories
     * Endpoint: /sub_category/all
     */
    public function getSubCategories(): Collection
    {
        if (!$this->isEnabled()) {
            return collect();
        }

        return Cache::remember('kubuku_subcategories', $this->cacheTtl * 24, function () {
            $response = $this->request('GET', '/sub_category/all');
            return collect($response['data'] ?? []);
        });
    }

    /**
     * Get e-books by category
     * Endpoint: /content/category/{category}/{page}
     */
    public function getByCategory(string $category, int $page = 1): Collection
    {
        if (!$this->isEnabled()) {
            return collect();
        }

        $cacheKey = 'kubuku_category_' . md5($category) . '_' . $page;

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($category, $page) {
            $endpoint = "/content/category/" . urlencode($category) . "/{$page}";
            $response = $this->request('GET', $endpoint);

            if (!$response || !isset($response['data'])) {
                return collect();
            }

            return collect($response['data'])->map(fn($item) => $this->transformBook($item));
        });
    }

    /**
     * Get newest e-books
     * Endpoint: /content/new
     */
    public function getNewContent(): Collection
    {
        if (!$this->isEnabled()) {
            return collect();
        }

        return Cache::remember('kubuku_new_content', $this->cacheTtl, function () {
            $response = $this->request('GET', '/content/new');

            if (!$response || !isset($response['data'])) {
                return collect();
            }

            return collect($response['data'])->map(fn($item) => $this->transformBook($item));
        });
    }

    /**
     * Get user's last read books
     * Endpoint: /content/lastRead (GET with username param)
     */
    public function getLastRead(string $email): Collection
    {
        if (!$this->isEnabled()) {
            return collect();
        }

        $cacheKey = 'kubuku_lastread_' . md5($email);

        return Cache::remember($cacheKey, 300, function () use ($email) { // 5 min cache
            $response = $this->request('GET', '/content/lastRead', [
                'username' => $email,
            ]);

            if (!$response || !isset($response['data'])) {
                return collect();
            }

            return collect($response['data'])->map(fn($item) => $this->transformBook($item));
        });
    }

    /**
     * Transform API response to standardized format
     */
    protected function transformBook(array $item, bool $full = false): array
    {
        $id = $item['id'] ?? $item['id_konten'] ?? uniqid();
        
        $book = [
            'type' => 'ebook',
            'source' => 'kubuku',
            'id' => $id,
            'kubuku_id' => $id,
            'title' => $item['judul'] ?? $item['title'] ?? 'Untitled',
            'author' => $item['penulis'] ?? $item['author'] ?? '-',
            'cover' => $item['cover'] ?? $item['thumbnail'] ?? null,
            'year' => $item['tahun'] ?? $item['year'] ?? null,
            'publisher' => $item['penerbit'] ?? $item['publisher'] ?? null,
            'category' => $item['kategori'] ?? $item['category'] ?? null,
            'subcategory' => $item['sub_kategori'] ?? $item['subcategory'] ?? null,
            'isbn' => $item['isbn'] ?? null,
            'pages' => $item['halaman'] ?? $item['pages'] ?? null,
            'badge' => 'KUBUKU',
            'badgeColor' => 'emerald',
            'icon' => 'fa-book-reader',
            'url' => route('opac.ebook.kubuku.show', $id),
            'description' => isset($item['sinopsis']) 
                ? \Str::limit(strip_tags($item['sinopsis']), 150) 
                : null,
            'meta' => [
                'source' => 'KUBUKU E-Library',
                'category' => $item['kategori'] ?? null,
                'subcategory' => $item['sub_kategori'] ?? null,
            ],
        ];

        // Add full details if requested
        if ($full) {
            $book['synopsis'] = $item['sinopsis'] ?? null;
            $book['language'] = $item['bahasa'] ?? $item['language'] ?? 'Indonesia';
        }

        return $book;
    }

    /**
     * Clear all KUBUKU caches
     */
    public function clearCache(): void
    {
        Cache::forget('kubuku_total_count');
        Cache::forget('kubuku_categories');
        Cache::forget('kubuku_subcategories');
        Cache::forget('kubuku_new_content');
        
        // Note: Search and page caches will expire naturally
    }
}
