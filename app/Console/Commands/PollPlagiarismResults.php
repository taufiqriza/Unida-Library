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
    protected $description = 'Poll iThenticate for pending plagiarism check results';

    public function handle(): int
    {
        $checks = PlagiarismCheck::whereIn('status', ['processing', 'submitted'])
            ->whereNotNull('external_id')
            ->where('created_at', '>', now()->subDays(7)) // Only check recent ones
            ->get();

        if ($checks->isEmpty()) {
            $this->info('No pending checks to poll.');
            return 0;
        }

        $this->info("Found {$checks->count()} pending checks");

        $provider = new IthenticateProvider();

        if (!$provider->isConfigured()) {
            $this->error('iThenticate not configured');
            return 1;
        }

        foreach ($checks as $check) {
            $this->pollCheck($check, $provider);
        }

        return 0;
    }

    protected function pollCheck(PlagiarismCheck $check, IthenticateProvider $provider): void
    {
        $this->info("Polling #{$check->id} (submission: {$check->external_id})");

        try {
            $result = $provider->checkStatus($check->external_id);

            if ($result === null) {
                $this->warn("  → Still processing");
                return;
            }

            // Update check with results
            $check->update([
                'status' => PlagiarismCheck::STATUS_COMPLETED,
                'similarity_score' => $result['score'],
                'similarity_sources' => $result['sources'],
                'detailed_report' => $result['report'],
                'provider' => 'ithenticate',
                'completed_at' => now(),
            ]);

            $this->info("  ✓ Completed! Score: {$result['score']}%");
            Log::info("Plagiarism #{$check->id} completed via poll. Score: {$result['score']}%");

            // Generate certificate
            try {
                (new CertificateGenerator($check))->generate();
                $this->info("  ✓ Certificate generated");
            } catch (\Exception $e) {
                $this->warn("  ! Certificate error: " . $e->getMessage());
            }

            // Send notification
            $this->sendNotification($check);

        } catch (\Exception $e) {
            $this->error("  ✗ Error: " . $e->getMessage());
            Log::warning("Poll error for #{$check->id}: " . $e->getMessage());
        }
    }

    protected function sendNotification(PlagiarismCheck $check): void
    {
        try {
            $member = $check->member;
            if ($member && $member->email) {
                $member->notify(new \App\Notifications\PlagiarismCheckCompleted($check));
                $this->info("  ✓ Email sent");
            }
        } catch (\Exception $e) {
            $this->warn("  ! Notification error: " . $e->getMessage());
        }
    }
}
