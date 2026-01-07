<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class KhastaraService
{
    private $baseUrl = 'https://khastara-api.perpusnas.go.id';
    
    public function getCollectionList($filters = [], $page = 1, $perPage = 10)
    {
        $cacheKey = 'khastara_collections_' . md5(serialize($filters) . $page . $perPage);
        
        return Cache::remember($cacheKey, 1800, function () use ($filters, $page, $perPage) {
            try {
                $params = [
                    'page' => $page,
                    'per_page' => $perPage
                ];
                
                if (isset($filters['worksheet_name'])) {
                    $params['worksheet_name'] = $filters['worksheet_name'];
                }
                
                $response = Http::timeout(15)
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36'
                    ])
                    ->get($this->baseUrl . '/inlis/collection-list', $params);
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['data'])) {
                        $transformedData = collect($data['data'])->map(function ($item) {
                            return [
                                'catalog_id' => $item['catalog_id'] ?? '',
                                'title' => $item['title'] ?? 'Tanpa Judul',
                                'cover_utama' => $item['cover_utama'] ?? '/assets/images/placeholder/manuscript.jpg',
                                'create_date' => $item['create_date'] ?? '',
                                'worksheet_name' => $item['worksheet_name'] ?? '',
                                'language_name' => $this->extractLanguage($item['aksara'] ?? []),
                                'subject' => $item['subject'] ?? '',
                                'publisher' => $item['publisher'] ?? '',
                                'publish_year' => $item['publish_year'] ?? '',
                                'deskripsi_fisik' => $item['deskripsi_fisik'] ?? '',
                                'view_count' => $item['view_count'] ?? 0,
                                'konten_digital_count' => $item['konten_digital_count'] ?? 0
                            ];
                        });
                        
                        return [
                            'data' => $transformedData->toArray(),
                            'meta' => [
                                'total' => $data['meta']['total'] ?? 0,
                                'per_page' => $data['meta']['per_page'] ?? $perPage,
                                'current_page' => $data['meta']['current_page'] ?? $page
                            ]
                        ];
                    }
                }
                
                Log::warning('Khastara API error: ' . $response->status());
                return $this->getFallbackData($filters, $page, $perPage);
                
            } catch (\Exception $e) {
                Log::error('Khastara API exception: ' . $e->getMessage());
                return $this->getFallbackData($filters, $page, $perPage);
            }
        });
    }
    
    public function getCollectionTypes($page = 1, $limit = 20)
    {
        $cacheKey = 'khastara_collection_types_' . $page . '_' . $limit;
        
        return Cache::remember($cacheKey, 3600, function () use ($page, $limit) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'User-Agent' => 'Mozilla/5.0'
                    ])
                    ->get($this->baseUrl . '/collection-type-list', [
                        'page' => $page,
                        'limit' => $limit
                    ]);
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                return null;
                
            } catch (\Exception $e) {
                Log::error('Khastara Collection Types API exception: ' . $e->getMessage());
                return null;
            }
        });
    }
    
    public function getStatistics()
    {
        return Cache::remember('khastara_statistics', 7200, function () {
            try {
                $response = Http::timeout(10)
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'User-Agent' => 'Mozilla/5.0'
                    ])
                    ->get($this->baseUrl . '/inlis/collection-statistic');
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                return null;
                
            } catch (\Exception $e) {
                Log::error('Khastara Statistics API exception: ' . $e->getMessage());
                return null;
            }
        });
    }
    
    public function searchManuscripts($query, $type = 'title', $page = 1)
    {
        $filters = [
            $type => $query
        ];
        
        return $this->getCollectionList($filters, $page, 12);
    }
    
    public function getFeaturedManuscripts($limit = 8)
    {
        $data = $this->getCollectionList([], 1, $limit);
        
        if ($data && isset($data['data'])) {
            return collect($data['data'])->map(function ($item) {
                return [
                    'id' => $item['catalog_id'] ?? '',
                    'title' => $item['title'] ?? 'Tanpa Judul',
                    'cover' => $item['cover_utama'] ?? '/assets/images/placeholder/manuscript.jpg',
                    'date' => $item['create_date'] ?? '',
                    'type' => $item['worksheet_name'] ?? 'Naskah',
                    'language' => $item['language_name'] ?? '',
                    'url' => route('opac.khastara.detail', $item['catalog_id'] ?? '')
                ];
            });
        }
        
        return collect();
    }
    
    public function getManuscriptDetail($id)
    {
        // For now, use fallback data for detail since we need specific detail endpoint
        $mockData = [
            '50513' => [
                'catalog_id' => '50513',
                'title' => 'Syarh \'Aja\'ib al-Qalb',
                'cover_utama' => 'http://file-opac.perpusnas.go.id/uploaded_files/sampul_koleksi/original/Manuskrip/50513.JPG',
                'create_date' => '11/21/2007 11:38:13 AM',
                'worksheet_name' => 'Manuskrip',
                'language_name' => 'Arab',
                'description' => 'Naskah tentang tasawuf dalam bahasa Arab yang membahas keajaiban hati dalam perspektif spiritual Islam.',
                'subject' => 'Tasawuf -- Manuskrip Arab -- Kesusastraan Arab',
                'publisher' => '[produsen tidak teridentifikasi]',
                'publish_year' => '[tahun produksi tidak teridentifikasi]',
                'deskripsi_fisik' => '318 halaman',
                'call_number' => 'A 109; A 109',
                'ddc' => '892.7 [23]',
                'aksara' => 'Aksara Arab',
                'condition' => 'Baik',
                'location' => 'Transformasi Digital',
                'external_url' => 'https://khastara.perpusnas.go.id/koleksi-digital/detail/?catId=50513',
                'view_count' => 3407,
                'konten_digital_count' => 13
            ]
        ];
        
        return $mockData[$id] ?? null;
    }
    
    private function extractLanguage($aksara)
    {
        if (empty($aksara)) return 'Indonesia';
        
        $firstAksara = is_array($aksara) ? $aksara[0] : $aksara;
        
        $languageMap = [
            'Aksara Arab' => 'Arab',
            'Aksara Jawa' => 'Jawa',
            'Aksara Latin' => 'Indonesia',
            'Aksara Bali' => 'Bali',
            'Aksara Bugis' => 'Bugis',
            'Aksara Jawi' => 'Melayu',
            'Aksara Batak' => 'Batak'
        ];
        
        return $languageMap[$firstAksara] ?? 'Indonesia';
    }
    
    private function getFallbackData($filters, $page, $perPage)
    {
        // Minimal fallback data
        $mockData = [
            ['catalog_id' => '50513', 'title' => 'Syarh Aja\'ib al-Qalb', 'cover_utama' => 'http://file-opac.perpusnas.go.id/uploaded_files/sampul_koleksi/original/Manuskrip/50513.JPG', 'create_date' => '11/21/2007', 'worksheet_name' => 'Manuskrip', 'language_name' => 'Arab'],
            ['catalog_id' => '50516', 'title' => 'Fath al-Muluk', 'cover_utama' => 'http://file-opac.perpusnas.go.id/uploaded_files/sampul_koleksi/original/Manuskrip/50516.jpg', 'create_date' => '11/21/2007', 'worksheet_name' => 'Manuskrip', 'language_name' => 'Arab']
        ];
        
        return [
            'data' => $mockData,
            'meta' => [
                'total' => count($mockData),
                'per_page' => $perPage,
                'current_page' => $page
            ]
        ];
    }
}
