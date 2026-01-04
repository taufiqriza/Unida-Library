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

    public int $tries = 3;
    public int $timeout = 600; // 10 minutes max
    public int $backoff = 60;

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

            Log::info("Plagiarism #{$this->check->id} completed. Score: {$result['score']}%");

            // Generate certificate
            (new CertificateGenerator($this->check))->generate();

            // Send notification
            $this->sendNotification();

        } catch (\Exception $e) {
            $message = $e->getMessage();
            Log::error("Plagiarism #{$this->check->id} failed: {$message}");

            // If it's a timeout but submission exists, mark as submitted for async polling
            if (str_contains($message, 'masih diproses') || str_contains($message, 'timeout')) {
                $this->check->update([
                    'status' => 'submitted',
                    'error_message' => 'Menunggu hasil dari Turnitin. Akan dikirim via email.',
                ]);
                Log::info("Plagiarism #{$this->check->id} marked for async polling");
                return;
            }

            $this->check->update([
                'status' => PlagiarismCheck::STATUS_FAILED,
                'error_message' => $message,
                'completed_at' => now(),
            ]);

            // Only retry on transient errors
            if ($this->attempts() < $this->tries && $this->isRetryableError($message)) {
                throw $e;
            }

            $this->sendNotification();
        }
    }

    protected function isRetryableError(string $message): bool
    {
        $retryable = ['connection', 'timeout', 'temporarily', '503', '502', '504'];
        foreach ($retryable as $keyword) {
            if (stripos($message, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Plagiarism job failed for #{$this->check->id}: " . $exception->getMessage());

        $this->check->update([
            'status' => PlagiarismCheck::STATUS_FAILED,
            'error_message' => 'Proses gagal: ' . $exception->getMessage(),
            'completed_at' => now(),
        ]);

        $this->sendNotification();
    }

    protected function sendNotification(): void
    {
        try {
            $member = $this->check->member;
            if ($member?->email) {
                $member->notify(new PlagiarismCheckCompleted($this->check->fresh()));
            }

            if ($member && $this->check->status === PlagiarismCheck::STATUS_COMPLETED) {
                app(\App\Services\MemberNotificationService::class)->sendPlagiarismComplete($this->check);
            }
        } catch (\Exception $e) {
            Log::error("Notification failed for #{$this->check->id}: " . $e->getMessage());
        }
    }
}
