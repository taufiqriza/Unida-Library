<?php

namespace App\Services\Plagiarism\Providers;

use App\Models\PlagiarismCheck;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class IthenticateProvider
{
    protected string $baseUrl;
    protected string $integrationName;
    protected string $apiKey;
    protected string $apiSecret;

    public function __construct()
    {
        $this->baseUrl = rtrim(Setting::get('ithenticate_base_url', 'https://unidagontor.turnitin.com'), '/');
        $this->integrationName = Setting::get('ithenticate_integration_name', 'Library-Portal-API');
        $this->apiKey = Setting::get('ithenticate_api_key', '');
        $this->apiSecret = Setting::get('ithenticate_api_secret', '');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiSecret);
    }

    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiSecret,
            'X-Turnitin-Integration-Name' => $this->integrationName,
            'X-Turnitin-Integration-Version' => '1.0.0',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Submit document - returns immediately after submission
     * Polling handled separately by scheduler
     */
    public function submit(PlagiarismCheck $check): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('iThenticate API belum dikonfigurasi');
        }

        Log::info("iThenticate: Starting submission #{$check->id}");

        // Step 1: Accept EULA
        $userId = $check->member->email ?? $check->member->member_id . '@student.unida.gontor.ac.id';
        $this->acceptEula($userId);

        // Step 2: Create submission
        $submissionId = $this->createSubmission($check);
        Log::info("iThenticate: Submission created: {$submissionId}");

        // Step 3: Upload file
        $this->uploadFile($check, $submissionId);
        Log::info("iThenticate: File uploaded for {$submissionId}");

        // Step 4: Wait for file processing (Turnitin needs time to process the upload)
        sleep(5);

        // Step 5: Request similarity report with retry
        $this->requestSimilarityReportWithRetry($submissionId);
        Log::info("iThenticate: Similarity report requested for {$submissionId}");

        // Step 6: Poll for results (max 5 minutes for quick results)
        $result = $this->pollForResults($submissionId, 20, 15); // 20 attempts x 15 sec = 5 min

        return $result;
    }

    protected function acceptEula(string $userId): void
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(30)
                ->get("{$this->baseUrl}/api/v1/eula/latest");

            if (!$response->successful()) {
                Log::warning("iThenticate: Could not get EULA: " . $response->body());
                return;
            }

            $eulaVersion = $response->json('version', 'v1beta');

            $acceptResponse = Http::withHeaders($this->getHeaders())
                ->timeout(30)
                ->post("{$this->baseUrl}/api/v1/eula/{$eulaVersion}/accept", [
                    'user_id' => $userId,
                    'accepted_timestamp' => now()->toIso8601String(),
                    'language' => 'en-US',
                ]);

            if ($acceptResponse->successful() || $acceptResponse->status() === 409) {
                Log::info("iThenticate: EULA accepted for {$userId}");
            }
        } catch (\Exception $e) {
            Log::warning("iThenticate: EULA error: " . $e->getMessage());
        }
    }

    protected function createSubmission(PlagiarismCheck $check): string
    {
        $member = $check->member;
        $ownerId = $member->member_id ?? $member->id;
        $submitterEmail = $member->email ?? $ownerId . '@student.unida.gontor.ac.id';

        $response = Http::withHeaders($this->getHeaders())
            ->timeout(60)
            ->post("{$this->baseUrl}/api/v1/submissions", [
                'owner' => (string) $ownerId,
                'title' => $check->document_title ?? $check->original_filename,
                'submitter' => $submitterEmail,
                'owner_default_permission_set' => 'LEARNER',
                'submitter_default_permission_set' => 'INSTRUCTOR',
                'extract_text_only' => false,
            ]);

        if (!$response->successful()) {
            Log::error("iThenticate: Create submission failed: " . $response->body());
            throw new \Exception('Gagal membuat submission: ' . ($response->json('message') ?? $response->body()));
        }

        $submissionId = $response->json('id');
        $check->update(['external_id' => $submissionId]);

        return $submissionId;
    }

    protected function uploadFile(PlagiarismCheck $check, string $submissionId): void
    {
        $filePath = Storage::disk('local')->path($check->file_path);
        
        if (!file_exists($filePath)) {
            throw new \Exception("File tidak ditemukan: {$check->file_path}");
        }

        $fileContent = file_get_contents($filePath);
        $filename = $check->original_filename ?? basename($check->file_path);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiSecret,
            'X-Turnitin-Integration-Name' => $this->integrationName,
            'X-Turnitin-Integration-Version' => '1.0.0',
            'Content-Type' => 'binary/octet-stream',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ])
        ->timeout(180)
        ->withBody($fileContent, 'binary/octet-stream')
        ->put("{$this->baseUrl}/api/v1/submissions/{$submissionId}/original");

        if (!$response->successful()) {
            Log::error("iThenticate: Upload failed: " . $response->body());
            throw new \Exception('Gagal upload file: ' . ($response->json('message') ?? $response->body()));
        }
    }

    /**
     * Request similarity report with retry mechanism
     */
    protected function requestSimilarityReportWithRetry(string $submissionId, int $maxRetries = 3): void
    {
        $lastError = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::withHeaders($this->getHeaders())
                    ->timeout(60)
                    ->put("{$this->baseUrl}/api/v1/submissions/{$submissionId}/similarity", [
                        'generation_settings' => [
                            'search_repositories' => [
                                'INTERNET',
                                'SUBMITTED_WORK',
                                'PUBLICATION',
                                'CROSSREF',
                                'CROSSREF_POSTED_CONTENT',
                            ],
                            'auto_exclude_self_matching_scope' => 'ALL',
                        ],
                        'view_settings' => [
                            'exclude_quotes' => true,
                            'exclude_bibliography' => true,
                            'exclude_citations' => true,
                            'exclude_small_matches' => 8,
                        ],
                        'indexing_settings' => [
                            'add_to_index' => true,
                        ],
                    ]);

                // 202 = Accepted, 409 = Already requested
                if ($response->status() === 202 || $response->status() === 409) {
                    Log::info("iThenticate: Similarity report request accepted (attempt {$attempt})");
                    return;
                }

                // 400 with "PROCESSING" means file still being processed
                if ($response->status() === 400) {
                    $error = $response->json('message', '');
                    if (str_contains(strtoupper($error), 'PROCESSING') || str_contains(strtoupper($error), 'NOT_READY')) {
                        Log::info("iThenticate: File still processing, waiting... (attempt {$attempt})");
                        sleep(10);
                        continue;
                    }
                }

                $lastError = "Status {$response->status()}: " . $response->body();
                Log::warning("iThenticate: Similarity request attempt {$attempt} failed: {$lastError}");

            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                Log::warning("iThenticate: Similarity request attempt {$attempt} error: {$lastError}");
            }

            if ($attempt < $maxRetries) {
                sleep(5);
            }
        }

        throw new \Exception("Gagal request similarity report setelah {$maxRetries} percobaan: {$lastError}");
    }

    /**
     * Poll for similarity results
     */
    protected function pollForResults(string $submissionId, int $maxAttempts = 20, int $delaySeconds = 15): array
    {
        Log::info("iThenticate: Polling for results: {$submissionId}");

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $response = Http::withHeaders($this->getHeaders())
                    ->timeout(30)
                    ->get("{$this->baseUrl}/api/v1/submissions/{$submissionId}/similarity");

                if ($response->status() === 404) {
                    // Report not ready yet - request it again
                    if ($attempt === 1 || $attempt === 5) {
                        Log::info("iThenticate: Report not found, re-requesting...");
                        try {
                            $this->requestSimilarityReportWithRetry($submissionId, 1);
                        } catch (\Exception $e) {
                            // Ignore, continue polling
                        }
                    }
                    Log::info("iThenticate: Poll {$attempt}/{$maxAttempts} - not ready (404)");
                    sleep($delaySeconds);
                    continue;
                }

                if ($response->status() === 403) {
                    Log::warning("iThenticate: Poll {$attempt} - 403 Forbidden. Check API permissions.");
                    sleep($delaySeconds);
                    continue;
                }

                if ($response->successful()) {
                    $data = $response->json();
                    $status = $data['status'] ?? 'PENDING';

                    Log::info("iThenticate: Poll {$attempt}/{$maxAttempts} - status: {$status}");

                    if ($status === 'COMPLETE') {
                        $score = $data['overall_match_percentage'] ?? 0;
                        Log::info("iThenticate: Report complete! Score: {$score}%");

                        return [
                            'score' => $score,
                            'sources' => $this->formatSources($data),
                            'report' => [
                                'submission_id' => $submissionId,
                                'overall_match_percentage' => $score,
                                'internet_match_percentage' => $data['internet_match_percentage'] ?? 0,
                                'publication_match_percentage' => $data['publication_match_percentage'] ?? 0,
                                'submitted_works_match_percentage' => $data['submitted_works_match_percentage'] ?? 0,
                                'status' => 'COMPLETE',
                                'provider' => 'ithenticate',
                            ],
                        ];
                    }

                    if (in_array($status, ['FAILED', 'ERROR'])) {
                        throw new \Exception('Turnitin report failed: ' . ($data['error_message'] ?? 'Unknown error'));
                    }
                }

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::warning("iThenticate: Poll {$attempt} connection error: " . $e->getMessage());
            }

            if ($attempt < $maxAttempts) {
                sleep($delaySeconds);
            }
        }

        // Timeout - but submission exists, mark for async polling
        Log::warning("iThenticate: Polling timeout for {$submissionId}, will continue via scheduler");
        
        throw new \Exception("Report masih diproses. Hasil akan dikirim via email setelah selesai.");
    }

    protected function formatSources(array $data): array
    {
        $sources = $data['top_sources'] ?? $data['top_source_largest_matched_word_count_list'] ?? [];
        
        return array_map(function ($source) {
            return [
                'source_type' => $source['source_type'] ?? 'INTERNET',
                'title' => $source['name'] ?? $source['title'] ?? 'Unknown',
                'similarity' => $source['percent_match'] ?? $source['match_percentage'] ?? 0,
                'url' => $source['url'] ?? null,
            ];
        }, array_slice($sources, 0, 10));
    }

    /**
     * Check submission status (for async polling)
     */
    public function checkStatus(string $submissionId): ?array
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(30)
                ->get("{$this->baseUrl}/api/v1/submissions/{$submissionId}/similarity");

            if ($response->status() === 404) {
                // Try to request report
                $this->requestSimilarityReportWithRetry($submissionId, 1);
                return null;
            }

            if ($response->successful()) {
                $data = $response->json();
                if (($data['status'] ?? '') === 'COMPLETE') {
                    return [
                        'score' => $data['overall_match_percentage'] ?? 0,
                        'sources' => $this->formatSources($data),
                        'report' => $data,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning("iThenticate checkStatus error: " . $e->getMessage());
        }

        return null;
    }

    public function getReportUrl(string $submissionId): ?string
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(30)
                ->post("{$this->baseUrl}/api/v1/submissions/{$submissionId}/viewer-url", [
                    'viewer_user_id' => 'admin',
                    'locale' => 'en-US',
                    'viewer_default_permission_set' => 'ADMINISTRATOR',
                ]);

            if ($response->successful()) {
                return $response->json('viewer_url');
            }
        } catch (\Exception $e) {
            Log::error("iThenticate getReportUrl error: " . $e->getMessage());
        }

        return null;
    }
}
