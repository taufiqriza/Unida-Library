<?php

namespace App\Console\Commands;

use App\Models\JournalSource;
use App\Services\OjsScraperService;
use Illuminate\Console\Command;

class ScrapeJournals extends Command
{
    protected $signature = 'journals:scrape {--journal= : Specific journal code to scrape}';
    protected $description = 'Scrape all articles from OJS journal archives';

    public function handle(OjsScraperService $scraper): int
    {
        $journalCode = $this->option('journal');
        
        if ($journalCode) {
            $source = JournalSource::where('code', $journalCode)->first();
            if (!$source) {
                $this->error("Journal '{$journalCode}' not found");
                return 1;
            }
            
            $this->info("Scraping {$source->name}...");
            $result = $scraper->scrapeJournal($source);
            $this->info("Done! Issues: {$result['issues']}, Articles: {$result['articles']}");
        } else {
            $this->info('Scraping all journals...');
            $this->newLine();
            
            $result = $scraper->scrapeAllJournals();
            
            $this->info("Completed!");
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Journals', $result['journals']],
                    ['Issues', $result['issues']],
                    ['Articles', $result['articles']],
                    ['Errors', count($result['errors'])],
                ]
            );
            
            if (!empty($result['errors'])) {
                $this->newLine();
                $this->warn('Errors:');
                foreach ($result['errors'] as $error) {
                    $this->line("  - {$error}");
                }
            }
        }
        
        return 0;
    }
}
