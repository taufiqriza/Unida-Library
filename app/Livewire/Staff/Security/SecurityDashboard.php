<?php

namespace App\Livewire\Staff\Security;

use Livewire\Component;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\Services\ContentFilterService;

class SecurityDashboard extends Component
{
    public array $stats = [];
    public array $recentLogs = [];
    public array $blockedIps = [];
    public array $failedLogins = [];
    public array $contentStats = [];
    public string $scanResult = '';
    public bool $isScanning = false;

    public function mount()
    {
        $this->loadStats();
        $this->loadRecentLogs();
        $this->loadFailedLogins();
        $this->loadContentStats();
    }

    public function loadStats()
    {
        $logPath = storage_path('logs/laravel.log');
        $securityLogPath = storage_path('logs/security.log');
        
        $logContent = File::exists($logPath) ? File::get($logPath) : '';
        $securityContent = File::exists($securityLogPath) ? File::get($securityLogPath) : '';
        
        $this->stats = [
            'blocked_requests' => substr_count($logContent, 'Blocked suspicious content'),
            'rate_limited' => substr_count($logContent, 'Rate limit exceeded'),
            'honeypot_triggered' => substr_count($logContent, 'Honeypot triggered'),
            'suspicious_agents' => substr_count($logContent, 'Suspicious user agent'),
            'failed_logins' => $this->countFailedLogins(),
            'content_violations' => substr_count($securityContent, 'Content filter violation'),
            'spam_blocked' => substr_count($securityContent, 'Gambling/spam content'),
            'last_scan' => $this->getLastScanTime(),
        ];
    }

    protected function countFailedLogins(): int
    {
        return Cache::get('failed_login_count_' . now()->format('Y-m-d'), 0);
    }

    public function loadFailedLogins()
    {
        $logPath = storage_path('logs/laravel.log');
        if (!File::exists($logPath)) {
            $this->failedLogins = [];
            return;
        }
        
        $content = File::get($logPath);
        $lines = array_filter(explode("\n", $content));
        
        $failed = [];
        foreach ($lines as $line) {
            if (strpos($line, 'Failed login attempt') !== false) {
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*Failed login attempt \{(.+)\}/', $line, $matches)) {
                    $data = json_decode('{' . $matches[2] . '}', true);
                    if ($data) {
                        $failed[] = [
                            'time' => $matches[1],
                            'ip' => $data['ip'] ?? '-',
                            'identifier' => $data['identifier'] ?? '-',
                            'user_agent' => $this->parseUserAgent($data['user_agent'] ?? ''),
                        ];
                    }
                }
            }
        }
        
        $this->failedLogins = array_slice(array_reverse($failed), 0, 50);
    }

    protected function parseUserAgent(?string $ua): string
    {
        if (!$ua) return '-';
        if (strpos($ua, 'Chrome') !== false) return 'Chrome';
        if (strpos($ua, 'Firefox') !== false) return 'Firefox';
        if (strpos($ua, 'Safari') !== false) return 'Safari';
        if (strpos($ua, 'Edge') !== false) return 'Edge';
        return 'Other';
    }

    protected function getLastScanTime(): ?string
    {
        $scans = glob(storage_path('logs/security-scan-*.json'));
        if (empty($scans)) return null;
        
        rsort($scans);
        $lastScan = basename($scans[0], '.json');
        $date = str_replace('security-scan-', '', $lastScan);
        
        try {
            return \Carbon\Carbon::createFromFormat('Y-m-d-His', $date)->diffForHumans();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function loadRecentLogs()
    {
        $logPath = storage_path('logs/laravel.log');
        if (!File::exists($logPath)) {
            $this->recentLogs = [];
            return;
        }
        
        $content = File::get($logPath);
        $lines = array_filter(explode("\n", $content));
        $lines = array_slice($lines, -200); // Last 200 lines
        
        $securityLogs = [];
        $keywords = ['Blocked', 'Rate limit', 'Honeypot', 'Suspicious', 'security', 'blocked'];
        
        foreach ($lines as $line) {
            foreach ($keywords as $keyword) {
                if (stripos($line, $keyword) !== false) {
                    // Parse log line
                    if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*?(\w+)\.(\w+): (.+)/', $line, $matches)) {
                        $securityLogs[] = [
                            'time' => $matches[1],
                            'level' => $matches[3],
                            'message' => substr($matches[4], 0, 150),
                        ];
                    }
                    break;
                }
            }
        }
        
        $this->recentLogs = array_slice(array_reverse($securityLogs), 0, 20);
    }

    public function runScan()
    {
        $this->isScanning = true;
        $this->scanResult = '';
        
        // Run security scan
        \Artisan::call('security:scan');
        $this->scanResult = \Artisan::output();
        
        $this->isScanning = false;
        $this->loadStats();
    }

    public function runIntegrityCheck()
    {
        \Artisan::call('security:integrity');
        $this->scanResult = \Artisan::output();
    }

    public function initializeBaseline()
    {
        \Artisan::call('security:integrity', ['--init' => true]);
        $this->scanResult = \Artisan::output();
    }

    public function clearLogs()
    {
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            File::put($logPath, '');
        }
        $this->loadStats();
        $this->loadRecentLogs();
    }

    public function loadContentStats()
    {
        $contentService = app(ContentFilterService::class);
        $this->contentStats = $contentService->getStats();
    }

    public function render()
    {
        return view('livewire.staff.security.security-dashboard')
            ->extends('staff.layouts.app')
            ->section('content');
    }
}
