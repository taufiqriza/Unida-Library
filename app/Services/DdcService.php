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
     * Enhanced search DDC by code or description with better ranking
     * Supports e-DDC Edition 23 features
     */
    public function search(string $query, int $limit = 50): array
    {
        $query = strtolower(trim($query));
        $isCodeSearch = preg_match('/^[0-9xX.]+$/', $query);
        
        // Handle special cases
        if ($query === '2x' || $query === '2X') {
            // Keep 2X as is for Islamic classifications
            $query = '2x';
        }
        
        $exactMatches = [];
        $startsWithMatches = [];
        $sameClassMatches = [];
        $containsMatches = [];
        $relatedMatches = [];
        
        // Split search terms for multi-word search
        $searchTerms = array_filter(explode(' ', $query), fn($term) => strlen($term) >= 2);
        
        foreach ($this->all() as $item) {
            $code = strtolower($item['code']);
            $desc = strtolower($item['description']);
            
            // Code-based search
            if ($isCodeSearch) {
                if ($code === $query) {
                    $exactMatches[] = $item;
                } elseif (str_starts_with($code, $query)) {
                    $startsWithMatches[] = $item;
                } elseif (str_contains($code, $query)) {
                    $containsMatches[] = $item;
                }
                continue;
            }
            
            // Multi-term description search
            if (!empty($searchTerms)) {
                $matchCount = 0;
                $exactWordMatches = 0;
                
                foreach ($searchTerms as $term) {
                    if (str_contains($desc, $term)) {
                        $matchCount++;
                        // Check for exact word match
                        if (preg_match('/\b' . preg_quote($term, '/') . '\b/', $desc)) {
                            $exactWordMatches++;
                        }
                    }
                }
                
                // Categorize by match quality
                if ($matchCount === count($searchTerms)) {
                    if ($exactWordMatches === count($searchTerms)) {
                        $exactMatches[] = $item;
                    } else {
                        $startsWithMatches[] = $item;
                    }
                } elseif ($matchCount > 0) {
                    $containsMatches[] = $item;
                }
            }
        }
        
        // Combine results with priority
        $results = array_merge($exactMatches, $startsWithMatches, $sameClassMatches, $containsMatches, $relatedMatches);
        
        return array_slice($results, 0, $limit);
    }
            
            if ($isCodeSearch) {
                // Enhanced code search with DDC hierarchy
                if ($code === $query) {
                    $exactMatches[] = $item;
                } elseif (str_starts_with($code, $query . '.')) {
                    $startsWithMatches[] = $item;
                } elseif ($this->isSameDdcClass($query, $code)) {
                    $sameClassMatches[] = $item;
                } elseif ($this->isRelatedDdcCode($query, $code)) {
                    $relatedMatches[] = $item;
                }
            } else {
                // Enhanced text search with multiple criteria
                $searchTerms = explode(' ', $query);
                $matchScore = 0;
                
                // Check for exact code match
                if ($code === $query) {
                    $exactMatches[] = $item;
                    continue;
                }
                
                // Check for code starts with
                if (str_starts_with($code, $query)) {
                    $startsWithMatches[] = $item;
                    continue;
                }
                
                // Multi-term search in description
                foreach ($searchTerms as $term) {
                    if (strlen($term) >= 2) {
                        if (str_contains($desc, $term)) {
                            $matchScore++;
                        }
                    }
                }
                
                // Categorize by match quality
                if ($matchScore === count($searchTerms) && count($searchTerms) > 1) {
                    // All terms found - high relevance
                    $startsWithMatches[] = $item;
                } elseif ($matchScore > 0) {
                    // Some terms found
                    $containsMatches[] = $item;
                }
            }
        }
        
        // Enhanced sorting with relevance scoring
        $this->sortByRelevance($exactMatches, $query);
        $this->sortByRelevance($startsWithMatches, $query);
        $this->sortByRelevance($sameClassMatches, $query);
        $this->sortByRelevance($containsMatches, $query);
        $this->sortByRelevance($relatedMatches, $query);
        
        // Merge with priority: exact > starts-with > same-class > contains > related
        $results = array_merge(
            $exactMatches, 
            $startsWithMatches, 
            $sameClassMatches, 
            $containsMatches,
            $relatedMatches
        );
        
        return array_slice($results, 0, $limit);
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
