<?php

namespace App\Livewire\Staff\Analytics;

use App\Models\Setting;
use App\Models\Visit;
use App\Models\Branch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AnalyticsDashboard extends Component
{
    public string $activeTab = 'online';
    public string $period = '7d';
    public string $visitPeriod = '7d';
    public ?int $visitBranch = null;
    public bool $isConfigured = false;
    public bool $isLoading = true;
    
    public array $stats = [];
    public array $pageViews = [];
    public array $topPages = [];
    public array $topCountries = [];
    public array $devices = [];
    public array $trafficSources = [];
    
    // Visit stats
    public array $visitStats = [];
    public array $visitDaily = [];
    public array $visitByPurpose = [];
    public array $visitByHour = [];
    public array $visitTopMembers = [];
    public array $visitRecent = [];
    
    public function mount()
    {
        $this->checkConfiguration();
    }
    
    public function checkConfiguration(): void
    {
        $propertyId = Setting::get('ga_property_id');
        $serviceAccount = Setting::get('ga_service_account_json');
        
        $this->isConfigured = !empty($propertyId) && !empty($serviceAccount);
    }
    
    public function loadData()
    {
        if (!$this->isConfigured) {
            $this->isLoading = false;
            return;
        }
        
        $this->isLoading = true;
        
        $cacheKey = "ga_data_{$this->period}";
        $cacheTtl = 300; // 5 minutes
        
        $data = Cache::remember($cacheKey, $cacheTtl, function () {
            return $this->fetchAnalyticsData();
        });
        
        $this->stats = $data['stats'] ?? [];
        $this->pageViews = $data['pageViews'] ?? [];
        $this->topPages = $data['topPages'] ?? [];
        $this->topCountries = $data['topCountries'] ?? [];
        $this->devices = $data['devices'] ?? [];
        $this->trafficSources = $data['trafficSources'] ?? [];
        
        $this->isLoading = false;
    }
    
    public function updatedPeriod()
    {
        Cache::forget("ga_data_{$this->period}");
        $this->loadData();
    }
    
    protected function fetchAnalyticsData(): array
    {
        try {
            $propertyId = Setting::get('ga_property_id');
            $serviceAccountJson = Setting::get('ga_service_account_json');
            
            if (empty($propertyId) || empty($serviceAccountJson)) {
                return $this->getDemoData();
            }
            
            $accessToken = $this->getAccessToken($serviceAccountJson);
            if (!$accessToken) {
                return $this->getDemoData();
            }
            
            $dateRange = $this->getDateRange();
            
            // Fetch main report
            $response = Http::withToken($accessToken)
                ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", [
                    'dateRanges' => [['startDate' => $dateRange['start'], 'endDate' => $dateRange['end']]],
                    'metrics' => [
                        ['name' => 'activeUsers'],
                        ['name' => 'sessions'],
                        ['name' => 'screenPageViews'],
                        ['name' => 'bounceRate'],
                        ['name' => 'averageSessionDuration'],
                        ['name' => 'newUsers'],
                    ],
                ]);
            
            if (!$response->successful()) {
                \Log::warning('GA API Error', ['response' => $response->json()]);
                return $this->getDemoData();
            }
            
            $mainData = $response->json();
            $totals = $mainData['rows'][0]['metricValues'] ?? [];
            
            $stats = [
                'users' => (int) ($totals[0]['value'] ?? 0),
                'sessions' => (int) ($totals[1]['value'] ?? 0),
                'pageviews' => (int) ($totals[2]['value'] ?? 0),
                'bounceRate' => round((float) ($totals[3]['value'] ?? 0) * 100, 1),
                'avgDuration' => round((float) ($totals[4]['value'] ?? 0)),
                'newUsers' => (int) ($totals[5]['value'] ?? 0),
            ];
            
            // Fetch daily page views
            $dailyResponse = Http::withToken($accessToken)
                ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", [
                    'dateRanges' => [['startDate' => $dateRange['start'], 'endDate' => $dateRange['end']]],
                    'dimensions' => [['name' => 'date']],
                    'metrics' => [['name' => 'screenPageViews'], ['name' => 'activeUsers']],
                    'orderBys' => [['dimension' => ['dimensionName' => 'date']]],
                ]);
            
            $pageViews = [];
            if ($dailyResponse->successful()) {
                foreach ($dailyResponse->json()['rows'] ?? [] as $row) {
                    $date = $row['dimensionValues'][0]['value'] ?? '';
                    $pageViews[] = [
                        'date' => substr($date, 4, 2) . '/' . substr($date, 6, 2),
                        'views' => (int) ($row['metricValues'][0]['value'] ?? 0),
                        'users' => (int) ($row['metricValues'][1]['value'] ?? 0),
                    ];
                }
            }
            
            // Fetch top pages
            $pagesResponse = Http::withToken($accessToken)
                ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", [
                    'dateRanges' => [['startDate' => $dateRange['start'], 'endDate' => $dateRange['end']]],
                    'dimensions' => [['name' => 'pagePath']],
                    'metrics' => [['name' => 'screenPageViews']],
                    'orderBys' => [['metric' => ['metricName' => 'screenPageViews'], 'desc' => true]],
                    'limit' => 10,
                ]);
            
            $topPages = [];
            if ($pagesResponse->successful()) {
                foreach ($pagesResponse->json()['rows'] ?? [] as $row) {
                    $topPages[] = [
                        'path' => $row['dimensionValues'][0]['value'] ?? '',
                        'views' => (int) ($row['metricValues'][0]['value'] ?? 0),
                    ];
                }
            }
            
            // Fetch devices
            $devicesResponse = Http::withToken($accessToken)
                ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", [
                    'dateRanges' => [['startDate' => $dateRange['start'], 'endDate' => $dateRange['end']]],
                    'dimensions' => [['name' => 'deviceCategory']],
                    'metrics' => [['name' => 'activeUsers']],
                ]);
            
            $devices = [];
            if ($devicesResponse->successful()) {
                foreach ($devicesResponse->json()['rows'] ?? [] as $row) {
                    $devices[] = [
                        'device' => ucfirst($row['dimensionValues'][0]['value'] ?? 'Unknown'),
                        'users' => (int) ($row['metricValues'][0]['value'] ?? 0),
                    ];
                }
            }
            
            // Fetch traffic sources
            $sourcesResponse = Http::withToken($accessToken)
                ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", [
                    'dateRanges' => [['startDate' => $dateRange['start'], 'endDate' => $dateRange['end']]],
                    'dimensions' => [['name' => 'sessionDefaultChannelGroup']],
                    'metrics' => [['name' => 'sessions']],
                    'orderBys' => [['metric' => ['metricName' => 'sessions'], 'desc' => true]],
                    'limit' => 5,
                ]);
            
            $trafficSources = [];
            if ($sourcesResponse->successful()) {
                foreach ($sourcesResponse->json()['rows'] ?? [] as $row) {
                    $trafficSources[] = [
                        'source' => $row['dimensionValues'][0]['value'] ?? 'Unknown',
                        'sessions' => (int) ($row['metricValues'][0]['value'] ?? 0),
                    ];
                }
            }
            
            return [
                'stats' => $stats,
                'pageViews' => $pageViews,
                'topPages' => $topPages,
                'devices' => $devices,
                'trafficSources' => $trafficSources,
            ];
            
        } catch (\Exception $e) {
            \Log::error('GA Fetch Error', ['error' => $e->getMessage()]);
            return $this->getDemoData();
        }
    }
    
    protected function getAccessToken(string $serviceAccountJson): ?string
    {
        try {
            $credentials = json_decode($serviceAccountJson, true);
            if (!$credentials || !isset($credentials['private_key']) || !isset($credentials['client_email'])) {
                return null;
            }
            
            $now = time();
            $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $claim = base64_encode(json_encode([
                'iss' => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/analytics.readonly',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
            ]));
            
            $signature = '';
            openssl_sign(
                "$header.$claim",
                $signature,
                $credentials['private_key'],
                OPENSSL_ALGO_SHA256
            );
            
            $jwt = "$header.$claim." . base64_encode($signature);
            
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);
            
            if ($response->successful()) {
                return $response->json()['access_token'] ?? null;
            }
            
            return null;
        } catch (\Exception $e) {
            \Log::error('GA Token Error', ['error' => $e->getMessage()]);
            return null;
        }
    }
    
    protected function getDateRange(): array
    {
        return match($this->period) {
            '7d' => ['start' => '7daysAgo', 'end' => 'today'],
            '30d' => ['start' => '30daysAgo', 'end' => 'today'],
            '90d' => ['start' => '90daysAgo', 'end' => 'today'],
            default => ['start' => '7daysAgo', 'end' => 'today'],
        };
    }
    
    protected function getDemoData(): array
    {
        // Generate demo data for preview
        $days = $this->period === '30d' ? 30 : ($this->period === '90d' ? 90 : 7);
        $pageViews = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $pageViews[] = [
                'date' => $date->format('m/d'),
                'views' => rand(50, 200),
                'users' => rand(20, 80),
            ];
        }
        
        return [
            'stats' => [
                'users' => rand(500, 2000),
                'sessions' => rand(800, 3000),
                'pageviews' => rand(2000, 8000),
                'bounceRate' => rand(30, 60),
                'avgDuration' => rand(60, 180),
                'newUsers' => rand(200, 800),
            ],
            'pageViews' => $pageViews,
            'topPages' => [
                ['path' => '/', 'views' => rand(500, 1000)],
                ['path' => '/search', 'views' => rand(300, 600)],
                ['path' => '/books', 'views' => rand(200, 400)],
                ['path' => '/news', 'views' => rand(100, 300)],
                ['path' => '/ethesis', 'views' => rand(50, 200)],
            ],
            'devices' => [
                ['device' => 'Desktop', 'users' => rand(300, 600)],
                ['device' => 'Mobile', 'users' => rand(400, 800)],
                ['device' => 'Tablet', 'users' => rand(50, 150)],
            ],
            'trafficSources' => [
                ['source' => 'Direct', 'sessions' => rand(200, 500)],
                ['source' => 'Organic Search', 'sessions' => rand(300, 700)],
                ['source' => 'Referral', 'sessions' => rand(100, 300)],
                ['source' => 'Social', 'sessions' => rand(50, 150)],
            ],
        ];
    }

    public function loadVisitData()
    {
        $days = match($this->visitPeriod) {
            '7d' => 7, '30d' => 30, '90d' => 90, 'today' => 0, default => 7
        };
        
        $startDate = $days === 0 ? today() : now()->subDays($days)->startOfDay();
        
        $query = Visit::where('visited_at', '>=', $startDate);
        if ($this->visitBranch) {
            $query->where('branch_id', $this->visitBranch);
        }
        
        // Main stats
        $this->visitStats = [
            'total' => (clone $query)->count(),
            'members' => (clone $query)->where('visitor_type', 'member')->count(),
            'guests' => (clone $query)->where('visitor_type', 'guest')->count(),
            'today' => Visit::whereDate('visited_at', today())
                ->when($this->visitBranch, fn($q) => $q->where('branch_id', $this->visitBranch))->count(),
            'avg_daily' => $days > 0 ? round((clone $query)->count() / $days, 1) : (clone $query)->count(),
        ];
        
        // Daily trend
        $this->visitDaily = (clone $query)
            ->selectRaw('DATE(visited_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();
        
        // By purpose
        $this->visitByPurpose = (clone $query)
            ->selectRaw('purpose, COUNT(*) as count')
            ->groupBy('purpose')
            ->pluck('count', 'purpose')
            ->toArray();
        
        // By hour (peak hours)
        $this->visitByHour = (clone $query)
            ->selectRaw('HOUR(visited_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();
        
        // Top members
        $this->visitTopMembers = Visit::where('visited_at', '>=', $startDate)
            ->where('visitor_type', 'member')
            ->when($this->visitBranch, fn($q) => $q->where('branch_id', $this->visitBranch))
            ->selectRaw('member_id, COUNT(*) as visit_count')
            ->groupBy('member_id')
            ->orderByDesc('visit_count')
            ->limit(10)
            ->with('member:id,name,member_id')
            ->get()
            ->toArray();
        
        // Recent visits
        $this->visitRecent = Visit::where('visited_at', '>=', $startDate)
            ->when($this->visitBranch, fn($q) => $q->where('branch_id', $this->visitBranch))
            ->with(['member:id,name,member_id', 'branch:id,name'])
            ->orderByDesc('visited_at')
            ->limit(20)
            ->get()
            ->toArray();
    }

    public function updatedVisitPeriod()
    {
        $this->loadVisitData();
    }

    public function updatedVisitBranch()
    {
        $this->loadVisitData();
    }

    public function render()
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        
        if ($this->activeTab === 'offline' && empty($this->visitStats)) {
            $this->loadVisitData();
        }
        
        return view('livewire.staff.analytics.analytics-dashboard', [
            'branches' => $branches,
        ])->extends('staff.layouts.app')
            ->section('content');
    }
}
