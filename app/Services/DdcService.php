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
     * Enhanced DDC search with proper Dewey Decimal Classification logic
     * Supports hierarchical classification and smart matching
     */
    public function search(string $query, int $limit = 100): array
    {
        $originalQuery = trim($query);
        $query = strtolower($originalQuery);
        $isCodeSearch = preg_match('/^[0-9xX.]+$/', $originalQuery);
        
        $exactMatches = [];
        $hierarchicalMatches = [];
        $startsWithMatches = [];
        $containsMatches = [];
        $descriptionMatches = [];
        
        // Split search terms for multi-word search
        $searchTerms = array_filter(explode(' ', $query), fn($term) => strlen($term) >= 2);
        
        foreach ($this->all() as $item) {
            $code = $item['code'];
            $desc = strtolower($item['description']);
            
            // Code-based search with DDC hierarchy logic
            if ($isCodeSearch) {
                if ($code === $originalQuery) {
                    $exactMatches[] = $item;
                } elseif ($this->isInDdcHierarchy($code, $originalQuery)) {
                    $hierarchicalMatches[] = $item;
                } elseif (str_starts_with($code, $originalQuery)) {
                    $startsWithMatches[] = $item;
                } elseif (str_contains($code, $originalQuery)) {
                    $containsMatches[] = $item;
                }
                continue;
            }
            
            // Description-based search with relevance scoring
            if (!empty($searchTerms)) {
                $matchScore = $this->calculateRelevanceScore($desc, $searchTerms);
                if ($matchScore > 0) {
                    $item['_score'] = $matchScore;
                    $descriptionMatches[] = $item;
                }
            }
        }
        
        // Sort description matches by relevance score
        usort($descriptionMatches, fn($a, $b) => $b['_score'] <=> $a['_score']);
        
        // Combine results with proper DDC hierarchy priority
        $results = array_merge(
            $exactMatches,
            $hierarchicalMatches,
            $startsWithMatches,
            $containsMatches,
            $descriptionMatches
        );
        
        return array_slice($results, 0, $limit);
    }
    
    /**
     * Check if a DDC code belongs to the hierarchy of another code
     * Implements proper DDC classification logic
     */
    private function isInDdcHierarchy(string $code, string $parentCode): bool
    {
        // Remove dots for comparison
        $cleanCode = str_replace('.', '', $code);
        $cleanParent = str_replace('.', '', $parentCode);
        
        // DDC Hierarchy Rules:
        // 1. Main class (0, 1, 2, etc.) includes all subclasses
        // 2. Division (00, 10, 20, etc.) includes all subdivisions
        // 3. Section (000, 100, 200, etc.) includes all subsections
        
        if (strlen($cleanParent) === 1) {
            // Main class search (0, 1, 2, etc.)
            return str_starts_with($cleanCode, $cleanParent);
        } elseif (strlen($cleanParent) === 2) {
            // Division search (00, 10, 20, etc.)
            return str_starts_with($cleanCode, $cleanParent);
        } elseif (strlen($cleanParent) === 3) {
            // Section search (000, 100, 200, etc.)
            return str_starts_with($cleanCode, $cleanParent);
        }
        
        return false;
    }
    
    /**
     * Calculate relevance score for description matching
     */
    private function calculateRelevanceScore(string $description, array $searchTerms): float
    {
        $score = 0;
        $totalTerms = count($searchTerms);
        
        foreach ($searchTerms as $term) {
            // Exact word match (highest score)
            if (preg_match('/\b' . preg_quote($term, '/') . '\b/i', $description)) {
                $score += 10;
            }
            // Partial match (lower score)
            elseif (str_contains($description, $term)) {
                $score += 5;
            }
        }
        
        // Bonus for matching all terms
        $matchedTerms = 0;
        foreach ($searchTerms as $term) {
            if (str_contains($description, $term)) {
                $matchedTerms++;
            }
        }
        
        if ($matchedTerms === $totalTerms) {
            $score += 20; // Bonus for complete match
        }
        
        return $score / $totalTerms; // Normalize by number of terms
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
