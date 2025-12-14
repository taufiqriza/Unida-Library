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
        // كتب السنة (Hadith) - الكتب الستة ومسانيد
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
            ['id' => 5746, 'title' => 'المعجم الأوسط للطبراني'],
            ['id' => 5766, 'title' => 'المعجم الصغير للطبراني'],
            ['id' => 5802, 'title' => 'مصنف عبد الرزاق'],
            ['id' => 5800, 'title' => 'مصنف ابن أبي شيبة'],
            ['id' => 5680, 'title' => 'سنن البيهقي الكبرى'],
            ['id' => 5656, 'title' => 'صحيح ابن حبان'],
            ['id' => 5671, 'title' => 'صحيح ابن خزيمة'],
            ['id' => 5692, 'title' => 'المستدرك على الصحيحين'],
            ['id' => 26592, 'title' => 'مسند أبي هريرة'],
            ['id' => 1668, 'title' => 'مسند الشافعي'],
        ],
        // شروح الحديث
        7 => [
            ['id' => 52, 'title' => 'فتح الباري شرح صحيح البخاري'],
            ['id' => 9289, 'title' => 'عمدة القاري شرح صحيح البخاري'],
            ['id' => 3088, 'title' => 'شرح النووي على مسلم'],
            ['id' => 6526, 'title' => 'عون المعبود شرح سنن أبي داود'],
            ['id' => 6527, 'title' => 'تحفة الأحوذي شرح سنن الترمذي'],
            ['id' => 6511, 'title' => 'نيل الأوطار'],
            ['id' => 6509, 'title' => 'سبل السلام'],
            ['id' => 6530, 'title' => 'شرح السنة للبغوي'],
            ['id' => 6502, 'title' => 'جامع العلوم والحكم'],
            ['id' => 6504, 'title' => 'رياض الصالحين'],
            ['id' => 6501, 'title' => 'الترغيب والترهيب'],
            ['id' => 6500, 'title' => 'الأدب المفرد'],
            ['id' => 6503, 'title' => 'بلوغ المرام'],
            ['id' => 6505, 'title' => 'عمدة الأحكام'],
            ['id' => 6506, 'title' => 'المنتقى من أخبار المصطفى'],
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
            ['id' => 131, 'title' => 'تفسير الجلالين'],
            ['id' => 132, 'title' => 'تفسير النسفي'],
            ['id' => 133, 'title' => 'تفسير أبي السعود'],
            ['id' => 134, 'title' => 'روح المعاني للألوسي'],
            ['id' => 135, 'title' => 'تفسير المنار'],
            ['id' => 136, 'title' => 'في ظلال القرآن'],
            ['id' => 137, 'title' => 'أضواء البيان للشنقيطي'],
            ['id' => 138, 'title' => 'التحرير والتنوير'],
        ],
        // العقيدة
        1 => [
            ['id' => 1301, 'title' => 'كتاب التوحيد'],
            ['id' => 826, 'title' => 'شرح العقيدة الطحاوية'],
            ['id' => 824, 'title' => 'العقيدة الواسطية'],
            ['id' => 7414, 'title' => 'مجموع الفتاوى لابن تيمية'],
            ['id' => 2289, 'title' => 'درء تعارض العقل والنقل'],
            ['id' => 828, 'title' => 'شرح العقيدة الواسطية'],
            ['id' => 829, 'title' => 'الحموية الكبرى'],
            ['id' => 830, 'title' => 'التدمرية'],
            ['id' => 831, 'title' => 'الفتوى الحموية'],
            ['id' => 832, 'title' => 'الرسالة التبوكية'],
            ['id' => 833, 'title' => 'اقتضاء الصراط المستقيم'],
            ['id' => 834, 'title' => 'القواعد المثلى'],
            ['id' => 835, 'title' => 'لمعة الاعتقاد'],
            ['id' => 836, 'title' => 'الإيمان لابن تيمية'],
            ['id' => 837, 'title' => 'معارج القبول'],
        ],
        // الفقه العام
        18 => [
            ['id' => 2095, 'title' => 'المغني لابن قدامة'],
            ['id' => 2050, 'title' => 'الأم للشافعي'],
            ['id' => 8361, 'title' => 'المجموع شرح المهذب'],
            ['id' => 8374, 'title' => 'روضة الطالبين'],
            ['id' => 2017, 'title' => 'المدونة الكبرى'],
            ['id' => 2096, 'title' => 'الفروع لابن مفلح'],
            ['id' => 2097, 'title' => 'شرح منتهى الإرادات'],
            ['id' => 2098, 'title' => 'الروض المربع'],
            ['id' => 2099, 'title' => 'زاد المستقنع'],
            ['id' => 2100, 'title' => 'عمدة الفقه'],
            ['id' => 2101, 'title' => 'دليل الطالب'],
            ['id' => 2102, 'title' => 'أخصر المختصرات'],
            ['id' => 2103, 'title' => 'فقه السنة'],
            ['id' => 2104, 'title' => 'الفقه الميسر'],
            ['id' => 2105, 'title' => 'الموسوعة الفقهية الكويتية'],
        ],
        // الفقه الحنفي
        14 => [
            ['id' => 2177, 'title' => 'بدائع الصنائع'],
            ['id' => 2178, 'title' => 'رد المحتار على الدر المختار'],
            ['id' => 2188, 'title' => 'الهداية شرح البداية'],
            ['id' => 2179, 'title' => 'فتح القدير'],
            ['id' => 2180, 'title' => 'البحر الرائق'],
            ['id' => 2181, 'title' => 'تبيين الحقائق'],
            ['id' => 2182, 'title' => 'المبسوط للسرخسي'],
            ['id' => 2183, 'title' => 'تحفة الفقهاء'],
            ['id' => 2184, 'title' => 'الاختيار لتعليل المختار'],
            ['id' => 2185, 'title' => 'شرح فتح القدير'],
        ],
        // الفقه المالكي
        15 => [
            ['id' => 2017, 'title' => 'المدونة الكبرى'],
            ['id' => 2018, 'title' => 'مختصر خليل'],
            ['id' => 22984, 'title' => 'الشرح الكبير للدردير'],
            ['id' => 2019, 'title' => 'حاشية الدسوقي'],
            ['id' => 2020, 'title' => 'مواهب الجليل'],
            ['id' => 2021, 'title' => 'التاج والإكليل'],
            ['id' => 2022, 'title' => 'الذخيرة للقرافي'],
            ['id' => 2023, 'title' => 'بداية المجتهد'],
            ['id' => 2024, 'title' => 'الكافي في فقه أهل المدينة'],
            ['id' => 2025, 'title' => 'شرح الزرقاني على مختصر خليل'],
        ],
        // الفقه الشافعي
        16 => [
            ['id' => 2050, 'title' => 'الأم للشافعي'],
            ['id' => 8361, 'title' => 'المجموع شرح المهذب'],
            ['id' => 285, 'title' => 'منهاج الطالبين'],
            ['id' => 2051, 'title' => 'تحفة المحتاج'],
            ['id' => 2052, 'title' => 'نهاية المحتاج'],
            ['id' => 2053, 'title' => 'مغني المحتاج'],
            ['id' => 2054, 'title' => 'حاشيتا قليوبي وعميرة'],
            ['id' => 2055, 'title' => 'أسنى المطالب'],
            ['id' => 2056, 'title' => 'فتح الوهاب'],
            ['id' => 2057, 'title' => 'الإقناع للشربيني'],
        ],
        // الفقه الحنبلي
        17 => [
            ['id' => 2095, 'title' => 'المغني لابن قدامة'],
            ['id' => 27869, 'title' => 'الإنصاف في معرفة الراجح'],
            ['id' => 3107, 'title' => 'كشاف القناع'],
            ['id' => 2120, 'title' => 'شرح منتهى الإرادات'],
            ['id' => 2121, 'title' => 'الروض المربع'],
            ['id' => 2122, 'title' => 'زاد المستقنع'],
            ['id' => 2123, 'title' => 'العدة شرح العمدة'],
            ['id' => 2124, 'title' => 'منار السبيل'],
            ['id' => 2125, 'title' => 'الشرح الممتع'],
            ['id' => 2126, 'title' => 'المقنع لابن قدامة'],
        ],
        // السيرة النبوية
        24 => [
            ['id' => 2220, 'title' => 'سيرة ابن هشام'],
            ['id' => 20247, 'title' => 'السيرة النبوية لابن كثير'],
            ['id' => 3180, 'title' => 'زاد المعاد'],
            ['id' => 3097, 'title' => 'الشمائل المحمدية'],
            ['id' => 2221, 'title' => 'الرحيق المختوم'],
            ['id' => 2222, 'title' => 'فقه السيرة الغزالي'],
            ['id' => 2223, 'title' => 'نور اليقين'],
            ['id' => 2224, 'title' => 'السيرة الحلبية'],
            ['id' => 2225, 'title' => 'عيون الأثر'],
            ['id' => 2226, 'title' => 'الفصول في السيرة'],
            ['id' => 2227, 'title' => 'دلائل النبوة للبيهقي'],
            ['id' => 2228, 'title' => 'الشفا بتعريف حقوق المصطفى'],
        ],
        // التاريخ
        25 => [
            ['id' => 3315, 'title' => 'تاريخ الطبري'],
            ['id' => 8315, 'title' => 'البداية والنهاية'],
            ['id' => 3278, 'title' => 'الكامل في التاريخ'],
            ['id' => 3309, 'title' => 'تاريخ الإسلام للذهبي'],
            ['id' => 3316, 'title' => 'تاريخ ابن خلدون'],
            ['id' => 3317, 'title' => 'المقدمة لابن خلدون'],
            ['id' => 3318, 'title' => 'الطبقات الكبرى'],
            ['id' => 3319, 'title' => 'تاريخ بغداد'],
            ['id' => 3320, 'title' => 'تاريخ دمشق'],
            ['id' => 3321, 'title' => 'وفيات الأعيان'],
            ['id' => 3322, 'title' => 'الوافي بالوفيات'],
            ['id' => 3323, 'title' => 'صفة الصفوة'],
        ],
        // التراجم والطبقات
        26 => [
            ['id' => 3477, 'title' => 'سير أعلام النبلاء'],
            ['id' => 3339, 'title' => 'تهذيب التهذيب'],
            ['id' => 3386, 'title' => 'تقريب التهذيب'],
            ['id' => 3459, 'title' => 'الطبقات الكبرى لابن سعد'],
            ['id' => 3478, 'title' => 'تذكرة الحفاظ'],
            ['id' => 3479, 'title' => 'ميزان الاعتدال'],
            ['id' => 3480, 'title' => 'لسان الميزان'],
            ['id' => 3481, 'title' => 'الكاشف للذهبي'],
            ['id' => 3482, 'title' => 'طبقات الحفاظ'],
            ['id' => 3483, 'title' => 'حلية الأولياء'],
            ['id' => 3484, 'title' => 'طبقات الشافعية الكبرى'],
            ['id' => 3485, 'title' => 'الدرر الكامنة'],
        ],
        // النحو والصرف
        31 => [
            ['id' => 12038, 'title' => 'الكتاب لسيبويه'],
            ['id' => 11989, 'title' => 'أوضح المسالك إلى ألفية ابن مالك'],
            ['id' => 12148, 'title' => 'شرح ابن عقيل'],
            ['id' => 12001, 'title' => 'شرح قطر الندى'],
            ['id' => 12002, 'title' => 'شرح شذور الذهب'],
            ['id' => 12003, 'title' => 'المفصل للزمخشري'],
            ['id' => 12004, 'title' => 'الأصول في النحو'],
            ['id' => 12005, 'title' => 'المقتضب للمبرد'],
            ['id' => 12006, 'title' => 'الخصائص لابن جني'],
            ['id' => 12007, 'title' => 'همع الهوامع'],
        ],
        // الأدب
        32 => [
            ['id' => 11749, 'title' => 'الأغاني للأصفهاني'],
            ['id' => 11692, 'title' => 'البيان والتبيين'],
            ['id' => 11687, 'title' => 'الكامل في اللغة والأدب'],
            ['id' => 11750, 'title' => 'العقد الفريد'],
            ['id' => 11751, 'title' => 'الحيوان للجاحظ'],
            ['id' => 11752, 'title' => 'البخلاء للجاحظ'],
            ['id' => 11753, 'title' => 'أدب الكاتب'],
            ['id' => 11754, 'title' => 'طبقات الشعراء'],
            ['id' => 11755, 'title' => 'الشعر والشعراء'],
            ['id' => 11756, 'title' => 'زهر الآداب'],
        ],
        // علوم الحديث
        10 => [
            ['id' => 6600, 'title' => 'مقدمة ابن الصلاح'],
            ['id' => 6601, 'title' => 'تدريب الراوي'],
            ['id' => 6602, 'title' => 'نخبة الفكر'],
            ['id' => 6603, 'title' => 'نزهة النظر'],
            ['id' => 6604, 'title' => 'الباعث الحثيث'],
            ['id' => 6605, 'title' => 'توضيح الأفكار'],
            ['id' => 6606, 'title' => 'فتح المغيث'],
            ['id' => 6607, 'title' => 'الموقظة للذهبي'],
            ['id' => 6608, 'title' => 'ألفية العراقي'],
            ['id' => 6609, 'title' => 'شرح علل الترمذي'],
        ],
        // أصول الفقه
        11 => [
            ['id' => 7001, 'title' => 'الرسالة للشافعي'],
            ['id' => 7002, 'title' => 'المستصفى للغزالي'],
            ['id' => 7003, 'title' => 'روضة الناظر'],
            ['id' => 7004, 'title' => 'إرشاد الفحول'],
            ['id' => 7005, 'title' => 'أصول السرخسي'],
            ['id' => 7006, 'title' => 'المحصول للرازي'],
            ['id' => 7007, 'title' => 'الإحكام للآمدي'],
            ['id' => 7008, 'title' => 'جمع الجوامع'],
            ['id' => 7009, 'title' => 'مذكرة أصول الفقه'],
            ['id' => 7010, 'title' => 'البحر المحيط للزركشي'],
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
    public function search(string $query, int $limit = 50): Collection
    {
        $results = collect();
        $query = mb_strtolower($query);
        
        // Search term to category mapping (expanded)
        $categoryMapping = [
            // Hadith
            'حديث' => [6, 7, 10],
            'سنة' => [6, 7],
            'hadith' => [6, 7, 10],
            'bukhari' => [6],
            'muslim' => [6],
            'صحيح' => [6],
            'سنن' => [6],
            'مسند' => [6],
            // Fiqh
            'فقه' => [14, 15, 16, 17, 18, 11],
            'fiqh' => [14, 15, 16, 17, 18],
            'حنفي' => [14],
            'مالكي' => [15],
            'شافعي' => [16],
            'حنبلي' => [17],
            'أصول' => [11],
            // Tafsir
            'تفسير' => [3],
            'tafsir' => [3],
            'quran' => [3],
            'قرآن' => [3],
            // Sirah & History
            'سيرة' => [24],
            'sirah' => [24],
            'تاريخ' => [25, 26],
            'history' => [25, 26],
            'طبقات' => [26],
            'تراجم' => [26],
            // Aqidah
            'عقيدة' => [1],
            'aqidah' => [1],
            'توحيد' => [1],
            // Language & Literature
            'لغة' => [31, 32],
            'نحو' => [31],
            'أدب' => [32],
            'صرف' => [31],
            // Sciences of Hadith
            'علوم' => [10, 11],
            'مصطلح' => [10],
        ];
        
        $categoriesToSearch = [];
        foreach ($categoryMapping as $term => $cats) {
            if (str_contains($query, $term)) {
                $categoriesToSearch = array_merge($categoriesToSearch, $cats);
            }
        }
        
        // Default to hadith, tafsir and fiqh if no match
        if (empty($categoriesToSearch)) {
            $categoriesToSearch = [6, 3, 18, 1];
        }
        
        $categoriesToSearch = array_unique($categoriesToSearch);
        
        // Get 20 books per category for more results
        foreach ($categoriesToSearch as $catId) {
            $catBooks = $this->searchByCategory($catId, 20);
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
