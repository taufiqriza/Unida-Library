<?php

namespace App\Services;

use App\Models\Ethesis;
use App\Models\JournalArticle;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RepoSyncService
{
    protected string $baseUrl = 'https://repo.unida.gontor.ac.id/cgi/oai2';
    protected int $timeout = 60;

    public function sync(): array
    {
        $stats = ['thesis' => 0, 'article' => 0, 'skipped' => 0, 'errors' => 0];
        $url = "{$this->baseUrl}?verb=ListRecords&metadataPrefix=oai_dc";

        while ($url) {
            try {
                $response = Http::retry(3, 2000)->timeout($this->timeout)->get($url);
                if (!$response->successful()) {
                    $stats['errors']++;
                    break;
                }

                $result = $this->processPage($response->body());
                $stats['thesis'] += $result['thesis'];
                $stats['article'] += $result['article'];
                $stats['skipped'] += $result['skipped'];

                // Get next page
                $url = $this->getNextPageUrl($response->body());
                
                if ($url) usleep(500000); // 500ms delay
            } catch (\Exception $e) {
                Log::error('Repo sync error', ['error' => $e->getMessage()]);
                $stats['errors']++;
                break;
            }
        }

        return $stats;
    }

    protected function processPage(string $xml): array
    {
        $stats = ['thesis' => 0, 'article' => 0, 'skipped' => 0];
        
        preg_match_all('/<record>(.*?)<\/record>/s', $xml, $matches);
        
        foreach ($matches[1] as $record) {
            $result = $this->processRecord($record);
            if ($result === 'thesis') $stats['thesis']++;
            elseif ($result === 'article') $stats['article']++;
            else $stats['skipped']++;
        }
        
        return $stats;
    }

    protected function processRecord(string $record): ?string
    {
        // Extract identifier
        preg_match('/<identifier>oai:repo\.unida\.gontor\.ac\.id:(\d+)<\/identifier>/', $record, $idMatch);
        $externalId = $idMatch[1] ?? null;
        if (!$externalId) return null;

        // Extract type
        $types = $this->extractAll($record, 'dc:type');
        $isThesis = in_array('Thesis', $types);
        $isArticle = in_array('Article', $types) || in_array('PeerReviewed', $types);

        if ($isThesis) {
            return $this->saveThesis($record, $externalId);
        } elseif ($isArticle) {
            return $this->saveArticle($record, $externalId);
        }

        return null;
    }

    protected function saveThesis(string $record, string $externalId): ?string
    {
        // Skip if exists
        if (Ethesis::where('source_type', 'repo')->where('external_id', $externalId)->exists()) {
            return null;
        }

        $title = $this->extract($record, 'dc:title');
        if (!$title) return null;

        $creators = $this->extractAll($record, 'dc:creator');
        $author = $creators[0] ?? 'Unknown';
        
        $abstract = $this->extract($record, 'dc:description');
        $date = $this->extract($record, 'dc:date');
        $year = $date ? (int) substr($date, 0, 4) : null;
        
        // Get URL
        $relations = $this->extractAll($record, 'dc:relation');
        $url = collect($relations)->first(fn($r) => str_contains($r, 'repo.unida.gontor.ac.id'));
        
        // Get PDF URL
        $identifiers = $this->extractAll($record, 'dc:identifier');
        $pdfUrl = collect($identifiers)->first(fn($i) => str_ends_with($i, '.pdf'));

        Ethesis::create([
            'source_type' => 'repo',
            'external_id' => $externalId,
            'external_url' => $url ?? "https://repo.unida.gontor.ac.id/{$externalId}/",
            'title' => $title,
            'author' => $author,
            'abstract' => $abstract,
            'year' => $year,
            'type' => 'skripsi', // Default, bisa di-refine
            'is_public' => true,
            'file_path' => $pdfUrl,
        ]);

        return 'thesis';
    }

    protected function saveArticle(string $record, string $externalId): ?string
    {
        // Skip if exists
        if (JournalArticle::where('source_type', 'repo')->where('external_id', $externalId)->exists()) {
            return null;
        }

        $title = $this->extract($record, 'dc:title');
        if (!$title) return null;

        $creators = $this->extractAll($record, 'dc:creator');
        $authors = array_map(fn($c) => ['name' => trim($c)], $creators);
        
        $abstract = $this->extract($record, 'dc:description');
        $date = $this->extract($record, 'dc:date');
        $year = $date ? (int) substr($date, 0, 4) : null;
        
        // Get URLs
        $relations = $this->extractAll($record, 'dc:relation');
        $repoUrl = collect($relations)->first(fn($r) => str_contains($r, 'repo.unida.gontor.ac.id'));
        $ojsUrl = collect($relations)->first(fn($r) => str_contains($r, 'ejournal.unida.gontor.ac.id'));
        
        // Extract journal info from identifier
        $identifiers = $this->extractAll($record, 'dc:identifier');
        $citation = collect($identifiers)->first(fn($i) => str_contains($i, 'ISSN') || str_contains($i, 'pp.'));
        
        $journalName = 'UNIDA Repository';
        $volume = null;
        $issue = null;
        
        if ($citation && preg_match('/([A-Za-z\s]+),\s*(\d+)\s*\((\d+)\)/', $citation, $m)) {
            $journalName = trim($m[1]);
            $volume = $m[2];
            $issue = $m[3];
        }

        // Get PDF URL
        $pdfUrl = collect($identifiers)->first(fn($i) => str_ends_with($i, '.pdf'));

        JournalArticle::create([
            'source_type' => 'repo',
            'external_id' => $externalId,
            'journal_code' => 'repo',
            'journal_name' => $journalName,
            'title' => $title,
            'abstract' => $abstract,
            'authors' => $authors,
            'volume' => $volume,
            'issue' => $issue,
            'publish_year' => $year,
            'published_at' => $date,
            'url' => $ojsUrl ?? $repoUrl ?? "https://repo.unida.gontor.ac.id/{$externalId}/",
            'pdf_url' => $pdfUrl,
            'synced_at' => now(),
        ]);

        return 'article';
    }

    protected function extract(string $xml, string $tag): ?string
    {
        if (preg_match("/<{$tag}>([^<]+)<\/{$tag}>/", $xml, $match)) {
            return html_entity_decode(trim($match[1]), ENT_QUOTES, 'UTF-8');
        }
        return null;
    }

    protected function extractAll(string $xml, string $tag): array
    {
        preg_match_all("/<{$tag}>([^<]+)<\/{$tag}>/", $xml, $matches);
        return array_map(fn($m) => html_entity_decode(trim($m), ENT_QUOTES, 'UTF-8'), $matches[1] ?? []);
    }

    protected function getNextPageUrl(string $xml): ?string
    {
        if (preg_match('/<resumptionToken[^>]*>([^<]+)<\/resumptionToken>/', $xml, $match)) {
            return "{$this->baseUrl}?verb=ListRecords&resumptionToken=" . $match[1];
        }
        return null;
    }
}
