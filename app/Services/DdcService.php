<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class DdcService
{
    protected string $jsonPath;
    protected string $cacheKey = 'ddc_classifications';
    protected int $cacheTtl = 86400; // 24 hours

    public function __construct()
    {
        $this->jsonPath = database_path('data/ddc.json');
    }

    /**
     * Get all DDC classifications (cached)
     */
    public function all(): array
    {
        return Cache::remember($this->cacheKey, $this->cacheTtl, function () {
            if (File::exists($this->jsonPath)) {
                return json_decode(File::get($this->jsonPath), true) ?? [];
            }
            return [];
        });
    }

    /**
     * Search DDC by code or description
     */
    public function search(string $query, int $limit = 25): array
    {
        $query = strtolower($query);
        $results = [];
        
        foreach ($this->all() as $item) {
            if (str_contains(strtolower($item['code']), $query) || 
                str_contains(strtolower($item['description']), $query)) {
                $results[] = $item;
                if (count($results) >= $limit) break;
            }
        }
        
        usort($results, fn($a, $b) => strcmp($a['code'], $b['code']));
        
        return $results;
    }

    /**
     * Get DDC by code
     */
    public function find(string $code): ?array
    {
        foreach ($this->all() as $item) {
            if ($item['code'] === $code) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Clear cache
     */
    public function clearCache(): void
    {
        Cache::forget($this->cacheKey);
    }
}
