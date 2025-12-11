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
     * If query looks like a code (numeric), match DDC hierarchy
     * If query is text, search descriptions
     */
    public function search(string $query, int $limit = 25): array
    {
        $query = strtolower(trim($query));
        $isCodeSearch = preg_match('/^[0-9xX.]+$/', $query);
        
        $exactMatches = [];
        $startsWithMatches = [];
        $sameClassMatches = [];
        $containsMatches = [];
        
        foreach ($this->all() as $item) {
            $code = strtolower($item['code']);
            $desc = strtolower($item['description']);
            
            if ($isCodeSearch) {
                // Query looks like a code - match against code field with DDC hierarchy
                if ($code === $query) {
                    // Exact match (100 = 100)
                    $exactMatches[] = $item;
                } elseif (str_starts_with($code, $query . '.')) {
                    // Subdivision match (100 matches 100.1, 100.23)
                    $startsWithMatches[] = $item;
                } elseif ($this->isSameDdcClass($query, $code)) {
                    // Same DDC class (100 matches 101, 102, ... 109)
                    $sameClassMatches[] = $item;
                }
            } else {
                // Query is text - search in description, and also code
                if ($code === $query) {
                    $exactMatches[] = $item;
                } elseif (str_starts_with($code, $query)) {
                    $startsWithMatches[] = $item;
                } elseif (str_contains($desc, $query)) {
                    $containsMatches[] = $item;
                }
            }
        }
        
        // Sort each group by code
        usort($startsWithMatches, fn($a, $b) => strcmp($a['code'], $b['code']));
        usort($sameClassMatches, fn($a, $b) => strcmp($a['code'], $b['code']));
        usort($containsMatches, fn($a, $b) => strcmp($a['code'], $b['code']));
        
        // Merge: exact first, then subdivisions, then same class, then description matches
        $results = array_merge($exactMatches, $startsWithMatches, $sameClassMatches, $containsMatches);
        
        return array_slice($results, 0, $limit);
    }

    /**
     * Check if two DDC codes belong to the same class
     * e.g., 100 and 101 are same class (1xx), 100 and 200 are not
     */
    protected function isSameDdcClass(string $query, string $code): bool
    {
        // Clean query - remove dots and get base number
        $queryClean = str_replace('.', '', strtolower($query));
        $codeClean = str_replace('.', '', strtolower($code));
        
        // If query is main class (e.g., 100, 200, 2x)
        if (strlen($queryClean) <= 3) {
            // For 3-digit class like 100, match 100-109
            if (strlen($queryClean) === 3 && ctype_digit($queryClean[0])) {
                $queryPrefix = substr($queryClean, 0, 2); // "10" from "100"
                $codePrefix = substr($codeClean, 0, 2);
                return $queryPrefix === $codePrefix && strlen($codeClean) >= 3;
            }
            // For 2X type codes
            if (str_contains($queryClean, 'x')) {
                $queryPrefix = str_replace('x', '', $queryClean);
                return str_starts_with($codeClean, $queryPrefix);
            }
        }
        
        return false;
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
