<?php

namespace App\Console\Commands;

use App\Services\OjsSyncService;
use Illuminate\Console\Command;

class SyncJournals extends Command
{
    protected $signature = 'journals:sync {--source= : Specific journal code to sync}';
    protected $description = 'Sync journal articles from OJS feeds';

    public function handle(OjsSyncService $service): int
    {
        $sourceCode = $this->option('source');

        if ($sourceCode) {
            $source = \App\Models\JournalSource::where('code', $sourceCode)->first();
            if (!$source) {
                $this->error("Journal source '{$sourceCode}' not found.");
                return 1;
            }
            $results = [$sourceCode => $service->syncSource($source)];
        } else {
            $results = $service->syncAll();
        }

        foreach ($results as $code => $result) {
            if ($result['success'] ?? false) {
                $this->info("✓ {$code}: {$result['created']} new, {$result['updated']} updated");
            } else {
                $this->error("✗ {$code}: " . ($result['error'] ?? 'Unknown error'));
            }
        }

        return 0;
    }
}
