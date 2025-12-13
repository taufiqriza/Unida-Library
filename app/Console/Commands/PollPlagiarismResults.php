<?php

namespace App\Console\Commands;

use App\Models\PlagiarismCheck;
use App\Models\Setting;
use App\Services\Plagiarism\CertificateGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PollPlagiarismResults extends Command
{
    protected $signature = 'plagiarism:poll';
    protected $description = 'Poll iThenticate for pending plagiarism check results';

    public function handle(): int
    {
        $checks = PlagiarismCheck::where('status', 'processing')
            ->whereNotNull('external_id')
            ->get();

        if ($checks->isEmpty()) {
            $this->info('No processing checks to poll.');
            return 0;
        }

        $apiSecret = Setting::get('ithenticate_api_secret');
        $baseUrl = Setting::get('ithenticate_base_url', 'https://unidagontor.turnitin.com');

        foreach ($checks as $check) {
            $this->info("Polling check #{$check->id} (submission: {$check->external_id})");

            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiSecret,
                    'X-Turnitin-Integration-Name' => 'Library-Portal-API',
                    'X-Turnitin-Integration-Version' => '1.0.0',
                ])->timeout(60)->get("{$baseUrl}/api/v1/submissions/{$check->external_id}/similarity");

                if ($response->status() === 404) {
                    $this->warn("  Report not ready yet (404)");
                    continue;
                }

                if (!$response->successful()) {
                    $this->error("  API error: " . $response->status());
                    continue;
                }

                $data = $response->json();
                $status = $data['status'] ?? 'PENDING';

                $this->info("  Status: {$status}");

                if ($status === 'COMPLETE') {
                    $score = $data['overall_match_percentage'] ?? 0;
                    
                    $check->update([
                        'status' => 'completed',
                        'similarity_score' => $score,
                        'provider' => 'ithenticate',
                        'detailed_report' => $data,
                        'completed_at' => now(),
                    ]);

                    // Generate certificate
                    (new CertificateGenerator($check))->generate();

                    $this->info("  ✓ Completed! Score: {$score}%");
                    Log::info("Plagiarism check #{$check->id} completed via poll. Score: {$score}%");
                } else {
                    $this->info("  Still processing...");
                }
            } catch (\Exception $e) {
                $this->error("  Error: " . $e->getMessage());
                Log::warning("Poll error for check #{$check->id}: " . $e->getMessage());
            }
        }

        return 0;
    }
}
