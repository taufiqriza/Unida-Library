<?php

namespace App\Console\Commands;

use App\Models\News;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadNewsImages extends Command
{
    protected $signature = 'news:download-images 
                            {--dry-run : Preview without downloading}
                            {--force : Re-download even if local image exists}';

    protected $description = 'Download external news images to local storage';

    public function handle(): int
    {
        $this->info('🖼️  Starting News Image Download...');
        $this->newLine();

        // Get news with external_image but no local featured_image
        $query = News::whereNotNull('external_image')
            ->where('external_image', '!=', '');

        if (!$this->option('force')) {
            $query->where(function ($q) {
                $q->whereNull('featured_image')
                    ->orWhere('featured_image', '');
            });
        }

        $newsItems = $query->get();

        if ($newsItems->isEmpty()) {
            $this->info('✅ No external images to download.');
            return Command::SUCCESS;
        }

        $this->info("📥 Found {$newsItems->count()} news items with external images");
        $this->newLine();

        // Create directory if not exists
        if (!Storage::disk('public')->exists('news')) {
            Storage::disk('public')->makeDirectory('news');
        }

        $downloaded = 0;
        $failed = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($newsItems->count());
        $bar->start();

        foreach ($newsItems as $news) {
            try {
                $result = $this->downloadImage($news);

                if ($result === 'downloaded') {
                    $downloaded++;
                } elseif ($result === 'skipped') {
                    $skipped++;
                } else {
                    $failed++;
                }
            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("  Error [{$news->id}]: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('✅ Download completed!');
        $this->table(
            ['Status', 'Count'],
            [
                ['Downloaded', $downloaded],
                ['Skipped', $skipped],
                ['Failed', $failed],
                ['Total', $newsItems->count()],
            ]
        );

        return Command::SUCCESS;
    }

    protected function downloadImage(News $news): string
    {
        $externalUrl = $news->external_image;

        // Skip if already has local image (unless forcing)
        if ($news->featured_image && !$this->option('force')) {
            return 'skipped';
        }

        // Validate URL
        if (!filter_var($externalUrl, FILTER_VALIDATE_URL)) {
            $this->newLine();
            $this->warn("  Invalid URL [{$news->id}]: {$externalUrl}");
            return 'failed';
        }

        if ($this->option('dry-run')) {
            $this->newLine();
            $this->line("  [DRY-RUN] Would download: {$externalUrl}");
            return 'downloaded';
        }

        // Download the image
        $response = Http::timeout(30)
            ->withOptions(['verify' => false])
            ->get($externalUrl);

        if (!$response->successful()) {
            $this->newLine();
            $this->warn("  Failed to download [{$news->id}]: HTTP {$response->status()}");
            return 'failed';
        }

        // Get content type and validate it's an image
        $contentType = $response->header('Content-Type');
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!Str::contains($contentType, $allowedTypes)) {
            // Try to detect from content
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($response->body());
            
            if (!in_array($mimeType, $allowedTypes)) {
                $this->newLine();
                $this->warn("  Invalid image type [{$news->id}]: {$mimeType}");
                return 'failed';
            }
            $contentType = $mimeType;
        }

        // Determine file extension
        $extension = match (true) {
            Str::contains($contentType, 'png') => 'png',
            Str::contains($contentType, 'gif') => 'gif',
            Str::contains($contentType, 'webp') => 'webp',
            default => 'jpg',
        };

        // Generate filename
        $filename = 'news/' . $news->slug . '-' . Str::random(8) . '.' . $extension;

        // Save to storage
        Storage::disk('public')->put($filename, $response->body());

        // Update the news record
        $news->update([
            'featured_image' => $filename,
        ]);

        return 'downloaded';
    }
}
