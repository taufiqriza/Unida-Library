<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DdcRecommendationService
{
    protected DdcService $ddcService;
    protected ?string $groqApiKey;
    
    public function __construct(DdcService $ddcService)
    {
        $this->ddcService = $ddcService;
        $this->groqApiKey = config('services.groq.api_key');
    }

    /**
     * Analyze title using AI + keyword fallback
     */
    public function analyze(string $title, array $authors = [], array $subjects = []): array
    {
        $title = trim($title);
        if (strlen($title) < 3) {
            return ['recommendations' => [], 'summary' => null, 'keywords_found' => []];
        }

        // Try AI first, fallback to keywords
        $aiResult = $this->analyzeWithAI($title);
        
        if ($aiResult && !empty($aiResult['recommendations'])) {
            return $aiResult;
        }
        
        return $this->analyzeWithKeywords($title);
    }

    /**
     * AI-powered analysis using Groq (free tier: 14,400 req/day)
     */
    protected function analyzeWithAI(string $title): ?array
    {
        if (!$this->groqApiKey) {
            return null;
        }

        // Cache AI results for 24 hours
        $cacheKey = 'ddc_ai_' . md5(strtolower($title));
        
        return Cache::remember($cacheKey, 86400, function () use ($title) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->groqApiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model' => 'llama-3.1-8b-instant',
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => $this->getSystemPrompt()
                            ],
                            [
                                'role' => 'user', 
                                'content' => "Klasifikasikan judul buku ini: \"$title\""
                            ]
                        ],
                        'temperature' => 0.1,
                        'max_tokens' => 500,
                        'response_format' => ['type' => 'json_object']
                    ]);

                if ($response->successful()) {
                    $content = $response->json('choices.0.message.content');
                    $data = json_decode($content, true);
                    
                    if ($data && isset($data['recommendations'])) {
                        return $this->enrichAIResult($data, $title);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Groq AI error: ' . $e->getMessage());
            }
            
            return null;
        });
    }

    protected function getSystemPrompt(): string
    {
        return <<<PROMPT
Kamu adalah pustakawan ahli klasifikasi DDC (Dewey Decimal Classification) untuk perpustakaan universitas Islam di Indonesia.

TUGAS: Analisis judul buku dan berikan rekomendasi kode DDC yang tepat.

ATURAN KHUSUS untuk buku Islam:
- Gunakan kode 2X0-2X9 (ekstensi DDC Indonesia) untuk topik Islam:
  - 2X0: Islam Umum
  - 2X1: Al-Quran dan Ilmu Tafsir
  - 2X2: Hadis dan Ilmu Hadis
  - 2X3: Akidah/Tauhid
  - 2X4: Fiqih/Hukum Islam (2X4.2=Ibadah, 2X4.3=Muamalah, 2X4.4=Waris, 2X4.5=Nikah)
  - 2X5: Tasawuf/Akhlak
  - 2X6: Sosial & Budaya Islam (2X6.3=Ekonomi Islam, 2X6.32=Perbankan Syariah)
  - 2X7: Pendidikan Islam/Dakwah
  - 2X8: Aliran/Organisasi Islam
  - 2X9: Sejarah Islam

RESPONS dalam format JSON:
{
  "recommendations": [
    {"code": "2X4.3", "description": "Fiqih Muamalah", "confidence": "high", "reason": "Judul membahas transaksi dalam Islam"},
    {"code": "330", "description": "Ekonomi", "confidence": "medium", "reason": "Topik ekonomi umum"}
  ],
  "primary_subject": "ekonomi islam",
  "detected_keywords": ["ekonomi", "islam", "muamalah"]
}

Berikan maksimal 5 rekomendasi, urutkan dari yang paling relevan.
PROMPT;
    }

    protected function enrichAIResult(array $data, string $title): array
    {
        $recommendations = [];
        
        foreach ($data['recommendations'] ?? [] as $rec) {
            $code = $rec['code'] ?? '';
            if (!$code) continue;
            
            // Get full description from DDC database
            $ddcInfo = $this->ddcService->find($code);
            if (!$ddcInfo) {
                $results = $this->ddcService->search($code, 1);
                $ddcInfo = $results[0] ?? null;
            }
            
            $recommendations[] = [
                'code' => $code,
                'description' => $ddcInfo ? $this->cleanDescription($ddcInfo['description']) : ($rec['description'] ?? ''),
                'confidence' => $rec['confidence'] ?? 'medium',
                'reason' => $rec['reason'] ?? '',
                'source' => 'ai'
            ];
        }

        $topRec = $recommendations[0] ?? null;
        
        return [
            'title' => $title,
            'recommendations' => $recommendations,
            'summary' => [
                'status' => $topRec && $topRec['confidence'] === 'high' ? 'confident' : 'suggested',
                'message' => $topRec 
                    ? "🤖 AI merekomendasikan {$topRec['code']} - {$topRec['description']}"
                    : 'Tidak dapat menganalisis judul',
                'suggestion' => $topRec['code'] ?? null,
            ],
            'keywords_found' => $data['detected_keywords'] ?? [],
            'primary_subject' => $data['primary_subject'] ?? null,
            'source' => 'ai'
        ];
    }

    /**
     * Keyword-based fallback analysis
     */
    protected function analyzeWithKeywords(string $title): array
    {
        $title = strtolower($title);
        $matchedKeywords = [];
        
        foreach ($this->getKeywordMap() as $keyword => $ddcCode) {
            if (str_contains($title, $keyword)) {
                $score = $this->calculateScore($keyword, $title);
                $matchedKeywords[$keyword] = ['code' => $ddcCode, 'score' => $score];
            }
        }
        
        // Build recommendations from matches
        $recommendations = [];
        foreach ($matchedKeywords as $keyword => $match) {
            $ddcInfo = $this->ddcService->find($match['code']);
            if (!$ddcInfo) {
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
                        'source' => 'keyword'
                    ];
                }
                $recommendations[$code]['score'] += $match['score'];
                $recommendations[$code]['keywords'][] = $keyword;
            }
        }
        
        // Sort and limit
        uasort($recommendations, fn($a, $b) => $b['score'] <=> $a['score']);
        $recommendations = array_slice($recommendations, 0, 5, true);
        
        // Add confidence and reason
        foreach ($recommendations as &$rec) {
            $rec['confidence'] = $rec['score'] >= 50 ? 'high' : ($rec['score'] >= 30 ? 'medium' : 'low');
            $rec['reason'] = 'Kata kunci: ' . implode(', ', array_slice($rec['keywords'], 0, 3));
            unset($rec['score'], $rec['keywords']);
        }
        
        $topRec = reset($recommendations);
        $keywordCount = count($matchedKeywords);
        
        return [
            'title' => $title,
            'recommendations' => array_values($recommendations),
            'summary' => $keywordCount > 0 ? [
                'status' => $topRec && $topRec['confidence'] === 'high' ? 'confident' : 'suggested',
                'message' => "📚 Ditemukan {$keywordCount} kata kunci. Rekomendasi: {$topRec['code']}",
                'suggestion' => $topRec['code'] ?? null,
            ] : [
                'status' => 'no_match',
                'message' => 'Tidak ditemukan kata kunci yang cocok. Silakan pilih manual.',
                'suggestion' => null,
            ],
            'keywords_found' => array_keys($matchedKeywords),
            'source' => 'keyword'
        ];
    }

    protected function calculateScore(string $keyword, string $title): int
    {
        $score = 10 + min(strlen($keyword), 15); // Base + length bonus
        
        // Exact word boundary match
        if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/i', $title)) {
            $score += 25;
        }
        
        // At beginning
        if (str_starts_with($title, $keyword)) {
            $score += 15;
        }
        
        return $score;
    }

    protected function cleanDescription(string $desc): string
    {
        $parts = preg_split('/\s{2,}/', $desc);
        return trim($parts[0] ?? $desc);
    }

    protected function getKeywordMap(): array
    {
        return [
            // Multi-word phrases first (higher priority)
            'ekonomi islam' => '2X6.3', 'islamic economics' => '2X6.3',
            'perbankan syariah' => '2X6.32', 'bank syariah' => '2X6.32', 'islamic banking' => '2X6.32',
            'pendidikan islam' => '2X7', 'islamic education' => '2X7',
            'hukum islam' => '2X4', 'islamic law' => '2X4',
            'sejarah islam' => '2X9', 'islamic history' => '2X9',
            'ilmu hadis' => '2X2.1', 'musthalah hadis' => '2X2.1',
            'ushul fiqh' => '2X4.1', 'usul fikih' => '2X4.1',
            'sirah nabawiyah' => '2X9.12', 'ulumul quran' => '2X1.1',
            'sistem informasi' => '004', 'information system' => '004',
            'kecerdasan buatan' => '006.3', 'artificial intelligence' => '006.3',
            'machine learning' => '006.31', 'data science' => '006.312',
            'bahasa indonesia' => '499.221', 'bahasa inggris' => '420', 'bahasa arab' => '492.7',
            
            // Single words
            'komputer' => '004', 'computer' => '004', 'pemrograman' => '005', 'programming' => '005',
            'software' => '005', 'database' => '005.74', 'internet' => '004.678', 'web' => '006.7',
            'algoritma' => '005.1', 'perpustakaan' => '020', 'library' => '020',
            
            'filsafat' => '100', 'philosophy' => '100', 'psikologi' => '150', 'psychology' => '150',
            'etika' => '170', 'ethics' => '170', 'logika' => '160', 'akhlak' => '170',
            
            'agama' => '200', 'religion' => '200', 'teologi' => '230',
            
            'islam' => '2X0', 'islamic' => '2X0', 'muslim' => '2X0',
            'quran' => '2X1', 'alquran' => '2X1', 'al-quran' => '2X1',
            'hadis' => '2X2', 'hadith' => '2X2',
            'fiqih' => '2X4', 'fiqh' => '2X4', 'fikih' => '2X4', 'syariah' => '2X4',
            'tasawuf' => '2X5', 'sufism' => '2X5', 'akidah' => '2X3', 'tauhid' => '2X3',
            'tafsir' => '2X1.4', 'dakwah' => '2X7', 'tarbiyah' => '2X7',
            'muamalah' => '2X4.3', 'ibadah' => '2X4.2', 'shalat' => '2X4.21',
            'zakat' => '2X4.24', 'haji' => '2X4.25', 'puasa' => '2X4.22',
            'waris' => '2X4.4', 'nikah' => '2X4.5',
            
            'sosiologi' => '301', 'antropologi' => '306', 'politik' => '320',
            'ekonomi' => '330', 'economics' => '330', 'bisnis' => '650', 'business' => '650',
            'manajemen' => '658', 'management' => '658', 'akuntansi' => '657',
            'keuangan' => '332', 'perbankan' => '332.1', 'banking' => '332.1',
            'hukum' => '340', 'law' => '340',
            'pendidikan' => '370', 'education' => '370', 'pembelajaran' => '371',
            'kurikulum' => '375', 'metodologi' => '001.42', 'penelitian' => '001.4',
            
            'bahasa' => '400', 'linguistik' => '410', 'grammar' => '425',
            'nahwu' => '492.75', 'sharaf' => '492.75', 'terjemahan' => '418',
            
            'sains' => '500', 'matematika' => '510', 'fisika' => '530', 'kimia' => '540',
            'biologi' => '570', 'statistik' => '519.5',
            
            'teknologi' => '600', 'teknik' => '620', 'kedokteran' => '610', 'kesehatan' => '613',
            'keperawatan' => '610.73', 'farmasi' => '615', 'pertanian' => '630',
            'arsitektur' => '720', 'industri' => '670',
            
            'seni' => '700', 'musik' => '780', 'olahraga' => '796', 'desain' => '745',
            
            'sastra' => '800', 'literature' => '800', 'puisi' => '808.1', 'novel' => '808.3',
            
            'sejarah' => '900', 'history' => '900', 'geografi' => '910', 'biografi' => '920',
        ];
    }
}
