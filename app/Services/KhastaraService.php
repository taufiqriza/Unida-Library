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
        
        return Cache::remember($cacheKey, 300, function () use ($filters, $page, $perPage) {
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
        $cacheKey = 'khastara_detail_' . $id;
        
        return Cache::remember($cacheKey, 1800, function () use ($id) {
            try {
                // Search for specific manuscript by ID in the collection list
                $response = Http::timeout(15)
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'User-Agent' => 'Mozilla/5.0'
                    ])
                    ->get($this->baseUrl . '/inlis/collection-list', [
                        'per_page' => 100,
                        'page' => 1
                    ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['data'])) {
                        // Find the specific manuscript by catalog_id
                        $manuscript = collect($data['data'])->firstWhere('catalog_id', $id);
                        
                        if ($manuscript) {
                            return [
                                'catalog_id' => $manuscript['catalog_id'] ?? $id,
                                'title' => $manuscript['title'] ?? 'Tanpa Judul',
                                'cover_utama' => $manuscript['cover_utama'] ?? '/assets/images/placeholder/manuscript.svg',
                                'create_date' => $manuscript['create_date'] ?? '',
                                'worksheet_name' => $manuscript['worksheet_name'] ?? '',
                                'language_name' => $this->extractLanguage($manuscript['aksara'] ?? []),
                                'description' => isset($manuscript['list_abstraksi']) ? $manuscript['list_abstraksi'][0] : 'Deskripsi tidak tersedia',
                                'subject' => $manuscript['subject'] ?? '',
                                'author' => $manuscript['author'] ?? '',
                                'publisher' => $manuscript['publisher'] ?? '',
                                'publish_year' => $manuscript['publish_year'] ?? '',
                                'deskripsi_fisik' => $manuscript['deskripsi_fisik'] ?? '',
                                'call_number' => $manuscript['call_number'] ?? '',
                                'ddc' => $manuscript['ddc'] ?? '',
                                'aksara' => isset($manuscript['aksara']) ? (is_array($manuscript['aksara']) ? implode(', ', $manuscript['aksara']) : $manuscript['aksara']) : '',
                                'condition' => 'Baik',
                                'location' => isset($manuscript['list_lokasi']) ? implode(', ', $manuscript['list_lokasi']) : 'Perpustakaan Nasional RI',
                                'external_url' => 'https://khastara.perpusnas.go.id/koleksi-digital/detail/?catId=' . $id,
                                'view_count' => $manuscript['view_count'] ?? 0,
                                'konten_digital_count' => $manuscript['konten_digital_count'] ?? 0
                            ];
                        }
                    }
                }
                
                // If not found in first page, return basic info
                return [
                    'catalog_id' => $id,
                    'title' => 'Naskah ID: ' . $id,
                    'cover_utama' => '/assets/images/placeholder/manuscript.svg',
                    'create_date' => '',
                    'worksheet_name' => 'Manuskrip',
                    'language_name' => 'Indonesia',
                    'description' => 'Detail naskah sedang dimuat dari Khastara Perpustakaan Nasional.',
                    'subject' => '',
                    'author' => '',
                    'publisher' => '',
                    'publish_year' => '',
                    'deskripsi_fisik' => '',
                    'call_number' => '',
                    'ddc' => '',
                    'aksara' => '',
                    'condition' => 'Baik',
                    'location' => 'Perpustakaan Nasional RI',
                    'external_url' => 'https://khastara.perpusnas.go.id/koleksi-digital/detail/?catId=' . $id,
                    'view_count' => 0,
                    'konten_digital_count' => 0
                ];
                
            } catch (\Exception $e) {
                Log::error('Khastara Detail API exception: ' . $e->getMessage());
                return null;
            }
        });
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
        // Enhanced fallback data with more manuscripts
        $mockData = [
            ['catalog_id' => '50513', 'title' => 'Syarh Aja\'ib al-Qalb', 'cover_utama' => 'http://file-opac.perpusnas.go.id/uploaded_files/sampul_koleksi/original/Manuskrip/50513.JPG', 'create_date' => '11/21/2007', 'worksheet_name' => 'Manuskrip', 'language_name' => 'Arab', 'subject' => 'Tasawuf -- Manuskrip Arab', 'view_count' => 3407, 'konten_digital_count' => 13],
            ['catalog_id' => '50516', 'title' => 'Fath al-Muluk', 'cover_utama' => 'http://file-opac.perpusnas.go.id/uploaded_files/sampul_koleksi/original/Manuskrip/50516.jpg', 'create_date' => '11/21/2007', 'worksheet_name' => 'Manuskrip', 'language_name' => 'Arab', 'subject' => 'Tasawuf -- Manuskrip Arab', 'view_count' => 2038, 'konten_digital_count' => 4],
            ['catalog_id' => '50512', 'title' => 'Ilm At-Tasawwuf', 'cover_utama' => 'http://file-opac.perpusnas.go.id/uploaded_files/sampul_koleksi/original/Manuskrip/50512_1.jpg', 'create_date' => '11/21/2007', 'worksheet_name' => 'Manuskrip', 'language_name' => 'Arab', 'subject' => 'Tasawuf', 'view_count' => 2044, 'konten_digital_count' => 23],
            ['catalog_id' => '62091', 'title' => 'Babad Yogyakarta Jilid 2', 'cover_utama' => 'http://file-opac.perpusnas.go.id/uploaded_files/sampul_koleksi/original/Manuskrip/62091.jpg', 'create_date' => '11/21/2007', 'worksheet_name' => 'Manuskrip', 'language_name' => 'Jawa', 'subject' => 'Cerita historis -- Kesusastraan Jawa', 'view_count' => 2246, 'konten_digital_count' => 18],
            ['catalog_id' => '62090', 'title' => 'Babad Yogyakarta Jilid 1', 'cover_utama' => 'http://file-opac.perpusnas.go.id/uploaded_files/sampul_koleksi/original/Manuskrip/62090.jpg', 'create_date' => '11/21/2007', 'worksheet_name' => 'Manuskrip', 'language_name' => 'Jawa', 'subject' => 'Cerita historis -- Kesusastraan Jawa', 'view_count' => 1340, 'konten_digital_count' => 1],
            ['catalog_id' => '62088', 'title' => 'Wariga', 'cover_utama' => 'http://file-opac.perpusnas.go.id/uploaded_files/sampul_koleksi/original/Manuskrip/62088.jpg', 'create_date' => '11/21/2007', 'worksheet_name' => 'Manuskrip', 'language_name' => 'Jawa', 'subject' => 'Almanak Jawa -- Kesusastraan Jawa', 'view_count' => 709, 'konten_digital_count' => 7],
            ['catalog_id' => '62089', 'title' => 'Geslachtslijst Der Menak\'s in De Preanger', 'cover_utama' => 'http://file-opac.perpusnas.go.id/uploaded_files/sampul_koleksi/original/Manuskrip/62089.jpg', 'create_date' => '11/21/2007', 'worksheet_name' => 'Manuskrip', 'language_name' => 'Arab', 'subject' => 'Cerita menak (Sunda) -- Kesusastraan Arab', 'view_count' => 250, 'konten_digital_count' => 6],
            ['catalog_id' => '62438', 'title' => 'Fotocopie van de Grote Batakse', 'cover_utama' => 'http://file-opac.perpusnas.go.id/uploaded_files/sampul_koleksi/original/Manuskrip/62438.jpeg', 'create_date' => '11/21/2007', 'worksheet_name' => 'Manuskrip', 'language_name' => 'Batak', 'subject' => 'Manuskrip Batak -- Kesusastraan Batak', 'view_count' => 265, 'konten_digital_count' => 5],
            ['catalog_id' => '62434', 'title' => 'Nota van den Secretaris art.38-39', 'cover_utama' => 'http://file-opac.perpusnas.go.id/uploaded_files/sampul_koleksi/original/Manuskrip/62434.jpg', 'create_date' => '11/21/2007', 'worksheet_name' => 'Manuskrip', 'language_name' => 'Belanda', 'subject' => 'Surat, perjanjian, dsb -- Manuskrip Belanda', 'view_count' => 169, 'konten_digital_count' => 1],
            ['catalog_id' => '62436', 'title' => 'Nota van den Secretaris art.44', 'cover_utama' => 'http://file-opac.perpusnas.go.id/uploaded_files/sampul_koleksi/original/Manuskrip/62436.jpg', 'create_date' => '11/21/2007', 'worksheet_name' => 'Manuskrip', 'language_name' => 'Belanda', 'subject' => 'Surat, perjanjian, dsb -- Manuskrip Belanda', 'view_count' => 146, 'konten_digital_count' => 1],
            ['catalog_id' => '50143', 'title' => 'Hubungan faktor-faktor dalam pembentukan reputasi perusahaan publik', 'cover_utama' => 'http://file-opac.perpusnas.go.id/uploaded_files/sampul_koleksi/original/Monograf/50143.jpg', 'create_date' => '11/25/2011', 'worksheet_name' => 'Monograf', 'language_name' => 'Indonesia', 'subject' => 'Perpustakaan korporasi', 'view_count' => 1007, 'konten_digital_count' => 1],
            ['catalog_id' => '50881', 'title' => 'Tafel voor snel opslaan van Coordinaten verschillen', 'cover_utama' => 'http://file-opac.perpusnas.go.id/uploaded_files/sampul_koleksi/original/Monograf/50881.jpg', 'create_date' => '12/26/2006', 'worksheet_name' => 'Monograf', 'language_name' => 'Belanda', 'subject' => 'Koordinat - Tabel', 'view_count' => 434, 'konten_digital_count' => 1]
        ];
        
        // Apply filters if any
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
        $paginatedData = $filteredData->skip(($page - 1) * $perPage)->take($perPage);
        
        return [
            'data' => $paginatedData->values()->toArray(),
            'meta' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page
            ]
        ];
    }
}
