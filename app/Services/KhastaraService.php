<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class KhastaraService
{
    private $baseUrl = 'https://khastara.perpusnas.go.id/api';
    
    public function getCollectionList($filters = [], $page = 1, $perPage = 10)
    {
        // Mock data dengan lebih banyak koleksi
        $mockData = [
            // Naskah Kuno
            ['catalog_id' => 'nk-001', 'title' => 'Serat Centhini', 'cover_utama' => 'https://khastara.perpusnas.go.id/assets/images/placeholder/manuscript.jpg', 'create_date' => '2024-01-01', 'worksheet_name' => 'Naskah Kuno', 'language_name' => 'Jawa'],
            ['catalog_id' => 'nk-002', 'title' => 'Babad Tanah Jawi', 'cover_utama' => 'https://khastara.perpusnas.go.id/assets/images/placeholder/manuscript.jpg', 'create_date' => '2024-01-02', 'worksheet_name' => 'Naskah Kuno', 'language_name' => 'Jawa'],
            ['catalog_id' => 'nk-003', 'title' => 'Serat Wedhatama', 'cover_utama' => 'https://khastara.perpusnas.go.id/assets/images/placeholder/manuscript.jpg', 'create_date' => '2024-01-03', 'worksheet_name' => 'Naskah Kuno', 'language_name' => 'Jawa'],
            ['catalog_id' => 'nk-004', 'title' => 'Kitab Undang-Undang Melaka', 'cover_utama' => 'https://khastara.perpusnas.go.id/assets/images/placeholder/manuscript.jpg', 'create_date' => '2024-01-04', 'worksheet_name' => 'Naskah Kuno', 'language_name' => 'Melayu'],
            ['catalog_id' => 'nk-005', 'title' => 'Hikayat Raja-Raja Pasai', 'cover_utama' => 'https://khastara.perpusnas.go.id/assets/images/placeholder/manuscript.jpg', 'create_date' => '2024-01-05', 'worksheet_name' => 'Naskah Kuno', 'language_name' => 'Melayu'],
            ['catalog_id' => 'nk-006', 'title' => 'Serat Yusuf', 'cover_utama' => 'https://khastara.perpusnas.go.id/assets/images/placeholder/manuscript.jpg', 'create_date' => '2024-01-06', 'worksheet_name' => 'Naskah Kuno', 'language_name' => 'Jawa'],
            ['catalog_id' => 'nk-007', 'title' => 'Hikayat Hang Tuah', 'cover_utama' => 'https://khastara.perpusnas.go.id/assets/images/placeholder/manuscript.jpg', 'create_date' => '2024-01-07', 'worksheet_name' => 'Naskah Kuno', 'language_name' => 'Melayu'],
            ['catalog_id' => 'nk-008', 'title' => 'Serat Kalimasada', 'cover_utama' => 'https://khastara.perpusnas.go.id/assets/images/placeholder/manuscript.jpg', 'create_date' => '2024-01-08', 'worksheet_name' => 'Naskah Kuno', 'language_name' => 'Jawa'],
            
            // Buku Langka
            ['catalog_id' => 'bl-001', 'title' => 'Sejarah Melayu', 'cover_utama' => 'https://khastara.perpusnas.go.id/assets/images/placeholder/manuscript.jpg', 'create_date' => '2024-02-01', 'worksheet_name' => 'Buku Langka', 'language_name' => 'Melayu'],
            ['catalog_id' => 'bl-002', 'title' => 'Pustaka Raja Purwa', 'cover_utama' => 'https://khastara.perpusnas.go.id/assets/images/placeholder/manuscript.jpg', 'create_date' => '2024-02-02', 'worksheet_name' => 'Buku Langka', 'language_name' => 'Jawa'],
            ['catalog_id' => 'bl-003', 'title' => 'Kitab Kuning Pesantren', 'cover_utama' => 'https://khastara.perpusnas.go.id/assets/images/placeholder/manuscript.jpg', 'create_date' => '2024-02-03', 'worksheet_name' => 'Buku Langka', 'language_name' => 'Arab'],
            ['catalog_id' => 'bl-004', 'title' => 'Lontar Bali Kuno', 'cover_utama' => 'https://khastara.perpusnas.go.id/assets/images/placeholder/manuscript.jpg', 'create_date' => '2024-02-04', 'worksheet_name' => 'Buku Langka', 'language_name' => 'Bali'],
            ['catalog_id' => 'bl-005', 'title' => 'Naskah Bugis Makassar', 'cover_utama' => 'https://khastara.perpusnas.go.id/assets/images/placeholder/manuscript.jpg', 'create_date' => '2024-02-05', 'worksheet_name' => 'Buku Langka', 'language_name' => 'Bugis'],
            ['catalog_id' => 'bl-006', 'title' => 'Tambo Minangkabau', 'cover_utama' => 'https://khastara.perpusnas.go.id/assets/images/placeholder/manuscript.jpg', 'create_date' => '2024-02-06', 'worksheet_name' => 'Buku Langka', 'language_name' => 'Minang'],
        ];
        
        // Filter data
        $filteredData = collect($mockData);
        
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                if ($value) {
                    $filteredData = $filteredData->filter(function ($item) use ($key, $value) {
                        return stripos($item[$key] ?? '', $value) !== false;
                    });
                }
            }
        }
        
        $total = $filteredData->count();
        $paginatedData = $filteredData->skip(($page - 1) * $perPage)->take($perPage)->values();
        
        return [
            'data' => $paginatedData->toArray(),
            'meta' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page
            ]
        ];
        
        $cacheKey = 'khastara_collections_' . md5(serialize($filters) . $page . $perPage);
        
        return Cache::remember($cacheKey, 3600, function () use ($filters, $page, $perPage) {
            try {
                $response = Http::timeout(10)->post($this->baseUrl . '/collections', [
                    'filter' => $filters,
                    'pages' => $page,
                    'per_page' => $perPage
                ]);
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                Log::warning('Khastara API error: ' . $response->status());
                return null;
                
            } catch (\Exception $e) {
                Log::error('Khastara API exception: ' . $e->getMessage());
                return null;
            }
        });
    }
    
    public function getDigitalCollection($page = 1, $perPage = 20)
    {
        $cacheKey = 'khastara_digital_' . $page . '_' . $perPage;
        
        return Cache::remember($cacheKey, 3600, function () use ($page, $perPage) {
            try {
                $response = Http::timeout(10)->get($this->baseUrl . '/digital-collections', [
                    'page' => $page,
                    'per_page' => $perPage
                ]);
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                return null;
                
            } catch (\Exception $e) {
                Log::error('Khastara Digital API exception: ' . $e->getMessage());
                return null;
            }
        });
    }
    
    public function getStatistics()
    {
        return Cache::remember('khastara_statistics', 7200, function () {
            try {
                $response = Http::timeout(10)->get($this->baseUrl . '/statistics');
                
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
        // Mock detail data
        $mockData = [
            'nk-001' => [
                'catalog_id' => 'nk-001',
                'title' => 'Serat Centhini',
                'cover_utama' => 'https://khastara.perpusnas.go.id/assets/images/placeholder/manuscript.jpg',
                'create_date' => '2024-01-01',
                'worksheet_name' => 'Naskah Kuno',
                'language_name' => 'Jawa',
                'description' => 'Serat Centhini adalah karya sastra Jawa klasik yang berisi ajaran-ajaran kehidupan, filosofi, dan budaya Jawa. Naskah ini merupakan salah satu karya terpenting dalam khazanah sastra Jawa.',
                'author' => 'Pakubuwono V',
                'year' => '1814',
                'pages' => '2.774 halaman',
                'size' => '21 x 33 cm',
                'material' => 'Kertas Eropa',
                'script' => 'Aksara Jawa',
                'condition' => 'Baik',
                'location' => 'Perpustakaan Nasional RI',
                'external_url' => 'https://khastara.perpusnas.go.id/koleksi-digital/detail?catId=nk-001'
            ],
            'bl-001' => [
                'catalog_id' => 'bl-001',
                'title' => 'Sejarah Melayu',
                'cover_utama' => 'https://khastara.perpusnas.go.id/assets/images/placeholder/manuscript.jpg',
                'create_date' => '2024-02-01',
                'worksheet_name' => 'Buku Langka',
                'language_name' => 'Melayu',
                'description' => 'Sejarah Melayu adalah karya historiografi Melayu klasik yang menceritakan sejarah kerajaan-kerajaan Melayu, khususnya Kesultanan Melaka.',
                'author' => 'Tun Sri Lanang',
                'year' => '1612',
                'pages' => '156 halaman',
                'size' => '19 x 25 cm',
                'material' => 'Kertas Cina',
                'script' => 'Aksara Jawi',
                'condition' => 'Baik',
                'location' => 'Perpustakaan Nasional RI',
                'external_url' => 'https://khastara.perpusnas.go.id/koleksi-digital/detail?catId=bl-001'
            ]
        ];
        
        return $mockData[$id] ?? null;
    }
}
