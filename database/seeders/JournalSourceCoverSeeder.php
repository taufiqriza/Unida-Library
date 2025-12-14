<?php

namespace Database\Seeders;

use App\Models\JournalSource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class JournalSourceCoverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all journal sources
        $sources = JournalSource::all();
        
        $this->command->info("Found {$sources->count()} journal sources");
        
        foreach ($sources as $source) {
            // Skip if already has cover_url
            if ($source->getRawOriginal('cover_url')) {
                $this->command->info("Skipped {$source->name} (already has cover)");
                continue;
            }
            
            // Try to fetch cover from OJS
            $coverUrl = $this->fetchCoverFromOjs($source);
            
            if ($coverUrl) {
                $source->update(['cover_url' => $coverUrl]);
                $this->command->info("✓ Updated cover for: {$source->name}");
            } else {
                $this->command->warn("✗ No cover found for: {$source->name}");
            }
        }

        $this->command->info('Journal covers update completed!');
    }

    /**
     * Try to fetch cover URL from OJS journal page
     */
    protected function fetchCoverFromOjs(JournalSource $source): ?string
    {
        if (!$source->base_url) {
            return null;
        }

        $baseUrl = rtrim($source->base_url, '/');
        
        // Common OJS cover URL patterns to try
        $patterns = [
            '/public/journals/journalThumbnail.png',
            '/public/journals/journalThumbnail.jpg', 
            '/public/site/images/homepageImage_en_US.jpg',
            '/public/site/images/homepageImage_id_ID.jpg',
        ];
        
        // Try to extract journal ID from URL and construct cover path
        // URL pattern: https://ejournal.unida.gontor.ac.id/index.php/al-tijarah
        if (preg_match('/index\.php\/([^\/]+)/', $source->base_url, $matches)) {
            $journalPath = $matches[1];
            
            // Try specific journal thumbnail patterns
            $specificPatterns = [
                "/public/journals/{$journalPath}/journalThumbnail_id_ID.png",
                "/public/journals/{$journalPath}/journalThumbnail_id_ID.jpg",
                "/public/journals/{$journalPath}/journalThumbnail_en_US.png",
                "/public/journals/{$journalPath}/journalThumbnail_en_US.jpg",
                "/public/journals/{$journalPath}/journalThumbnail.png",
                "/public/journals/{$journalPath}/journalThumbnail.jpg",
            ];
            
            $patterns = array_merge($specificPatterns, $patterns);
        }
        
        // Try fetching each pattern
        foreach ($patterns as $pattern) {
            $url = 'https://ejournal.unida.gontor.ac.id' . $pattern;
            
            try {
                $response = Http::timeout(5)->head($url);
                
                if ($response->successful()) {
                    $contentType = $response->header('Content-Type');
                    if (str_contains($contentType, 'image')) {
                        return $url;
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        return null;
    }
}
