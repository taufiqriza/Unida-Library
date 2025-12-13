<?php

namespace App\Services\Plagiarism\Providers;

use App\Models\PlagiarismCheck;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IthenticateProvider
{
    protected string $baseUrl;
    protected string $integrationName;
    protected string $apiKey;
    protected string $apiSecret;

    public function __construct()
    {
        $this->baseUrl = rtrim(Setting::get('ithenticate_base_url', 'https://unidagontor.turnitin.com'), '/');
        $this->integrationName = Setting::get('ithenticate_integration_name', '');
        $this->apiKey = Setting::get('ithenticate_api_key', '');
        $this->apiSecret = Setting::get('ithenticate_api_secret', '');
    }

    /**
     * Check if provider is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->apiSecret);
    }

    /**
     * Get authorization headers
     * TCA uses the signing secret directly as the API key
     */
    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiSecret,
            'X-Turnitin-Integration-Name' => $this->integrationName ?: 'Library-Portal-API',
            'X-Turnitin-Integration-Version' => '1.0.0',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Submit document for plagiarism check
     */
    public function submit(PlagiarismCheck $check): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('iThenticate API belum dikonfigurasi. Silakan isi credentials di App Settings.');
        }

        Log::info("iThenticate: Starting submission for check #{$check->id}");

        try {
            $member = $check->member;
            $userId = $member->email ?? $member->member_id . '@student.unida.gontor.ac.id';
            
            // Step 0: Accept EULA for user (if not already accepted)
            $this->acceptEula($userId);
            
            // Step 1: Create submission
            $submission = $this->createSubmission($check);
            
            // Step 2: Upload file
            $this->uploadFile($check, $submission['id']);
            
            // Step 3: Request similarity report
            $this->requestSimilarityReport($submission['id']);
            
            // Step 4: Wait a bit before polling (Turnitin needs time to process)
            Log::info("iThenticate: Waiting 30 seconds before polling for results...");
            sleep(30);
            
            // Step 5: Poll for results
            $result = $this->pollForResults($submission['id']);
            
            return $result;

        } catch (\Exception $e) {
            Log::error("iThenticate error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Accept EULA for a user
     */
    protected function acceptEula(string $userId): void
    {
        // First get the latest EULA version
        $eulaResponse = Http::withHeaders($this->getHeaders())
            ->get("{$this->baseUrl}/api/v1/eula/latest");
        
        if (!$eulaResponse->successful()) {
            Log::warning("Could not get EULA version: " . $eulaResponse->body());
            return;
        }
        
        $eulaVersion = $eulaResponse->json('version', 'v1beta');
        
        // Accept EULA for the user
        $response = Http::withHeaders($this->getHeaders())
            ->post("{$this->baseUrl}/api/v1/eula/{$eulaVersion}/accept", [
                'user_id' => $userId,
                'accepted_timestamp' => now()->toIso8601String(),
                'language' => 'en-US',
            ]);
        
        if ($response->successful()) {
            Log::info("iThenticate: EULA accepted for user: {$userId}");
        } elseif ($response->status() === 409) {
            // Already accepted - this is fine
            Log::info("iThenticate: EULA already accepted for user: {$userId}");
        } else {
            Log::warning("iThenticate: Could not accept EULA for user {$userId}: " . $response->body());
        }
    }

    /**
     * Create a new submission
     */
    protected function createSubmission(PlagiarismCheck $check): array
    {
        $member = $check->member;
        
        $response = Http::withHeaders($this->getHeaders())
            ->post("{$this->baseUrl}/api/v1/submissions", [
                'owner' => $member->member_id,
                'title' => $check->document_title ?? $check->original_filename,
                'submitter' => $member->email ?? $member->member_id . '@student.unida.gontor.ac.id',
                'owner_default_permission_set' => 'LEARNER',
                'submitter_default_permission_set' => 'INSTRUCTOR',
                'extract_text_only' => false,
                'metadata' => [
                    'group' => [
                        'id' => 'library-portal',
                        'name' => 'Library Portal Submission',
                    ],
                ],
            ]);

        if (!$response->successful()) {
            Log::error("iThenticate createSubmission failed: " . $response->body());
            throw new \Exception('Gagal membuat submission: ' . $response->json('message', 'Unknown error'));
        }

        $data = $response->json();
        
        // Store external ID
        $check->update([
            'external_id' => $data['id'],
        ]);

        Log::info("iThenticate: Submission created with ID: " . $data['id']);

        return $data;
    }

    /**
     * Upload file to submission
     */
    protected function uploadFile(PlagiarismCheck $check, string $submissionId): void
    {
        $filePath = Storage::disk('local')->path($check->file_path);
        $fileContent = file_get_contents($filePath);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiSecret,
            'X-Turnitin-Integration-Name' => $this->integrationName ?: 'Library-Portal-API',
            'X-Turnitin-Integration-Version' => '1.0.0',
            'Content-Type' => 'binary/octet-stream',
            'Content-Disposition' => 'inline; filename="' . $check->original_filename . '"',
        ])
        ->withBody($fileContent, 'binary/octet-stream')
        ->put("{$this->baseUrl}/api/v1/submissions/{$submissionId}/original");

        if (!$response->successful()) {
            Log::error("iThenticate uploadFile failed: " . $response->body());
            throw new \Exception('Gagal mengupload file: ' . $response->json('message', 'Unknown error'));
        }

        Log::info("iThenticate: File uploaded for submission: {$submissionId}");
    }

    /**
     * Request similarity report generation
     */
    protected function requestSimilarityReport(string $submissionId): void
    {
        $response = Http::withHeaders($this->getHeaders())
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
                    'exclude_abstract' => false,
                    'exclude_methods' => false,
                    'exclude_small_matches' => 8,
                    'exclude_internet' => false,
                    'exclude_publications' => false,
                    'exclude_submitted_works' => false,
                ],
                'indexing_settings' => [
                    'add_to_index' => true,
                ],
            ]);

        if (!$response->successful()) {
            // Check if report already requested
            if ($response->status() !== 409) {
                Log::error("iThenticate requestSimilarityReport failed: " . $response->body());
                throw new \Exception('Gagal meminta similarity report: ' . $response->json('message', 'Unknown error'));
            }
        }

        Log::info("iThenticate: Similarity report requested for: {$submissionId}");
    }

    /**
     * Poll for similarity results
     * maxAttempts: 60, delaySeconds: 15 = max 15 minutes
     */
    protected function pollForResults(string $submissionId, int $maxAttempts = 60, int $delaySeconds = 15): array
    {
        Log::info("iThenticate: Polling for results on submission: {$submissionId}");

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $response = Http::withHeaders($this->getHeaders())
                    ->timeout(60)
                    ->connectTimeout(30)
                    ->get($this->baseUrl . "/api/v1/submissions/{$submissionId}/similarity");

                // 404 means report not ready yet - continue polling
                if ($response->status() === 404) {
                    Log::info("iThenticate: Poll attempt {$attempt}, report not ready yet (404)");
                    if ($attempt < $maxAttempts) {
                        sleep($delaySeconds);
                    }
                    continue;
                }

                if ($response->successful()) {
                    $data = $response->json();
                    $status = $data['status'] ?? 'PENDING';

                    Log::info("iThenticate: Poll attempt {$attempt}, status: {$status}");

                    if ($status === 'COMPLETE') {
                        return [
                            'score' => $data['overall_match_percentage'] ?? 0,
                            'sources' => $this->formatSources($data['top_sources'] ?? []),
                            'report' => [
                                'submission_id' => $submissionId,
                                'overall_match_percentage' => $data['overall_match_percentage'] ?? 0,
                                'internet_match_percentage' => $data['internet_match_percentage'] ?? 0,
                                'publication_match_percentage' => $data['publication_match_percentage'] ?? 0,
                                'submitted_works_match_percentage' => $data['submitted_works_match_percentage'] ?? 0,
                                'status' => 'COMPLETE',
                                'provider' => 'ithenticate',
                            ],
                        ];
                    }

                    if (in_array($status, ['FAILED', 'ERROR'])) {
                        throw new \Exception('Similarity check failed: ' . ($data['error_message'] ?? 'Unknown error'));
                    }
                } else {
                    Log::warning("iThenticate: Poll attempt {$attempt} failed with status: " . $response->status());
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::warning("iThenticate: Poll attempt {$attempt} connection error: " . $e->getMessage());
                // Continue polling on connection errors
            }

            // Wait before next attempt
            if ($attempt < $maxAttempts) {
                sleep($delaySeconds);
            }
        }

        throw new \Exception("Timeout: Similarity report tidak selesai dalam waktu yang ditentukan.");
    }

    /**
     * Format sources for storage
     */
    protected function formatSources(array $sources): array
    {
        return array_map(function ($source) {
            return [
                'source_type' => $source['source_type'] ?? 'INTERNET',
                'title' => $source['name'] ?? 'Unknown Source',
                'author' => $source['author'] ?? null,
                'year' => $source['publication_year'] ?? null,
                'url' => $source['url'] ?? null,
                'similarity' => $source['percent_match'] ?? 0,
            ];
        }, array_slice($sources, 0, 10)); // Top 10 sources
    }

    /**
     * Get similarity report URL for viewing in browser
     */
    public function getReportUrl(string $submissionId): ?string
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/api/v1/submissions/{$submissionId}/viewer-url", [
                    'viewer_user_id' => 'admin',
                    'locale' => 'id',
                    'viewer_default_permission_set' => 'INSTRUCTOR',
                ]);

            if ($response->successful()) {
                return $response->json('viewer_url');
            }
        } catch (\Exception $e) {
            Log::error("Failed to get report URL: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Download PDF report from iThenticate
     */
    public function downloadPdfReport(string $submissionId): ?string
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/api/v1/submissions/{$submissionId}/similarity/pdf", [
                    'locale' => 'id',
                ]);

            if ($response->successful()) {
                return $response->json('download_url');
            }
            
            Log::error("Failed to get PDF download URL: " . $response->body());
        } catch (\Exception $e) {
            Log::error("Failed to get PDF report: " . $e->getMessage());
        }

        return null;
    }
}
