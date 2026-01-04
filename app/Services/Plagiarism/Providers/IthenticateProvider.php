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
    protected string $apiSecret;

    public function __construct()
    {
        $this->baseUrl = rtrim(Setting::get('ithenticate_base_url', 'https://unidagontor.turnitin.com'), '/');
        $this->integrationName = Setting::get('ithenticate_integration_name', 'Library-Portal-API');
        $this->apiSecret = Setting::get('ithenticate_api_secret', '');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiSecret);
    }

    protected function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiSecret,
            'X-Turnitin-Integration-Name' => $this->integrationName,
            'X-Turnitin-Integration-Version' => '1.0.0',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Submit and process plagiarism check
     */
    public function submit(PlagiarismCheck $check): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('iThenticate API belum dikonfigurasi');
        }

        $userId = $check->member->email ?? $check->member->member_id . '@student.unida.gontor.ac.id';

        // Step 1: Accept EULA
        $this->acceptEula($userId);

        // Step 2: Create submission
        $submissionId = $this->createSubmission($check);
        Log::info("iThenticate: Submission created: {$submissionId}");

        // Step 3: Upload file
        $this->uploadFile($check, $submissionId);
        Log::info("iThenticate: File uploaded");

        // Step 4: Wait for processing then request report
        sleep(3);
        $this->requestReport($submissionId);
        Log::info("iThenticate: Report requested");

        // Step 5: Poll for results (max 3 minutes)
        $result = $this->poll($submissionId, 12, 15);

        return $result;
    }

    protected function acceptEula(string $userId): void
    {
        try {
            $eula = Http::withHeaders($this->headers())->timeout(30)
                ->get("{$this->baseUrl}/api/v1/eula/latest");

            if ($eula->successful()) {
                Http::withHeaders($this->headers())->timeout(30)
                    ->post("{$this->baseUrl}/api/v1/eula/{$eula->json('version')}/accept", [
                        'user_id' => $userId,
                        'accepted_timestamp' => now()->toIso8601String(),
                        'language' => 'en-US',
                    ]);
            }
        } catch (\Exception $e) {
            // EULA errors are non-fatal
        }
    }

    protected function createSubmission(PlagiarismCheck $check): string
    {
        $member = $check->member;

        $response = Http::withHeaders($this->headers())->timeout(60)
            ->post("{$this->baseUrl}/api/v1/submissions", [
                'owner' => (string) ($member->member_id ?? $member->id),
                'title' => $check->document_title ?? $check->original_filename,
                'submitter' => $member->email ?? $member->member_id . '@student.unida.gontor.ac.id',
                'owner_default_permission_set' => 'LEARNER',
                'submitter_default_permission_set' => 'INSTRUCTOR',
            ]);

        if (!$response->successful()) {
            throw new \Exception('Gagal membuat submission: ' . $response->body());
        }

        $submissionId = $response->json('id');
        $check->update(['external_id' => $submissionId]);

        return $submissionId;
    }

    protected function uploadFile(PlagiarismCheck $check, string $submissionId): void
    {
        $filePath = Storage::disk('local')->path($check->file_path);

        if (!file_exists($filePath)) {
            throw new \Exception("File tidak ditemukan");
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiSecret,
            'X-Turnitin-Integration-Name' => $this->integrationName,
            'X-Turnitin-Integration-Version' => '1.0.0',
            'Content-Type' => 'binary/octet-stream',
            'Content-Disposition' => 'inline; filename="' . $check->original_filename . '"',
        ])
        ->timeout(180)
        ->withBody(file_get_contents($filePath), 'binary/octet-stream')
        ->put("{$this->baseUrl}/api/v1/submissions/{$submissionId}/original");

        if (!$response->successful()) {
            throw new \Exception('Gagal upload file: ' . $response->body());
        }
    }

    protected function requestReport(string $submissionId): void
    {
        for ($i = 0; $i < 3; $i++) {
            $response = Http::withHeaders($this->headers())->timeout(60)
                ->put("{$this->baseUrl}/api/v1/submissions/{$submissionId}/similarity", [
                    'generation_settings' => [
                        'search_repositories' => ['INTERNET', 'SUBMITTED_WORK', 'PUBLICATION', 'CROSSREF', 'CROSSREF_POSTED_CONTENT'],
                        'auto_exclude_self_matching_scope' => 'ALL',
                    ],
                    'view_settings' => [
                        'exclude_quotes' => true,
                        'exclude_bibliography' => true,
                        'exclude_citations' => true,
                        'exclude_small_matches' => 8,
                    ],
                    'indexing_settings' => ['add_to_index' => true],
                ]);

            if (in_array($response->status(), [202, 409])) {
                return; // Success or already requested
            }

            if ($response->status() === 400 && str_contains($response->body(), 'PROCESSING')) {
                sleep(5);
                continue;
            }

            Log::warning("iThenticate: Request report attempt " . ($i + 1) . " failed: " . $response->body());
            sleep(3);
        }
    }

    protected function poll(string $submissionId, int $maxAttempts = 12, int $delay = 15): array
    {
        for ($i = 1; $i <= $maxAttempts; $i++) {
            $response = Http::withHeaders($this->headers())->timeout(30)
                ->get("{$this->baseUrl}/api/v1/submissions/{$submissionId}/similarity");

            if ($response->status() === 404) {
                Log::info("iThenticate: Poll {$i}/{$maxAttempts} - report not ready");
                if ($i === 1) $this->requestReport($submissionId);
                sleep($delay);
                continue;
            }

            if ($response->successful()) {
                $data = $response->json();
                $status = $data['status'] ?? '';

                Log::info("iThenticate: Poll {$i}/{$maxAttempts} - {$status}");

                if ($status === 'COMPLETE') {
                    return [
                        'score' => $data['overall_match_percentage'] ?? 0,
                        'sources' => $this->formatSources($data),
                        'report' => [
                            'submission_id' => $submissionId,
                            'overall_match_percentage' => $data['overall_match_percentage'] ?? 0,
                            'internet_match_percentage' => $data['internet_match_percentage'] ?? 0,
                            'publication_match_percentage' => $data['publication_match_percentage'] ?? 0,
                            'submitted_works_match_percentage' => $data['submitted_works_match_percentage'] ?? 0,
                            'status' => 'COMPLETE',
                        ],
                    ];
                }

                if (in_array($status, ['FAILED', 'ERROR'])) {
                    throw new \Exception('Report gagal: ' . ($data['error_message'] ?? 'Unknown'));
                }
            }

            sleep($delay);
        }

        // Timeout - mark for async polling
        throw new \Exception('ASYNC_POLL:' . $submissionId);
    }

    protected function formatSources(array $data): array
    {
        $sources = $data['top_sources'] ?? [];
        return array_map(fn($s) => [
            'source_type' => $s['source_type'] ?? 'INTERNET',
            'title' => $s['name'] ?? 'Unknown',
            'similarity' => $s['percent_match'] ?? 0,
        ], array_slice($sources, 0, 10));
    }

    /**
     * Check status for async polling
     */
    public function checkStatus(string $submissionId): ?array
    {
        $response = Http::withHeaders($this->headers())->timeout(30)
            ->get("{$this->baseUrl}/api/v1/submissions/{$submissionId}/similarity");

        if ($response->status() === 404) {
            $this->requestReport($submissionId);
            return null;
        }

        if ($response->successful() && ($response->json('status') ?? '') === 'COMPLETE') {
            $data = $response->json();
            return [
                'score' => $data['overall_match_percentage'] ?? 0,
                'sources' => $this->formatSources($data),
                'report' => $data,
            ];
        }

        return null;
    }

    public function getReportUrl(string $submissionId): ?string
    {
        try {
            $response = Http::withHeaders($this->headers())->timeout(30)
                ->post("{$this->baseUrl}/api/v1/submissions/{$submissionId}/viewer-url", [
                    'viewer_user_id' => 'admin',
                    'locale' => 'en-US',
                    'viewer_default_permission_set' => 'ADMINISTRATOR',
                ]);

            return $response->successful() ? $response->json('viewer_url') : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
