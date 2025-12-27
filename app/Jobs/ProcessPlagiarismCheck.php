<?php

namespace App\Jobs;

use App\Models\PlagiarismCheck;
use App\Notifications\PlagiarismCheckCompleted;
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

    public int $tries = 5; // Allow retries for transient errors
    public int $timeout = 2100; // 35 minutes for Turnitin processing (30 min polling + buffer)
    public int $backoff = 120; // Retry after 2 minutes if needed

    public function __construct(
        public PlagiarismCheck $check
    ) {}

    public function handle(PlagiarismService $service): void
    {
        Log::info("Processing plagiarism check #{$this->check->id}");

        $this->check->update([
            'status' => PlagiarismCheck::STATUS_PROCESSING,
            'started_at' => now(),
            'error_message' => null,
        ]);

        try {
            $result = $service->check($this->check);

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

            // Send email notification
            $this->sendNotification();

        } catch (\Exception $e) {
            Log::error("Plagiarism check #{$this->check->id} failed: " . $e->getMessage());

            $this->check->update([
                'status' => PlagiarismCheck::STATUS_FAILED,
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            if ($this->attempts() < $this->tries) {
                throw $e;
            }
            
            // Send failure notification on last attempt
            $this->sendNotification();
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

        $this->sendNotification();
    }

    protected function sendNotification(): void
    {
        try {
            $member = $this->check->member;
            if ($member && $member->email) {
                $member->notify(new PlagiarismCheckCompleted($this->check->fresh()));
                Log::info("Email notification sent for check #{$this->check->id}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send notification for check #{$this->check->id}: " . $e->getMessage());
        }
    }
}
