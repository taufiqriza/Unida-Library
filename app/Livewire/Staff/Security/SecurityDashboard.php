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
        $logDir = storage_path('logs');
        $allLogContent = '';
        $securityContent = '';
        
        // Read all Laravel log files (last 7 days)
        for ($i = 0; $i < 7; $i++) {
            $date = now()->subDays($i)->format('Y-m-d');
            $logFile = $logDir . '/laravel-' . $date . '.log';
            
            if (File::exists($logFile)) {
                $allLogContent .= File::get($logFile) . "\n";
            }
        }
        
        // Also read main laravel.log
        $mainLog = $logDir . '/laravel.log';
        if (File::exists($mainLog)) {
            $allLogContent .= File::get($mainLog);
        }
        
        // Read security log
        $securityLog = $logDir . '/security.log';
        if (File::exists($securityLog)) {
            $securityContent = File::get($securityLog);
        }

        $this->stats = [
            'blocked_requests' => substr_count($allLogContent, 'Blocked suspicious content'),
            'rate_limited' => substr_count($allLogContent, 'Rate limit exceeded'),
            'honeypot_triggered' => substr_count($allLogContent, 'Honeypot triggered'),
            'suspicious_agents' => substr_count($allLogContent, 'Suspicious user agent'),
            'failed_logins' => $this->countFailedLogins(),
            'content_violations' => substr_count($securityContent, 'Content filter violation'),
            'spam_blocked' => substr_count($securityContent, 'Gambling/spam content'),
            'last_scan' => $this->getLastScanTime(),
        ];
    }

    protected function countFailedLogins(): int
    {
        $count = 0;
        $logDir = storage_path('logs');
        
        // Count from all Laravel log files (last 7 days)
        for ($i = 0; $i < 7; $i++) {
            $date = now()->subDays($i)->format('Y-m-d');
            $logFile = $logDir . '/laravel-' . $date . '.log';
            
            if (File::exists($logFile)) {
                $content = File::get($logFile);
                $count += substr_count($content, 'Login failed');
            }
        }
        
        // Also check main laravel.log
        $mainLog = $logDir . '/laravel.log';
        if (File::exists($mainLog)) {
            $content = File::get($mainLog);
            $count += substr_count($content, 'Login failed');
        }
        
        return $count;
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
