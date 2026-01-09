<?php

namespace App\Services;

use Heyitsmi\ContentGuard\Facades\ContentGuard;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Enhanced Content Filter Service
 * 
 * Provides advanced content filtering capabilities
 * with caching and performance optimizations
 */
class ContentFilterService
{
    protected int $cacheMinutes = 60;
    
    /**
     * Check if content contains inappropriate words
     */
    public function hasBadWords(string $content): bool
    {
        if (empty(trim($content)) || strlen($content) < 3) {
            return false;
        }

        $cacheKey = 'content_check_' . md5($content);
        
        return Cache::remember($cacheKey, $this->cacheMinutes, function () use ($content) {
            try {
                return ContentGuard::hasBadWords($content);
            } catch (\Exception $e) {
                Log::warning('ContentGuard check failed', [
                    'error' => $e->getMessage(),
                    'content_length' => strlen($content)
                ]);
                return false; // Fail open for availability
            }
        });
    }

    /**
     * Sanitize content by replacing bad words
     */
    public function sanitize(string $content): string
    {
        if (empty(trim($content))) {
            return $content;
        }

        try {
            return ContentGuard::sanitize($content);
        } catch (\Exception $e) {
            Log::warning('ContentGuard sanitize failed', [
                'error' => $e->getMessage(),
                'content_length' => strlen($content)
            ]);
            return $content; // Return original on error
        }
    }

    /**
     * Batch check multiple content items
     */
    public function batchCheck(array $contents): array
    {
        $results = [];
        
        foreach ($contents as $key => $content) {
            if (is_string($content)) {
                $results[$key] = $this->hasBadWords($content);
            }
        }
        
        return $results;
    }

    /**
     * Get content filtering statistics
     */
    public function getStats(): array
    {
        $logPath = storage_path('logs/security.log');
        
        if (!file_exists($logPath)) {
            return [
                'total_violations' => 0,
                'today_violations' => 0,
                'most_common_field' => null
            ];
        }

        $content = file_get_contents($logPath);
        $today = now()->format('Y-m-d');
        
        return [
            'total_violations' => substr_count($content, 'Content filter violation'),
            'today_violations' => substr_count($content, $today . ' ') && substr_count($content, 'Content filter violation'),
            'most_common_field' => $this->extractMostCommonField($content)
        ];
    }

    protected function extractMostCommonField(string $logContent): ?string
    {
        preg_match_all('/"field":"([^"]+)"/', $logContent, $matches);
        
        if (empty($matches[1])) {
            return null;
        }

        $fieldCounts = array_count_values($matches[1]);
        arsort($fieldCounts);
        
        return array_key_first($fieldCounts);
    }
}
