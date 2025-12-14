<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShamelaService
{
    protected string $baseUrl = 'https://shamela.ws';
    
    // Shamela categories mapping
    protected array $categories = [
        1 => 'العقيدة',
        2 => 'الفرق والردود',
        3 => 'التفسير',
        4 => 'علوم القرآن',
        5 => 'التجويد والقراءات',
        6 => 'كتب السنة',
        7 => 'شروح الحديث',
        8 => 'التخريج والأطراف',
        9 => 'العلل والسؤلات',
        10 => 'علوم الحديث',
        11 => 'أصول الفقه',
        12 => 'القواعد الفقهية',
        14 => 'الفقه الحنفي',
        15 => 'الفقه المالكي',
        16 => 'الفقه الشافعي',
        17 => 'الفقه الحنبلي',
        18 => 'الفقه العام',
        19 => 'مسائل فقهية',
        22 => 'الفتاوى',
        23 => 'الرقائق والآداب',
        24 => 'السيرة النبوية',
        25 => 'التاريخ',
        26 => 'التراجم والطبقات',
        29 => 'كتب اللغة',
        31 => 'النحو والصرف',
        32 => 'الأدب',
    ];

    /**
     * Search books by category (predefined results)
     */
    public function searchByCategory(int $categoryId, int $limit = 20): Collection
    {
        $cacheKey = "shamela_category_{$categoryId}_{$limit}";
        
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($categoryId, $limit) {
            try {
                $response = Http::timeout(15)->get("{$this->baseUrl}/category/{$categoryId}");
                
                if (!$response->successful()) {
                    return collect();
                }
                
                return $this->parseBookListHtml($response->body(), $limit);
            } catch (\Exception $e) {
                Log::error("Shamela category fetch failed: " . $e->getMessage());
                return collect();
            }
        });
    }

    /**
     * Get featured/recent books from Shamela homepage
     */
    public function getFeaturedBooks(int $limit = 20): Collection
    {
        $cacheKey = "shamela_featured_{$limit}";
        
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($limit) {
            try {
                $response = Http::timeout(15)->get($this->baseUrl);
                
                if (!$response->successful()) {
                    return collect();
                }
                
                return $this->parseBookListHtml($response->body(), $limit);
            } catch (\Exception $e) {
                Log::error("Shamela homepage fetch failed: " . $e->getMessage());
                return collect();
            }
        });
    }

    /**
     * Get book details by ID
     */
    public function getBook(int $bookId): ?array
    {
        $cacheKey = "shamela_book_{$bookId}";
        
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($bookId) {
            try {
                $response = Http::timeout(15)->get("{$this->baseUrl}/book/{$bookId}");
                
                if (!$response->successful()) {
                    return null;
                }
                
                return $this->parseBookDetailHtml($response->body(), $bookId);
            } catch (\Exception $e) {
                Log::error("Shamela book fetch failed: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Get cover URL for a book
     */
    public function getCoverUrl(int $bookId): string
    {
        return "{$this->baseUrl}/covers/{$bookId}.jpg";
    }

    /**
     * Get book URL on Shamela
     */
    public function getBookUrl(int $bookId): string
    {
        return "{$this->baseUrl}/book/{$bookId}";
    }

    /**
     * Parse book list from HTML
     */
    protected function parseBookListHtml(string $html, int $limit): Collection
    {
        $books = collect();
        
        // Match book links: /book/{id}
        preg_match_all('/<a[^>]*href="\/book\/(\d+)"[^>]*>([^<]+)<\/a>/u', $html, $matches, PREG_SET_ORDER);
        
        foreach (array_slice($matches, 0, $limit) as $match) {
            $bookId = (int) $match[1];
            $title = trim(strip_tags($match[2]));
            
            // Skip navigation links
            if (mb_strlen($title) < 5) continue;
            
            $books->push([
                'id' => $bookId,
                'title' => $title,
                'cover' => $this->getCoverUrl($bookId),
                'url' => $this->getBookUrl($bookId),
            ]);
        }
        
        return $books->unique('id')->take($limit);
    }

    /**
     * Parse book detail from HTML
     */
    protected function parseBookDetailHtml(string $html, int $bookId): array
    {
        $title = '';
        $author = '';
        $authorId = null;
        $category = '';
        $categoryId = null;
        
        // Extract title from <title> tag
        if (preg_match('/<title>([^-<]+)/u', $html, $match)) {
            $title = trim(str_replace('- المكتبة الشاملة', '', $match[1]));
        }
        
        // Extract author
        if (preg_match('/href="\/author\/(\d+)"[^>]*>([^<]+)/u', $html, $match)) {
            $authorId = (int) $match[1];
            $author = trim($match[2]);
        }
        
        // Extract category
        if (preg_match('/href="\/category\/(\d+)"[^>]*>([^<]+)/u', $html, $match)) {
            $categoryId = (int) $match[1];
            $category = trim($match[2]);
        }
        
        // Extract table of contents
        $toc = [];
        preg_match_all('/href="\/book\/' . $bookId . '\/(\d+)"[^>]*>([^<]+)/u', $html, $matches, PREG_SET_ORDER);
        foreach (array_slice($matches, 0, 50) as $match) {
            $toc[] = [
                'page' => (int) $match[1],
                'title' => trim($match[2]),
            ];
        }
        
        return [
            'id' => $bookId,
            'title' => $title ?: "كتاب #{$bookId}",
            'author' => $author,
            'author_id' => $authorId,
            'category' => $category,
            'category_id' => $categoryId,
            'cover' => $this->getCoverUrl($bookId),
            'url' => $this->getBookUrl($bookId),
            'toc' => $toc,
        ];
    }

    /**
     * Get all categories
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * Simple keyword search using predefined popular books
     */
    public function search(string $query, int $limit = 20): Collection
    {
        // For now, search by fetching category and filtering
        // In future, could implement actual search API if available
        
        $results = collect();
        $query = mb_strtolower($query);
        
        // Search term to category mapping
        $categoryMapping = [
            'حديث' => [6, 7],
            'سنة' => [6],
            'hadith' => [6, 7],
            'فقه' => [14, 15, 16, 17, 18],
            'fiqh' => [14, 15, 16, 17, 18],
            'تفسير' => [3, 4],
            'tafsir' => [3, 4],
            'quran' => [3, 4, 5],
            'قرآن' => [3, 4, 5],
            'سيرة' => [24],
            'sirah' => [24],
            'عقيدة' => [1],
            'aqidah' => [1],
            'تاريخ' => [25, 26],
            'history' => [25, 26],
            'لغة' => [29, 31],
            'نحو' => [31],
            'أدب' => [32],
        ];
        
        $categoriesToSearch = [];
        foreach ($categoryMapping as $term => $cats) {
            if (str_contains($query, $term)) {
                $categoriesToSearch = array_merge($categoriesToSearch, $cats);
            }
        }
        
        // Default to hadith and fiqh if no match
        if (empty($categoriesToSearch)) {
            $categoriesToSearch = [6, 7, 18];
        }
        
        $categoriesToSearch = array_unique(array_slice($categoriesToSearch, 0, 3));
        
        foreach ($categoriesToSearch as $catId) {
            $catBooks = $this->searchByCategory($catId, 10);
            $results = $results->merge($catBooks);
        }
        
        return $results->unique('id')->take($limit);
    }
}
