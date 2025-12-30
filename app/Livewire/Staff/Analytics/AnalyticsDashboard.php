<?php

namespace App\Livewire\Staff\Analytics;

use App\Models\Setting;
use App\Models\Visit;
use App\Models\Branch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
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
    public array $devices = [];
    public array $trafficSources = [];
    public array $browsers = [];
    public array $countries = [];
    public array $cities = [];
    public array $hourlyData = [];
    public array $userTypes = [];
    
    // Realtime
    public array $realtime = [];
    
    // Visit stats
    public array $visitStats = [];
    public array $visitDaily = [];
    public array $visitByPurpose = [];
    public array $visitByHour = [];
    public array $visitTopMembers = [];
    public array $visitRecent = [];
    
    const GA_PROPERTY_ID = '347806816';
    
    public function mount()
    {
        $this->checkConfiguration();
    }
    
    public function checkConfiguration(): void
    {
        $serviceAccount = Setting::get('ga_service_account_json');
        $this->isConfigured = !empty($serviceAccount);
    }
    
    public function loadData()
    {
        if (!$this->isConfigured) {
            $this->isLoading = false;
            return;
        }
        
        $this->isLoading = true;
        
        // Load realtime first (faster)
        $this->loadRealtime();
        
        $cacheKey = "ga_full_data_{$this->period}";
        $data = Cache::remember($cacheKey, 180, fn() => $this->fetchAllAnalyticsData());
        
        $this->stats = $data['stats'] ?? [];
        $this->pageViews = $data['pageViews'] ?? [];
        $this->topPages = $data['topPages'] ?? [];
        $this->devices = $data['devices'] ?? [];
        $this->trafficSources = $data['trafficSources'] ?? [];
        $this->browsers = $data['browsers'] ?? [];
        $this->countries = $data['countries'] ?? [];
        $this->cities = $data['cities'] ?? [];
        $this->hourlyData = $data['hourlyData'] ?? [];
        $this->userTypes = $data['userTypes'] ?? [];
        
        $this->isLoading = false;
    }
    
    public function loadRealtime()
    {
        if (!$this->isConfigured) {
            $this->realtime = $this->getEmptyRealtime();
            return;
        }
        
        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                $this->realtime = $this->getEmptyRealtime();
                return;
            }
            
            $propertyId = self::GA_PROPERTY_ID;
            
            // Active users
            $activeUsers = $this->fetchRealtimeMetric($accessToken, $propertyId, 'activeUsers');
            
            // By page
            $pages = $this->fetchRealtimeDimension($accessToken, $propertyId, 'unifiedScreenName', 10);
            
            // By country with country code
            $countries = $this->fetchRealtimeDimensionWithCode($accessToken, $propertyId, 'country', 'countryId', 20);
            
            // By city
            $cities = $this->fetchRealtimeDimension($accessToken, $propertyId, 'city', 10);
            
            // By device
            $devices = $this->fetchRealtimeDimension($accessToken, $propertyId, 'deviceCategory', 5);
            
            // By source
            $sources = $this->fetchRealtimeDimension($accessToken, $propertyId, 'sessionSource', 5);
            
            // Events in last 30 min
            $events = $this->fetchRealtimeMetric($accessToken, $propertyId, 'eventCount');
            
            // Page views in last 30 min
            $pageviews = $this->fetchRealtimeMetric($accessToken, $propertyId, 'screenPageViews');
            
            $this->realtime = [
                'activeUsers' => $activeUsers,
                'pageviews30min' => $pageviews,
                'events30min' => $events,
                'pages' => $pages,
                'countries' => $countries,
                'cities' => $cities,
                'devices' => $devices,
                'sources' => $sources,
            ];
            
        } catch (\Exception $e) {
            \Log::error('GA Realtime Error', ['error' => $e->getMessage()]);
            $this->realtime = $this->getEmptyRealtime();
        }
    }
    
    protected function fetchRealtimeMetric($token, $propertyId, $metric): int
    {
        $response = Http::withToken($token)
            ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runRealtimeReport", [
                'metrics' => [['name' => $metric]],
            ]);
        
        if ($response->successful()) {
            return (int) ($response->json()['rows'][0]['metricValues'][0]['value'] ?? 0);
        }
        return 0;
    }
    
    protected function fetchRealtimeDimension($token, $propertyId, $dimension, $limit = 10): array
    {
        $response = Http::withToken($token)
            ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runRealtimeReport", [
                'dimensions' => [['name' => $dimension]],
                'metrics' => [['name' => 'activeUsers']],
                'limit' => $limit,
            ]);
        
        $result = [];
        if ($response->successful()) {
            foreach ($response->json()['rows'] ?? [] as $row) {
                $result[] = [
                    'name' => $row['dimensionValues'][0]['value'] ?? 'Unknown',
                    'users' => (int) ($row['metricValues'][0]['value'] ?? 0),
                ];
            }
        }
        return $result;
    }
    
    protected function fetchRealtimeDimensionWithCode($token, $propertyId, $dimension, $codeDimension, $limit = 20): array
    {
        $response = Http::withToken($token)
            ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runRealtimeReport", [
                'dimensions' => [['name' => $dimension], ['name' => $codeDimension]],
                'metrics' => [['name' => 'activeUsers']],
                'limit' => $limit,
            ]);
        
        $result = [];
        if ($response->successful()) {
            foreach ($response->json()['rows'] ?? [] as $row) {
                $result[] = [
                    'name' => $row['dimensionValues'][0]['value'] ?? 'Unknown',
                    'code' => $row['dimensionValues'][1]['value'] ?? '',
                    'users' => (int) ($row['metricValues'][0]['value'] ?? 0),
                ];
            }
        }
        return $result;
    }
    
    protected function getEmptyRealtime(): array
    {
        return ['activeUsers' => 0, 'pageviews30min' => 0, 'events30min' => 0, 'pages' => [], 'countries' => [], 'cities' => [], 'devices' => [], 'sources' => []];
    }
    
    protected function fetchAllAnalyticsData(): array
    {
        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) return [];
            
            $propertyId = self::GA_PROPERTY_ID;
            $dateRange = $this->getDateRange();
            
            // Main stats
            $stats = $this->fetchMainStats($accessToken, $propertyId, $dateRange);
            
            // Daily pageviews
            $pageViews = $this->fetchDailyData($accessToken, $propertyId, $dateRange);
            
            // Top pages
            $topPages = $this->fetchTopPages($accessToken, $propertyId, $dateRange);
            
            // Devices
            $devices = $this->fetchByDimension($accessToken, $propertyId, $dateRange, 'deviceCategory', 'activeUsers');
            
            // Browsers
            $browsers = $this->fetchByDimension($accessToken, $propertyId, $dateRange, 'browser', 'activeUsers', 8);
            
            // Traffic sources
            $trafficSources = $this->fetchByDimension($accessToken, $propertyId, $dateRange, 'sessionDefaultChannelGroup', 'sessions', 8);
            
            // Countries
            $countries = $this->fetchCountries($accessToken, $propertyId, $dateRange);
            
            // Cities
            $cities = $this->fetchByDimension($accessToken, $propertyId, $dateRange, 'city', 'activeUsers', 10);
            
            // Hourly data
            $hourlyData = $this->fetchHourlyData($accessToken, $propertyId, $dateRange);
            
            // User types (new vs returning)
            $userTypes = $this->fetchByDimension($accessToken, $propertyId, $dateRange, 'newVsReturning', 'activeUsers');
            
            return compact('stats', 'pageViews', 'topPages', 'devices', 'browsers', 'trafficSources', 'countries', 'cities', 'hourlyData', 'userTypes');
            
        } catch (\Exception $e) {
            \Log::error('GA Fetch Error', ['error' => $e->getMessage()]);
            return [];
        }
    }
    
    protected function fetchMainStats($token, $propertyId, $dateRange): array
    {
        $response = Http::withToken($token)
            ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", [
                'dateRanges' => [['startDate' => $dateRange['start'], 'endDate' => $dateRange['end']]],
                'metrics' => [
                    ['name' => 'activeUsers'],
                    ['name' => 'sessions'],
                    ['name' => 'screenPageViews'],
                    ['name' => 'bounceRate'],
                    ['name' => 'averageSessionDuration'],
                    ['name' => 'newUsers'],
                    ['name' => 'engagedSessions'],
                    ['name' => 'screenPageViewsPerSession'],
                    ['name' => 'userEngagementDuration'],
                ],
            ]);
        
        if (!$response->successful()) return [];
        
        $totals = $response->json()['rows'][0]['metricValues'] ?? [];
        return [
            'users' => (int) ($totals[0]['value'] ?? 0),
            'sessions' => (int) ($totals[1]['value'] ?? 0),
            'pageviews' => (int) ($totals[2]['value'] ?? 0),
            'bounceRate' => round((float) ($totals[3]['value'] ?? 0) * 100, 1),
            'avgDuration' => round((float) ($totals[4]['value'] ?? 0)),
            'newUsers' => (int) ($totals[5]['value'] ?? 0),
            'engagedSessions' => (int) ($totals[6]['value'] ?? 0),
            'pagesPerSession' => round((float) ($totals[7]['value'] ?? 0), 2),
            'totalEngagementTime' => round((float) ($totals[8]['value'] ?? 0)),
        ];
    }
    
    protected function fetchDailyData($token, $propertyId, $dateRange): array
    {
        $response = Http::withToken($token)
            ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", [
                'dateRanges' => [['startDate' => $dateRange['start'], 'endDate' => $dateRange['end']]],
                'dimensions' => [['name' => 'date']],
                'metrics' => [['name' => 'screenPageViews'], ['name' => 'activeUsers'], ['name' => 'sessions']],
                'orderBys' => [['dimension' => ['dimensionName' => 'date']]],
            ]);
        
        $result = [];
        if ($response->successful()) {
            foreach ($response->json()['rows'] ?? [] as $row) {
                $date = $row['dimensionValues'][0]['value'] ?? '';
                $result[] = [
                    'date' => substr($date, 6, 2) . '/' . substr($date, 4, 2),
                    'fullDate' => substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2),
                    'views' => (int) ($row['metricValues'][0]['value'] ?? 0),
                    'users' => (int) ($row['metricValues'][1]['value'] ?? 0),
                    'sessions' => (int) ($row['metricValues'][2]['value'] ?? 0),
                ];
            }
        }
        return $result;
    }
    
    protected function fetchTopPages($token, $propertyId, $dateRange): array
    {
        $response = Http::withToken($token)
            ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", [
                'dateRanges' => [['startDate' => $dateRange['start'], 'endDate' => $dateRange['end']]],
                'dimensions' => [['name' => 'pagePath'], ['name' => 'pageTitle']],
                'metrics' => [['name' => 'screenPageViews'], ['name' => 'activeUsers'], ['name' => 'averageSessionDuration']],
                'orderBys' => [['metric' => ['metricName' => 'screenPageViews'], 'desc' => true]],
                'limit' => 15,
            ]);
        
        $result = [];
        if ($response->successful()) {
            foreach ($response->json()['rows'] ?? [] as $row) {
                $result[] = [
                    'path' => $row['dimensionValues'][0]['value'] ?? '',
                    'title' => $row['dimensionValues'][1]['value'] ?? '',
                    'views' => (int) ($row['metricValues'][0]['value'] ?? 0),
                    'users' => (int) ($row['metricValues'][1]['value'] ?? 0),
                    'avgTime' => round((float) ($row['metricValues'][2]['value'] ?? 0)),
                ];
            }
        }
        return $result;
    }
    
    protected function fetchByDimension($token, $propertyId, $dateRange, $dimension, $metric, $limit = 10): array
    {
        $response = Http::withToken($token)
            ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", [
                'dateRanges' => [['startDate' => $dateRange['start'], 'endDate' => $dateRange['end']]],
                'dimensions' => [['name' => $dimension]],
                'metrics' => [['name' => $metric]],
                'orderBys' => [['metric' => ['metricName' => $metric], 'desc' => true]],
                'limit' => $limit,
            ]);
        
        $result = [];
        if ($response->successful()) {
            foreach ($response->json()['rows'] ?? [] as $row) {
                $result[] = [
                    'name' => ucfirst($row['dimensionValues'][0]['value'] ?? 'Unknown'),
                    'value' => (int) ($row['metricValues'][0]['value'] ?? 0),
                ];
            }
        }
        return $result;
    }
    
    protected function fetchCountries($token, $propertyId, $dateRange): array
    {
        $response = Http::withToken($token)
            ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", [
                'dateRanges' => [['startDate' => $dateRange['start'], 'endDate' => $dateRange['end']]],
                'dimensions' => [['name' => 'country'], ['name' => 'countryId']],
                'metrics' => [['name' => 'activeUsers'], ['name' => 'sessions']],
                'orderBys' => [['metric' => ['metricName' => 'activeUsers'], 'desc' => true]],
                'limit' => 20,
            ]);
        
        $result = [];
        if ($response->successful()) {
            foreach ($response->json()['rows'] ?? [] as $row) {
                $result[] = [
                    'name' => $row['dimensionValues'][0]['value'] ?? 'Unknown',
                    'code' => $row['dimensionValues'][1]['value'] ?? '',
                    'users' => (int) ($row['metricValues'][0]['value'] ?? 0),
                    'sessions' => (int) ($row['metricValues'][1]['value'] ?? 0),
                ];
            }
        }
        return $result;
    }
    
    protected function fetchHourlyData($token, $propertyId, $dateRange): array
    {
        $response = Http::withToken($token)
            ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", [
                'dateRanges' => [['startDate' => $dateRange['start'], 'endDate' => $dateRange['end']]],
                'dimensions' => [['name' => 'hour']],
                'metrics' => [['name' => 'activeUsers']],
                'orderBys' => [['dimension' => ['dimensionName' => 'hour']]],
            ]);
        
        $result = array_fill(0, 24, 0);
        if ($response->successful()) {
            foreach ($response->json()['rows'] ?? [] as $row) {
                $hour = (int) ($row['dimensionValues'][0]['value'] ?? 0);
                $result[$hour] = (int) ($row['metricValues'][0]['value'] ?? 0);
            }
        }
        return $result;
    }
    
    protected function getAccessToken(): ?string
    {
        $serviceAccountJson = Setting::get('ga_service_account_json');
        if (!$serviceAccountJson) return null;
        
        $cacheKey = 'ga_access_token';
        return Cache::remember($cacheKey, 3500, function () use ($serviceAccountJson) {
            try {
                $credentials = json_decode($serviceAccountJson, true);
                if (!$credentials || !isset($credentials['private_key'])) return null;
                
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
                openssl_sign("$header.$claim", $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256);
                $jwt = "$header.$claim." . base64_encode($signature);
                
                $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ]);
                
                return $response->successful() ? ($response->json()['access_token'] ?? null) : null;
            } catch (\Exception $e) {
                return null;
            }
        });
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
    
    public function updatedPeriod()
    {
        Cache::forget("ga_full_data_{$this->period}");
        $this->loadData();
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
        
        $this->visitStats = [
            'total' => (clone $query)->count(),
            'members' => (clone $query)->where('visitor_type', 'member')->count(),
            'guests' => (clone $query)->where('visitor_type', 'guest')->count(),
            'today' => Visit::whereDate('visited_at', today())
                ->when($this->visitBranch, fn($q) => $q->where('branch_id', $this->visitBranch))->count(),
            'avg_daily' => $days > 0 ? round((clone $query)->count() / $days, 1) : (clone $query)->count(),
        ];
        
        $this->visitDaily = (clone $query)
            ->selectRaw('DATE(visited_at) as date, COUNT(*) as count')
            ->groupBy('date')->orderBy('date')
            ->pluck('count', 'date')->toArray();
        
        $this->visitByPurpose = (clone $query)
            ->selectRaw('purpose, COUNT(*) as count')
            ->groupBy('purpose')->pluck('count', 'purpose')->toArray();
        
        $this->visitByHour = (clone $query)
            ->selectRaw('HOUR(visited_at) as hour, COUNT(*) as count')
            ->groupBy('hour')->orderBy('hour')
            ->pluck('count', 'hour')->toArray();
        
        $this->visitTopMembers = Visit::where('visited_at', '>=', $startDate)
            ->where('visitor_type', 'member')
            ->when($this->visitBranch, fn($q) => $q->where('branch_id', $this->visitBranch))
            ->selectRaw('member_id, COUNT(*) as visit_count')
            ->groupBy('member_id')->orderByDesc('visit_count')->limit(10)
            ->with('member:id,name,member_id')->get()->toArray();
        
        $this->visitRecent = Visit::where('visited_at', '>=', $startDate)
            ->when($this->visitBranch, fn($q) => $q->where('branch_id', $this->visitBranch))
            ->with(['member:id,name,member_id', 'branch:id,name'])
            ->orderByDesc('visited_at')->limit(20)->get()->toArray();
    }

    public function updatedVisitPeriod() { $this->loadVisitData(); }
    public function updatedVisitBranch() { $this->loadVisitData(); }

    public function render()
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        
        if ($this->activeTab === 'offline' && empty($this->visitStats)) {
            $this->loadVisitData();
        }
        
        return view('livewire.staff.analytics.analytics-dashboard', [
            'branches' => $branches,
        ])->extends('staff.layouts.app')->section('content');
    }
}
