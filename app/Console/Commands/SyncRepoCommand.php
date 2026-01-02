<?php

namespace App\Console\Commands;

use App\Services\RepoSyncService;
use Illuminate\Console\Command;

class SyncRepoCommand extends Command
{
    protected $signature = 'repo:sync {--type= : thesis or article}';
    protected $description = 'Sync data from repo.unida.gontor.ac.id via OAI-PMH';

    public function handle(RepoSyncService $service): int
    {
        $type = $this->option('type');
        $this->info("Starting sync" . ($type ? " for {$type}" : " all") . "...");

        $stats = $service->sync($type);

        $this->table(['Type', 'Count'], [
            ['Thesis', $stats['thesis']],
            ['Article', $stats['article']],
            ['Skipped', $stats['skipped']],
            ['Errors', $stats['errors']],
        ]);

        return 0;
    }
}
