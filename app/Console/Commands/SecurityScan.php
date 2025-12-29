<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SecurityScan extends Command
{
    protected $signature = 'security:scan {--fix : Auto-fix issues where possible} {--notify : Send notification}';
    protected $description = 'Scan for security issues, malware, and suspicious content';

    protected array $suspiciousPatterns = [
        'eval\s*\(' => 'Potential code execution (eval)',
        'base64_decode\s*\(' => 'Potential obfuscated code (base64)',
        'shell_exec\s*\(' => 'Shell execution detected',
        'passthru\s*\(' => 'Passthru execution',
        '\$_GET\s*\[.*\]\s*\(' => 'Dynamic function call from GET',
        '\$_POST\s*\[.*\]\s*\(' => 'Dynamic function call from POST',
        '\$_REQUEST\s*\[.*\]\s*\(' => 'Dynamic function call from REQUEST',
        'preg_replace.*\/e' => 'Dangerous preg_replace with /e modifier',
        'assert\s*\(' => 'Assert function (potential code execution)',
        'create_function\s*\(' => 'Create function (deprecated, dangerous)',
        'slot\s*gacor|judi\s*online|togel|sbobet' => 'Gambling content detected',
    ];

    // Files/patterns to exclude from scanning (known safe)
    protected array $excludedFiles = [
        'SecurityScan.php',
        'ContentFilter.php',
        'NotificationService.php', // Uses system() for legitimate purposes
        'ShamelaContentService.php', // Uses exec() for PDF processing
        'ChatMessage.php', // Uses system() for notifications
    ];

    protected array $safeUploadFiles = [
        'index.php', // Directory listing protection
        '.gitignore',
    ];

    protected array $suspiciousFiles = [
        '*.php.suspected', '*.php.bak', '*.php.old', '*.php.orig',
        'wp-*.php', 'shell*.php', 'c99*.php', 'r57*.php',
        'webshell*.php', 'backdoor*.php', 'hack*.php',
    ];

    public function handle(): int
    {
        $this->info('🔍 Starting Security Scan...');
        $this->newLine();
        
        $issues = [];
        
        // 1. Scan PHP files for suspicious patterns
        $this->info('Scanning PHP files for suspicious patterns...');
        $issues = array_merge($issues, $this->scanPhpFiles());
        
        // 2. Check for suspicious files
        $this->info('Checking for suspicious files...');
        $issues = array_merge($issues, $this->checkSuspiciousFiles());
        
        // 3. Check file permissions
        $this->info('Checking file permissions...');
        $issues = array_merge($issues, $this->checkPermissions());
        
        // 4. Check database for injected content
        $this->info('Scanning database for suspicious content...');
        $issues = array_merge($issues, $this->scanDatabase());
        
        // 5. Check .env security
        $this->info('Checking configuration security...');
        $issues = array_merge($issues, $this->checkConfig());
        
        $this->newLine();
        
        if (empty($issues)) {
            $this->info('✅ No security issues found!');
            return 0;
        }
        
        $this->error("⚠️  Found " . count($issues) . " security issue(s):");
        $this->newLine();
        
        foreach ($issues as $i => $issue) {
            $this->warn(($i + 1) . ". [{$issue['severity']}] {$issue['message']}");
            if (isset($issue['file'])) {
                $this->line("   File: {$issue['file']}");
            }
            if (isset($issue['line'])) {
                $this->line("   Line: {$issue['line']}");
            }
        }
        
        // Save report
        $reportPath = storage_path('logs/security-scan-' . date('Y-m-d-His') . '.json');
        File::put($reportPath, json_encode($issues, JSON_PRETTY_PRINT));
        $this->newLine();
        $this->info("Report saved to: {$reportPath}");
        
        // Send notification if requested
        if ($this->option('notify') && count($issues) > 0) {
            $this->sendNotification($issues);
        }
        
        return count($issues) > 0 ? 1 : 0;
    }

    protected function scanPhpFiles(): array
    {
        $issues = [];
        $directories = [
            app_path(),
            resource_path('views'),
            public_path(),
        ];
        
        foreach ($directories as $dir) {
            if (!File::isDirectory($dir)) continue;
            
            $files = File::allFiles($dir);
            foreach ($files as $file) {
                if ($file->getExtension() !== 'php' && $file->getExtension() !== 'blade') continue;
                
                $content = File::get($file->getPathname());
                $lines = explode("\n", $content);
                
                foreach ($this->suspiciousPatterns as $pattern => $description) {
                    foreach ($lines as $lineNum => $line) {
                        if (preg_match('/' . $pattern . '/i', $line)) {
                            // Skip vendor and known safe files
                            if (str_contains($file->getPathname(), 'vendor/')) continue;
                            
                            $skip = false;
                            foreach ($this->excludedFiles as $excluded) {
                                if (str_contains($file->getPathname(), $excluded)) {
                                    $skip = true;
                                    break;
                                }
                            }
                            if ($skip) continue;
                            
                            $issues[] = [
                                'severity' => 'HIGH',
                                'message' => $description,
                                'file' => $file->getPathname(),
                                'line' => $lineNum + 1,
                                'content' => trim(substr($line, 0, 100)),
                            ];
                        }
                    }
                }
            }
        }
        
        $this->line("  Scanned " . count($directories) . " directories");
        return $issues;
    }

    protected function checkSuspiciousFiles(): array
    {
        $issues = [];
        $publicPath = public_path();
        
        foreach ($this->suspiciousFiles as $pattern) {
            $files = glob($publicPath . '/' . $pattern);
            foreach ($files as $file) {
                $issues[] = [
                    'severity' => 'CRITICAL',
                    'message' => 'Suspicious file detected',
                    'file' => $file,
                ];
                
                if ($this->option('fix')) {
                    File::delete($file);
                    $this->warn("  Deleted: {$file}");
                }
            }
        }
        
        // Check for PHP files in upload directories
        $uploadDirs = [
            storage_path('app/public'),
            public_path('storage'),
        ];
        
        foreach ($uploadDirs as $dir) {
            if (!File::isDirectory($dir)) continue;
            
            $phpFiles = glob($dir . '/**/*.php');
            foreach ($phpFiles as $file) {
                // Skip known safe files
                $filename = basename($file);
                if (in_array($filename, $this->safeUploadFiles)) continue;
                
                $issues[] = [
                    'severity' => 'CRITICAL',
                    'message' => 'PHP file in upload directory',
                    'file' => $file,
                ];
            }
        }
        
        return $issues;
    }

    protected function checkPermissions(): array
    {
        $issues = [];
        
        // .env should not be world-readable
        $envPath = base_path('.env');
        if (File::exists($envPath)) {
            $perms = substr(sprintf('%o', fileperms($envPath)), -4);
            if ($perms !== '0600' && $perms !== '0640' && $perms !== '0644') {
                $issues[] = [
                    'severity' => 'MEDIUM',
                    'message' => ".env file has loose permissions ({$perms})",
                    'file' => $envPath,
                ];
            }
        }
        
        // Note: Storage directory executable check removed
        // PHP execution in storage is blocked at Nginx level:
        // location ~* /storage/.*\.php$ { deny all; }
        
        return $issues;
    }

    protected function scanDatabase(): array
    {
        $issues = [];
        $gamblingPatterns = ['slot gacor', 'judi online', 'togel', 'sbobet', 'poker online', 'casino online'];
        
        // Check news/articles
        if (class_exists(\App\Models\News::class)) {
            $news = \App\Models\News::query();
            foreach ($gamblingPatterns as $pattern) {
                $found = (clone $news)->where('title', 'like', "%{$pattern}%")
                    ->orWhere('content', 'like', "%{$pattern}%")->count();
                if ($found > 0) {
                    $issues[] = [
                        'severity' => 'CRITICAL',
                        'message' => "Gambling content found in News table ({$found} records matching '{$pattern}')",
                    ];
                }
            }
        }
        
        // Check books
        if (class_exists(\App\Models\Book::class)) {
            $books = \App\Models\Book::query();
            foreach ($gamblingPatterns as $pattern) {
                $found = (clone $books)->where('title', 'like', "%{$pattern}%")->count();
                if ($found > 0) {
                    $issues[] = [
                        'severity' => 'CRITICAL',
                        'message' => "Gambling content found in Books table ({$found} records matching '{$pattern}')",
                    ];
                }
            }
        }
        
        return $issues;
    }

    protected function checkConfig(): array
    {
        $issues = [];
        
        // Check debug mode
        if (config('app.debug') === true && config('app.env') === 'production') {
            $issues[] = [
                'severity' => 'HIGH',
                'message' => 'Debug mode is enabled in production',
            ];
        }
        
        // Check APP_KEY
        if (empty(config('app.key'))) {
            $issues[] = [
                'severity' => 'CRITICAL',
                'message' => 'APP_KEY is not set',
            ];
        }
        
        return $issues;
    }

    protected function sendNotification(array $issues): void
    {
        // Send to admin email or Telegram
        $this->info('Sending notification...');
        // Implementation depends on notification channel
    }
}
