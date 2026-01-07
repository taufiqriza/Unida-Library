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
                    'url' => 'https://khastara.perpusnas.go.id/koleksi-digital/detail?catId=' . ($item['catalog_id'] ?? '')
                ];
            });
        }
        
        return collect();
    }
}
