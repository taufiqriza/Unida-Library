<?php

namespace App\Console\Commands;

use App\Models\PlagiarismCheck;
use App\Services\Plagiarism\CertificateGenerator;
use App\Services\Plagiarism\Providers\IthenticateProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PollPlagiarismResults extends Command
{
    protected $signature = 'plagiarism:poll';
    protected $description = 'Poll iThenticate for pending results';

    public function handle(): int
    {
        $checks = PlagiarismCheck::whereIn('status', ['processing', 'submitted'])
            ->whereNotNull('external_id')
            ->where('created_at', '>', now()->subDays(3))
            ->get();

        if ($checks->isEmpty()) {
            return 0;
        }

        $this->info("Polling {$checks->count()} pending checks...");

        $provider = new IthenticateProvider();
        if (!$provider->isConfigured()) {
            $this->error('iThenticate not configured');
            return 1;
        }

        foreach ($checks as $check) {
            $this->processCheck($check, $provider);
        }

        return 0;
    }

    protected function processCheck(PlagiarismCheck $check, IthenticateProvider $provider): void
    {
        $this->line("  #{$check->id}: {$check->external_id}");

        try {
            $result = $provider->checkStatus($check->external_id);

            if (!$result) {
                $this->warn("    → Still processing");
                return;
            }

            $check->update([
                'status' => PlagiarismCheck::STATUS_COMPLETED,
                'similarity_score' => $result['score'],
                'similarity_sources' => $result['sources'],
                'detailed_report' => $result['report'],
                'provider' => 'ithenticate',
                'completed_at' => now(),
                'error_message' => null,
            ]);

            $this->info("    ✓ Score: {$result['score']}%");
            Log::info("Plagiarism #{$check->id} completed via poll. Score: {$result['score']}%");

            // Generate certificate
            (new CertificateGenerator($check))->generate();

            // Notify
            if ($check->member?->email) {
                $check->member->notify(new \App\Notifications\PlagiarismCheckCompleted($check));
            }

        } catch (\Exception $e) {
            $this->error("    ✗ " . $e->getMessage());
        }
    }
}
