<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DdcRecommendationService
{
    protected DdcService $ddcService;
    
    // Keyword mappings to DDC classes (curated for Indonesian academic context)
    protected array $keywordMap = [
        // 000 - Computer Science, Information
        'komputer' => '004', 'computer' => '004', 'programming' => '005', 'pemrograman' => '005',
        'software' => '005', 'database' => '005.74', 'internet' => '004.678', 'web' => '006.7',
        'artificial intelligence' => '006.3', 'kecerdasan buatan' => '006.3', 'ai' => '006.3',
        'machine learning' => '006.31', 'data science' => '006.312', 'big data' => '005.7',
        'cybersecurity' => '005.8', 'keamanan siber' => '005.8', 'algoritma' => '005.1',
        'perpustakaan' => '020', 'library' => '020', 'informasi' => '020',
        'sistem informasi' => '004', 'information system' => '004',
        
        // 100 - Philosophy, Psychology
        'filsafat' => '100', 'philosophy' => '100', 'psikologi' => '150', 'psychology' => '150',
        'etika' => '170', 'ethics' => '170', 'logika' => '160', 'logic' => '160',
        'metafisika' => '110', 'epistemologi' => '121', 'akhlak' => '170',
        'moral' => '170', 'karakter' => '155.2',
        
        // 200 - Religion
        'agama' => '200', 'religion' => '200', 'teologi' => '230', 'theology' => '230',
        'kristen' => '230', 'christian' => '230', 'bible' => '220', 'alkitab' => '220',
        
        // 2X0 - Islam (Indonesian DDC extension)
        'islam' => '2X0', 'islamic' => '2X0', 'muslim' => '2X0', 'quran' => '2X1',
        'alquran' => '2X1', 'al-quran' => '2X1', 'hadis' => '2X2', 'hadith' => '2X2',
        'fiqih' => '2X4', 'fiqh' => '2X4', 'fikih' => '2X4', 'syariah' => '2X4', 'sharia' => '2X4',
        'tasawuf' => '2X5', 'sufism' => '2X5', 'akidah' => '2X3', 'aqidah' => '2X3', 'tauhid' => '2X3',
        'tafsir' => '2X1.4', 'dakwah' => '2X7', 'islamic education' => '2X7',
        'pendidikan islam' => '2X7', 'tarbiyah' => '2X7', 'ekonomi islam' => '2X6.3', 'islamic economics' => '2X6.3',
        'perbankan syariah' => '2X6.32', 'islamic banking' => '2X6.32', 'bank syariah' => '2X6.32',
        'hukum islam' => '2X4', 'islamic law' => '2X4', 'ushul fiqh' => '2X4.1', 'usul fikih' => '2X4.1',
        'sejarah islam' => '2X9', 'islamic history' => '2X9', 'sirah' => '2X9.1', 'sirah nabawiyah' => '2X9.12',
        'ulumul quran' => '2X1.1', 'ilmu hadis' => '2X2.1', 'musthalah hadis' => '2X2.1',
        'muamalah' => '2X4.3', 'ibadah' => '2X4.2', 'shalat' => '2X4.21', 'zakat' => '2X4.24',
        'haji' => '2X4.25', 'puasa' => '2X4.22', 'waris' => '2X4.4', 'nikah' => '2X4.5',
        
        // 300 - Social Sciences
        'sosiologi' => '301', 'sociology' => '301', 'antropologi' => '306', 'anthropology' => '306',
        'politik' => '320', 'politics' => '320', 'pemerintahan' => '320', 'government' => '320',
        'ekonomi' => '330', 'economics' => '330', 'bisnis' => '650', 'business' => '650',
        'manajemen' => '658', 'management' => '658', 'akuntansi' => '657', 'accounting' => '657',
        'keuangan' => '332', 'finance' => '332', 'perbankan' => '332.1', 'banking' => '332.1',
        'hukum' => '340', 'law' => '340', 'legal' => '340', 'undang-undang' => '348',
        'pendidikan' => '370', 'education' => '370', 'pembelajaran' => '371', 'teaching' => '371',
        'kurikulum' => '375', 'curriculum' => '375', 'pedagogi' => '371.3',
        'metodologi' => '001.42', 'penelitian' => '001.4', 'research' => '001.4',
        'komunikasi' => '302.2', 'communication' => '302.2', 'media' => '302.23',
        'jurnalistik' => '070', 'journalism' => '070', 'pers' => '070.4',
        
        // 400 - Language
        'bahasa' => '400', 'language' => '400', 'linguistik' => '410', 'linguistics' => '410',
        'bahasa indonesia' => '499.221', 'indonesian' => '499.221',
        'bahasa inggris' => '420', 'english' => '420', 'grammar' => '425',
        'bahasa arab' => '492.7', 'arabic' => '492.7', 'nahwu' => '492.75', 'sharaf' => '492.75',
        'terjemahan' => '418', 'translation' => '418',
        
        // 500 - Science
        'sains' => '500', 'science' => '500', 'matematika' => '510', 'mathematics' => '510',
        'fisika' => '530', 'physics' => '530', 'kimia' => '540', 'chemistry' => '540',
        'biologi' => '570', 'biology' => '570', 'astronomi' => '520', 'astronomy' => '520',
        'geologi' => '550', 'geology' => '550', 'ekologi' => '577', 'ecology' => '577',
        'statistik' => '519.5', 'statistics' => '519.5', 'probabilitas' => '519.2',
        
        // 600 - Technology
        'teknologi' => '600', 'technology' => '600', 'teknik' => '620', 'engineering' => '620',
        'kedokteran' => '610', 'medicine' => '610', 'kesehatan' => '613', 'health' => '613',
        'keperawatan' => '610.73', 'nursing' => '610.73', 'farmasi' => '615', 'pharmacy' => '615',
        'pertanian' => '630', 'agriculture' => '630', 'peternakan' => '636', 'livestock' => '636',
        'arsitektur' => '720', 'architecture' => '720', 'konstruksi' => '690', 'construction' => '690',
        'elektro' => '621.3', 'electrical' => '621.3', 'mesin' => '621', 'mechanical' => '621',
        'industri' => '670', 'manufacturing' => '670',
        
        // 700 - Arts
        'seni' => '700', 'art' => '700', 'musik' => '780', 'music' => '780',
        'lukis' => '750', 'painting' => '750', 'fotografi' => '770', 'photography' => '770',
        'olahraga' => '796', 'sports' => '796', 'sepakbola' => '796.334', 'football' => '796.334',
        'desain' => '745', 'design' => '745', 'grafis' => '741.6', 'graphic' => '741.6',
        
        // 800 - Literature
        'sastra' => '800', 'literature' => '800', 'puisi' => '808.1', 'poetry' => '808.1',
        'novel' => '808.3', 'fiction' => '808.3', 'drama' => '808.2', 'cerpen' => '808.31',
        'sastra indonesia' => '899.221', 'sastra arab' => '892.7', 'arabic literature' => '892.7',
        
        // 900 - History, Geography
        'sejarah' => '900', 'history' => '900', 'geografi' => '910', 'geography' => '910',
        'biografi' => '920', 'biography' => '920', 'indonesia' => '959.8',
        'sejarah indonesia' => '959.8', 'peradaban' => '909', 'civilization' => '909',
    ];

    public function __construct(DdcService $ddcService)
    {
        $this->ddcService = $ddcService;
    }

    /**
     * Analyze title and return DDC recommendations with confidence scores
     */
    public function analyze(string $title, array $authors = [], array $subjects = []): array
    {
        $title = strtolower(trim($title));
        $recommendations = [];
        $matchedKeywords = [];
        
        // 1. Keyword-based analysis
        foreach ($this->keywordMap as $keyword => $ddcCode) {
            if (str_contains($title, $keyword)) {
                $score = $this->calculateKeywordScore($keyword, $title);
                $matchedKeywords[$keyword] = ['code' => $ddcCode, 'score' => $score];
            }
        }
        
        // 2. Get DDC details for matched codes
        foreach ($matchedKeywords as $keyword => $match) {
            $ddcInfo = $this->ddcService->find($match['code']);
            if (!$ddcInfo) {
                // Try to find closest match
                $results = $this->ddcService->search($match['code'], 1);
                $ddcInfo = $results[0] ?? null;
            }
            
            if ($ddcInfo) {
                $code = $ddcInfo['code'];
                if (!isset($recommendations[$code])) {
                    $recommendations[$code] = [
                        'code' => $code,
                        'description' => $this->cleanDescription($ddcInfo['description']),
                        'score' => 0,
                        'keywords' => [],
                        'reason' => '',
                    ];
                }
                $recommendations[$code]['score'] += $match['score'];
                $recommendations[$code]['keywords'][] = $keyword;
            }
        }
        
        // 3. Sort by score and limit
        uasort($recommendations, fn($a, $b) => $b['score'] <=> $a['score']);
        $recommendations = array_slice($recommendations, 0, 5, true);
        
        // 4. Generate reasons and confidence levels
        foreach ($recommendations as &$rec) {
            $rec['confidence'] = $this->getConfidenceLevel($rec['score']);
            $rec['reason'] = $this->generateReason($rec['keywords'], $title);
        }
        
        // 5. Build summary
        $summary = $this->buildSummary($title, $recommendations, $matchedKeywords);
        
        return [
            'title' => $title,
            'recommendations' => array_values($recommendations),
            'summary' => $summary,
            'keywords_found' => array_keys($matchedKeywords),
        ];
    }

    protected function calculateKeywordScore(string $keyword, string $title): int
    {
        $score = 10; // Base score
        
        // Exact word match (not part of another word)
        if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/i', $title)) {
            $score += 20;
        }
        
        // Keyword at beginning of title
        if (str_starts_with($title, $keyword)) {
            $score += 15;
        }
        
        // Longer keywords are more specific
        $score += min(strlen($keyword), 10);
        
        return $score;
    }

    protected function getConfidenceLevel(int $score): string
    {
        if ($score >= 50) return 'high';
        if ($score >= 30) return 'medium';
        return 'low';
    }

    protected function generateReason(array $keywords, string $title): string
    {
        if (empty($keywords)) return '';
        
        $keywordList = implode(', ', array_map(fn($k) => "\"$k\"", array_slice($keywords, 0, 3)));
        return "Terdeteksi kata kunci: $keywordList";
    }

    protected function cleanDescription(string $desc): string
    {
        // Get main description before additional info
        $parts = preg_split('/\s{2,}/', $desc);
        return trim($parts[0] ?? $desc);
    }

    protected function buildSummary(string $title, array $recommendations, array $matchedKeywords): array
    {
        $keywordCount = count($matchedKeywords);
        $topRec = reset($recommendations);
        
        if ($keywordCount === 0) {
            return [
                'status' => 'no_match',
                'message' => 'Tidak ditemukan kata kunci yang cocok dengan database DDC. Silakan pilih klasifikasi secara manual.',
                'suggestion' => null,
            ];
        }
        
        if ($topRec && $topRec['confidence'] === 'high') {
            return [
                'status' => 'confident',
                'message' => "Judul ini sangat cocok dengan klasifikasi {$topRec['code']} ({$this->cleanDescription($topRec['description'])})",
                'suggestion' => $topRec['code'],
            ];
        }
        
        if ($topRec && $topRec['confidence'] === 'medium') {
            return [
                'status' => 'suggested',
                'message' => "Ditemukan {$keywordCount} kata kunci. Rekomendasi utama: {$topRec['code']}",
                'suggestion' => $topRec['code'],
            ];
        }
        
        return [
            'status' => 'review',
            'message' => "Ditemukan {$keywordCount} kata kunci dengan tingkat kepercayaan rendah. Mohon review manual.",
            'suggestion' => $topRec['code'] ?? null,
        ];
    }
}
