<?php

namespace App\Console\Commands;

use App\Services\RepoSyncService;
use Illuminate\Console\Command;

class SyncRepo extends Command
{
    protected $signature = 'repo:sync';
    protected $description = 'Sync thesis and articles from UNIDA Repository (OAI-PMH)';

    public function handle(RepoSyncService $service): int
    {
        $this->info('Syncing from repo.unida.gontor.ac.id...');
        $this->newLine();

        $result = $service->sync();

        $this->table(
            ['Type', 'Count'],
            [
                ['Thesis (E-Thesis)', $result['thesis']],
                ['Articles (Journal)', $result['article']],
                ['Skipped', $result['skipped']],
                ['Errors', $result['errors']],
            ]
        );

        $this->newLine();
        $this->info('Sync completed!');

        return 0;
    }
}
