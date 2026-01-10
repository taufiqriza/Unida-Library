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
     * DDC Search - Supports code and description search
     */
    public function search(string $query, int $limit = 300): array
    {
        $query = trim($query);
        if ($query === '') return [];
        
        $all = $this->all();
        
        // Code-based search (numbers, X, dots)
        if (preg_match('/^[0-9xX.]+$/', $query)) {
            $results = $this->searchByCode($all, $query);
        } else {
            $results = $this->searchByDescription($all, $query);
        }
        
        return array_slice($results, 0, $limit);
    }
    
    /**
     * SMART CODE SEARCH - Follows DDC hierarchy perfectly
     */
    private function searchByCode(array $all, string $query): array
    {
        $exact = [];
        $hierarchical = [];
        $partial = [];
        
        foreach ($all as $item) {
            $code = $item['code'];
            
            // EXACT MATCH (highest priority)
            if ($code === $query) {
                $exact[] = $item;
                continue;
            }
            
            // HIERARCHICAL MATCH (DDC standard)
            if ($this->isHierarchicalMatch($code, $query)) {
                $hierarchical[] = $item;
                continue;
            }
            
            // PARTIAL MATCH (contains)
            if (str_contains($code, $query)) {
                $partial[] = $item;
            }
        }
        
        return array_merge($exact, $hierarchical, $partial);
    }
    
    /**
     * PERFECT DDC HIERARCHY MATCHING
     */
    private function isHierarchicalMatch(string $code, string $query): bool
    {
        // Remove dots for clean comparison
        $cleanCode = str_replace('.', '', $code);
        $cleanQuery = str_replace('.', '', $query);
        
        // DDC HIERARCHY RULES (INTERNATIONAL STANDARD):
        
        // 1. MAIN CLASS: "0" matches all 0xx codes
        if (strlen($cleanQuery) === 1) {
            return str_starts_with($cleanCode, $cleanQuery);
        }
        
        // 2. DIVISION: "00" matches all 00x codes  
        if (strlen($cleanQuery) === 2) {
            return str_starts_with($cleanCode, $cleanQuery);
        }
        
        // 3. SECTION: "000" matches all 000.x codes
        if (strlen($cleanQuery) === 3) {
            return str_starts_with($cleanCode, $cleanQuery);
        }
        
        // 4. SUBSECTION: "000.1" matches all 000.1x codes
        return str_starts_with($cleanCode, $cleanQuery);
    }
    
    /**
     * SMART DESCRIPTION SEARCH with relevance scoring
     */
    private function searchByDescription(array $all, string $query): array
    {
        $terms = array_filter(explode(' ', strtolower($query)), fn($t) => strlen($t) >= 2);
        if (empty($terms)) return [];
        
        $scored = [];
        
        foreach ($all as $item) {
            $desc = strtolower($item['description']);
            $score = 0;
            
            foreach ($terms as $term) {
                // EXACT WORD MATCH (highest score)
                if (preg_match('/\b' . preg_quote($term, '/') . '\b/', $desc)) {
                    $score += 100;
                }
                // PARTIAL MATCH (medium score)
                elseif (str_contains($desc, $term)) {
                    $score += 50;
                }
            }
            
            if ($score > 0) {
                $item['_score'] = $score;
                $scored[] = $item;
            }
        }
        
        // Sort by relevance score (highest first)
        usort($scored, fn($a, $b) => $b['_score'] <=> $a['_score']);
        
        return $scored;
    }

    /**
     * Sort results by relevance to query
     */
    protected function sortByRelevance(array &$results, string $query): void
    {
        usort($results, function($a, $b) use ($query) {
            // Prefer shorter codes (more general classifications)
            $aLen = strlen($a['code']);
            $bLen = strlen($b['code']);
            
            if ($aLen !== $bLen) {
                return $aLen <=> $bLen;
            }
            
            // Then sort by code numerically
            return strcmp($a['code'], $b['code']);
        });
    }

    /**
     * Check if DDC codes are related (broader/narrower relationships)
     */
    protected function isRelatedDdcCode(string $query, string $code): bool
    {
        // Remove dots for comparison
        $queryClean = str_replace('.', '', $query);
        $codeClean = str_replace('.', '', $code);
        
        // Check if one is a subdivision of the other
        if (strlen($queryClean) >= 3 && strlen($codeClean) >= 3) {
            $queryBase = substr($queryClean, 0, 3);
            $codeBase = substr($codeClean, 0, 3);
            
            // Same base class (e.g., 297 and 298 are related)
            if (abs((int)$queryBase - (int)$codeBase) <= 1) {
                return true;
            }
        }
        
        return false;
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
