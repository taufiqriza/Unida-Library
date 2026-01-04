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

    public int $tries = 2;
    public int $timeout = 300; // 5 minutes
    public int $backoff = 30;

    public function __construct(public PlagiarismCheck $check) {}

    public function handle(PlagiarismService $service): void
    {
        Log::info("Processing plagiarism #{$this->check->id}");

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

            Log::info("Plagiarism #{$this->check->id} completed. Score: {$result['score']}%");

            (new CertificateGenerator($this->check))->generate();
            $this->notify();

        } catch (\Exception $e) {
            $message = $e->getMessage();
            Log::error("Plagiarism #{$this->check->id} error: {$message}");

            // Async poll marker - submission exists, just waiting for report
            if (str_starts_with($message, 'ASYNC_POLL:')) {
                $this->check->update([
                    'status' => 'submitted',
                    'error_message' => 'Menunggu hasil dari Turnitin...',
                ]);
                return;
            }

            $this->check->update([
                'status' => PlagiarismCheck::STATUS_FAILED,
                'error_message' => $message,
                'completed_at' => now(),
            ]);

            $this->notify();
        }
    }

    public function failed(\Throwable $e): void
    {
        $this->check->update([
            'status' => PlagiarismCheck::STATUS_FAILED,
            'error_message' => $e->getMessage(),
            'completed_at' => now(),
        ]);
        $this->notify();
    }

    protected function notify(): void
    {
        try {
            if ($this->check->member?->email) {
                $this->check->member->notify(new PlagiarismCheckCompleted($this->check->fresh()));
            }
        } catch (\Exception $e) {
            Log::warning("Notification failed: " . $e->getMessage());
        }
    }
}
