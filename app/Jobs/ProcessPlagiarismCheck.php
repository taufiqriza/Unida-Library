<?php

namespace App\Jobs;

use App\Models\PlagiarismCheck;
use App\Services\Plagiarism\CertificateGenerator;
use App\Services\Plagiarism\PlagiarismService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPlagiarismCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 600; // 10 minutes
    public int $backoff = 60; // Retry after 60 seconds

    public function __construct(
        public PlagiarismCheck $check
    ) {}

    public function handle(PlagiarismService $service): void
    {
        Log::info("Processing plagiarism check #{$this->check->id}");

        // Update status to processing
        $this->check->update([
            'status' => PlagiarismCheck::STATUS_PROCESSING,
            'started_at' => now(),
            'error_message' => null,
        ]);

        try {
            // Perform the check
            $result = $service->check($this->check);

            // Update with results
            $this->check->update([
                'status' => PlagiarismCheck::STATUS_COMPLETED,
                'similarity_score' => $result['score'],
                'similarity_sources' => $result['sources'],
                'detailed_report' => $result['report'],
                'provider' => $service->getProvider(),
                'completed_at' => now(),
            ]);

            Log::info("Plagiarism check #{$this->check->id} completed. Score: {$result['score']}%");

            // Generate certificate
            $generator = new CertificateGenerator($this->check);
            $generator->generate();

            Log::info("Certificate generated for check #{$this->check->id}");

        } catch (\Exception $e) {
            Log::error("Plagiarism check #{$this->check->id} failed: " . $e->getMessage());

            $this->check->update([
                'status' => PlagiarismCheck::STATUS_FAILED,
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            // Re-throw to trigger retry if applicable
            if ($this->attempts() < $this->tries) {
                throw $e;
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Plagiarism check job failed permanently for #{$this->check->id}: " . $exception->getMessage());

        $this->check->update([
            'status' => PlagiarismCheck::STATUS_FAILED,
            'error_message' => 'Proses gagal setelah beberapa percobaan: ' . $exception->getMessage(),
            'completed_at' => now(),
        ]);
    }
}
