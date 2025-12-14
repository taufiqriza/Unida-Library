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
    
    // Popular books per category (cached list)
    protected array $popularBooks = [
        // كتب السنة (Hadith)
        6 => [
            ['id' => 1680, 'title' => 'صحيح البخاري'],
            ['id' => 1681, 'title' => 'صحيح مسلم'],
            ['id' => 1673, 'title' => 'سنن أبي داود'],
            ['id' => 1672, 'title' => 'سنن الترمذي'],
            ['id' => 1674, 'title' => 'سنن النسائي'],
            ['id' => 1675, 'title' => 'سنن ابن ماجه'],
            ['id' => 21493, 'title' => 'مسند الإمام أحمد'],
            ['id' => 1667, 'title' => 'موطأ مالك'],
            ['id' => 1684, 'title' => 'سنن الدارمي'],
            ['id' => 5722, 'title' => 'المعجم الكبير للطبراني'],
        ],
        // شروح الحديث
        7 => [
            ['id' => 52, 'title' => 'فتح الباري شرح صحيح البخاري'],
            ['id' => 9289, 'title' => 'عمدة القاري شرح صحيح البخاري'],
            ['id' => 3088, 'title' => 'شرح النووي على مسلم'],
            ['id' => 6526, 'title' => 'عون المعبود شرح سنن أبي داود'],
            ['id' => 6527, 'title' => 'تحفة الأحوذي شرح سنن الترمذي'],
        ],
        // التفسير
        3 => [
            ['id' => 23, 'title' => 'تفسير الطبري'],
            ['id' => 11, 'title' => 'تفسير ابن كثير'],
            ['id' => 118, 'title' => 'تفسير القرطبي'],
            ['id' => 8310, 'title' => 'تفسير البغوي'],
            ['id' => 205, 'title' => 'تفسير السعدي'],
            ['id' => 130, 'title' => 'الكشاف للزمخشري'],
            ['id' => 12963, 'title' => 'تفسير الرازي'],
            ['id' => 18, 'title' => 'زاد المسير لابن الجوزي'],
        ],
        // العقيدة
        1 => [
            ['id' => 1301, 'title' => 'كتاب التوحيد'],
            ['id' => 826, 'title' => 'شرح العقيدة الطحاوية'],
            ['id' => 824, 'title' => 'العقيدة الواسطية'],
            ['id' => 7414, 'title' => 'مجموع الفتاوى لابن تيمية'],
            ['id' => 2289, 'title' => 'درء تعارض العقل والنقل'],
        ],
        // الفقه العام
        18 => [
            ['id' => 2095, 'title' => 'المغني لابن قدامة'],
            ['id' => 2050, 'title' => 'الأم للشافعي'],
            ['id' => 8361, 'title' => 'المجموع شرح المهذب'],
            ['id' => 8374, 'title' => 'روضة الطالبين'],
            ['id' => 2017, 'title' => 'المدونة الكبرى'],
        ],
        // الفقه الحنفي
        14 => [
            ['id' => 2177, 'title' => 'بدائع الصنائع'],
            ['id' => 2178, 'title' => 'رد المحتار على الدر المختار'],
            ['id' => 2188, 'title' => 'الهداية شرح البداية'],
        ],
        // الفقه المالكي
        15 => [
            ['id' => 2017, 'title' => 'المدونة الكبرى'],
            ['id' => 1301, 'title' => 'مختصر خليل'],
            ['id' => 22984, 'title' => 'الشرح الكبير للدردير'],
        ],
        // الفقه الشافعي
        16 => [
            ['id' => 2050, 'title' => 'الأم للشافعي'],
            ['id' => 8361, 'title' => 'المجموع شرح المهذب'], 
            ['id' => 285, 'title' => 'منهاج الطالبين'],
        ],
        // الفقه الحنبلي
        17 => [
            ['id' => 2095, 'title' => 'المغني لابن قدامة'],
            ['id' => 27869, 'title' => 'الإنصاف في معرفة الراجح'],
            ['id' => 3107, 'title' => 'كشاف القناع'],
        ],
        // السيرة النبوية
        24 => [
            ['id' => 2220, 'title' => 'سيرة ابن هشام'],
            ['id' => 20247, 'title' => 'السيرة النبوية لابن كثير'],
            ['id' => 3180, 'title' => 'زاد المعاد'],
            ['id' => 3097, 'title' => 'الشمائل المحمدية'],
        ],
        // التاريخ
        25 => [
            ['id' => 3315, 'title' => 'تاريخ الطبري'],
            ['id' => 8315, 'title' => 'البداية والنهاية'],
            ['id' => 3278, 'title' => 'الكامل في التاريخ'],
            ['id' => 3309, 'title' => 'تاريخ الإسلام للذهبي'],
        ],
        // التراجم والطبقات
        26 => [
            ['id' => 3477, 'title' => 'سير أعلام النبلاء'],
            ['id' => 3339, 'title' => 'تهذيب التهذيب'],
            ['id' => 3386, 'title' => 'تقريب التهذيب'],
            ['id' => 3459, 'title' => 'الطبقات الكبرى لابن سعد'],
        ],
        // النحو والصرف
        31 => [
            ['id' => 12038, 'title' => 'الكتاب لسيبويه'],
            ['id' => 11989, 'title' => 'أوضح المسالك إلى ألفية ابن مالك'],
            ['id' => 12148, 'title' => 'شرح ابن عقيل'],
        ],
        // الأدب
        32 => [
            ['id' => 11749, 'title' => 'الأغاني للأصفهاني'],
            ['id' => 11692, 'title' => 'البيان والتبيين'],
            ['id' => 11687, 'title' => 'الكامل في اللغة والأدب'],
        ],
    ];

    /**
     * Search books by category using predefined popular books
     */
    public function searchByCategory(int $categoryId, int $limit = 20): Collection
    {
        if (!isset($this->popularBooks[$categoryId])) {
            return collect();
        }
        
        return collect($this->popularBooks[$categoryId])
            ->take($limit)
            ->map(fn($book) => [
                'id' => $book['id'],
                'title' => $book['title'],
                'cover' => $this->getCoverUrl($book['id'], $book['title']),
                'url' => $this->getBookUrl($book['id']),
            ]);
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
     * Get cover URL for a book (fallback since Shamela covers not publicly accessible)
     */
    public function getCoverUrl(int $bookId, ?string $title = null): string
    {
        // Shamela doesn't have public cover URLs, use generated image
        $name = $title ? urlencode(mb_substr($title, 0, 10)) : 'كتاب';
        return "https://ui-avatars.com/api/?name={$name}&background=059669&color=fff&size=300&font-size=0.35&bold=true";
    }

    /**
     * Get book URL on Shamela
     */
    public function getBookUrl(int $bookId): string
    {
        return "{$this->baseUrl}/book/{$bookId}";
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
        if (preg_match('/<title>([^<]+)/u', $html, $match)) {
            $title = trim(str_replace('- المكتبة الشاملة', '', $match[1]));
        }
        
        // Extract author - look for author link pattern
        if (preg_match('/href="\/author\/(\d+)"[^>]*>\s*([^<]+)/u', $html, $match)) {
            $authorId = (int) $match[1];
            $author = trim($match[2]);
        }
        
        // Extract category - look for category link pattern
        if (preg_match('/href="\/category\/(\d+)"[^>]*>\s*([^<]+)/u', $html, $match)) {
            $categoryId = (int) $match[1];
            $category = trim($match[2]);
        }
        
        // Extract table of contents
        $toc = [];
        preg_match_all('/href="\/book\/' . $bookId . '\/(\d+)"[^>]*>\s*([^<]+)/u', $html, $matches, PREG_SET_ORDER);
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
     * Search books by keyword
     */
    public function search(string $query, int $limit = 20): Collection
    {
        $results = collect();
        $query = mb_strtolower($query);
        
        // Search term to category mapping
        $categoryMapping = [
            'حديث' => [6, 7],
            'سنة' => [6],
            'hadith' => [6, 7],
            'bukhari' => [6],
            'muslim' => [6],
            'فقه' => [14, 15, 16, 17, 18],
            'fiqh' => [14, 15, 16, 17, 18],
            'تفسير' => [3],
            'tafsir' => [3],
            'quran' => [3],
            'قرآن' => [3],
            'سيرة' => [24],
            'sirah' => [24],
            'عقيدة' => [1],
            'aqidah' => [1],
            'تاريخ' => [25, 26],
            'history' => [25, 26],
            'لغة' => [31],
            'نحو' => [31],
            'أدب' => [32],
        ];
        
        $categoriesToSearch = [];
        foreach ($categoryMapping as $term => $cats) {
            if (str_contains($query, $term)) {
                $categoriesToSearch = array_merge($categoriesToSearch, $cats);
            }
        }
        
        // Default to hadith and tafsir if no match
        if (empty($categoriesToSearch)) {
            $categoriesToSearch = [6, 3];
        }
        
        $categoriesToSearch = array_unique($categoriesToSearch);
        
        foreach ($categoriesToSearch as $catId) {
            $catBooks = $this->searchByCategory($catId, 15);
            $results = $results->merge($catBooks);
        }
        
        return $results->unique('id')->take($limit);
    }
    
    /**
     * Check if service is enabled
     */
    public function isEnabled(): bool
    {
        return true; // Always enabled, uses static data
    }
}
