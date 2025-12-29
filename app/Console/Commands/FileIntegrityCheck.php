<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class FileIntegrityCheck extends Command
{
    protected $signature = 'security:integrity {--init : Initialize baseline} {--notify : Send notification on changes}';
    protected $description = 'Check file integrity against baseline';

    protected array $monitoredPaths = [
        'app',
        'config',
        'routes',
        'bootstrap',
        'public/index.php',
    ];

    protected array $excludedPaths = [
        'storage',
        'vendor',
        'node_modules',
        '.git',
    ];

    public function handle(): int
    {
        $baselinePath = storage_path('app/security/file-baseline.json');
        
        if ($this->option('init')) {
            return $this->initializeBaseline($baselinePath);
        }
        
        if (!File::exists($baselinePath)) {
            $this->error('Baseline not found. Run with --init first.');
            return 1;
        }
        
        return $this->checkIntegrity($baselinePath);
    }

    protected function initializeBaseline(string $baselinePath): int
    {
        $this->info('Initializing file integrity baseline...');
        
        $hashes = $this->calculateHashes();
        
        File::ensureDirectoryExists(dirname($baselinePath));
        File::put($baselinePath, json_encode([
            'created_at' => now()->toIso8601String(),
            'files' => $hashes,
        ], JSON_PRETTY_PRINT));
        
        $this->info("✅ Baseline created with " . count($hashes) . " files");
        $this->info("Saved to: {$baselinePath}");
        
        return 0;
    }

    protected function checkIntegrity(string $baselinePath): int
    {
        $this->info('Checking file integrity...');
        
        $baseline = json_decode(File::get($baselinePath), true);
        $currentHashes = $this->calculateHashes();
        $baselineHashes = $baseline['files'];
        
        $changes = [
            'modified' => [],
            'added' => [],
            'deleted' => [],
        ];
        
        // Check for modified and deleted files
        foreach ($baselineHashes as $file => $hash) {
            if (!isset($currentHashes[$file])) {
                $changes['deleted'][] = $file;
            } elseif ($currentHashes[$file] !== $hash) {
                $changes['modified'][] = $file;
            }
        }
        
        // Check for new files
        foreach ($currentHashes as $file => $hash) {
            if (!isset($baselineHashes[$file])) {
                $changes['added'][] = $file;
            }
        }
        
        $totalChanges = count($changes['modified']) + count($changes['added']) + count($changes['deleted']);
        
        if ($totalChanges === 0) {
            $this->info('✅ No file changes detected');
            return 0;
        }
        
        $this->warn("⚠️  Detected {$totalChanges} file change(s):");
        $this->newLine();
        
        if (!empty($changes['modified'])) {
            $this->error('Modified files:');
            foreach ($changes['modified'] as $file) {
                $this->line("  - {$file}");
            }
        }
        
        if (!empty($changes['added'])) {
            $this->warn('New files:');
            foreach ($changes['added'] as $file) {
                $this->line("  + {$file}");
            }
        }
        
        if (!empty($changes['deleted'])) {
            $this->info('Deleted files:');
            foreach ($changes['deleted'] as $file) {
                $this->line("  x {$file}");
            }
        }
        
        // Save report
        $reportPath = storage_path('logs/integrity-' . date('Y-m-d-His') . '.json');
        File::put($reportPath, json_encode([
            'checked_at' => now()->toIso8601String(),
            'baseline_created' => $baseline['created_at'],
            'changes' => $changes,
        ], JSON_PRETTY_PRINT));
        
        $this->newLine();
        $this->info("Report saved to: {$reportPath}");
        
        if ($this->option('notify')) {
            $this->sendNotification($changes);
        }
        
        return 1;
    }

    protected function calculateHashes(): array
    {
        $hashes = [];
        
        foreach ($this->monitoredPaths as $path) {
            $fullPath = base_path($path);
            
            if (File::isFile($fullPath)) {
                $hashes[$path] = md5_file($fullPath);
                continue;
            }
            
            if (!File::isDirectory($fullPath)) continue;
            
            $files = File::allFiles($fullPath);
            foreach ($files as $file) {
                $relativePath = str_replace(base_path() . '/', '', $file->getPathname());
                
                // Skip excluded paths
                $skip = false;
                foreach ($this->excludedPaths as $excluded) {
                    if (str_starts_with($relativePath, $excluded)) {
                        $skip = true;
                        break;
                    }
                }
                if ($skip) continue;
                
                // Only check PHP files
                if (!in_array($file->getExtension(), ['php', 'blade.php'])) continue;
                
                $hashes[$relativePath] = md5_file($file->getPathname());
            }
        }
        
        ksort($hashes);
        return $hashes;
    }

    protected function sendNotification(array $changes): void
    {
        $this->info('Sending notification...');
    }
}
