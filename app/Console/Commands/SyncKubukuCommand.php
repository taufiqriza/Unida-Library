<?php

namespace App\Console\Commands;

use App\Models\Ebook;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncKubukuCommand extends Command
{
    protected $signature = 'kubuku:sync {--force : Force sync even if disabled}';
    protected $description = 'Sync e-book catalog from Kubuku API';

    public function handle(): int
    {
        // Check if enabled
        if (!Setting::get('kubuku_enabled', false) && !$this->option('force')) {
            $this->warn('Kubuku integration is disabled. Use --force to sync anyway.');
            return Command::SUCCESS;
        }

        $apiUrl = Setting::get('kubuku_api_url');
        $apiKey = Setting::get('kubuku_api_key');
        $libraryId = Setting::get('kubuku_library_id');

        if (empty($apiUrl) || empty($apiKey)) {
            $this->error('Kubuku API URL and API Key must be configured in App Settings.');
            return Command::FAILURE;
        }

        $this->info('Starting Kubuku catalog sync...');
        $this->info("API URL: {$apiUrl}");
        $this->info("Library ID: {$libraryId}");

        try {
            // Fetch catalog from Kubuku API
            // Note: Actual endpoint will be confirmed from Kubuku documentation
            $endpoint = rtrim($apiUrl, '/') . '/catalog';
            
            if ($libraryId) {
                $endpoint .= '?library_id=' . $libraryId;
            }

            $this->line("Fetching from: {$endpoint}");

            $response = Http::timeout(60)->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept' => 'application/json',
            ])->get($endpoint);

            if (!$response->successful()) {
                $this->error("API request failed with status: " . $response->status());
                Log::error('Kubuku sync failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return Command::FAILURE;
            }

            $data = $response->json();
            
            // Expected structure based on KUBUKU_API_REQUIREMENTS.md:
            // { "status": "success", "data": [...] }
            $books = $data['data'] ?? $data;

            if (!is_array($books)) {
                $this->error('Invalid response format from Kubuku API');
                return Command::FAILURE;
            }

            $this->info("Found " . count($books) . " e-books from Kubuku");

            $created = 0;
            $updated = 0;

            $bar = $this->output->createProgressBar(count($books));
            $bar->start();

            foreach ($books as $book) {
                // Map Kubuku fields to our Ebook model
                // Field mapping based on KUBUKU_API_REQUIREMENTS.md
                $externalId = $book['id'] ?? $book['external_id'] ?? null;
                
                if (!$externalId) {
                    $bar->advance();
                    continue;
                }

                $ebookData = [
                    'title' => $book['title'] ?? 'Untitled',
                    'sor' => $book['authors'] ?? '',
                    'publisher' => $book['publisher'] ?? '',
                    'publish_year' => $book['publish_year'] ?? null,
                    'isbn' => $book['isbn'] ?? null,
                    'abstract' => $book['description'] ?? null,
                    'cover_url' => $book['cover_url'] ?? null,
                    'read_url' => $book['read_url'] ?? null,
                    'source' => 'kubuku',
                    'source_id' => $externalId,
                    'is_active' => true,
                ];

                // Check if exists
                $ebook = Ebook::where('source', 'kubuku')
                    ->where('source_id', $externalId)
                    ->first();

                if ($ebook) {
                    $ebook->update($ebookData);
                    $updated++;
                } else {
                    Ebook::create($ebookData);
                    $created++;
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            $this->info("Sync completed: {$created} created, {$updated} updated");

            Log::info('Kubuku sync completed', [
                'total' => count($books),
                'created' => $created,
                'updated' => $updated,
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Sync failed: " . $e->getMessage());
            Log::error('Kubuku sync error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }
    }
}
