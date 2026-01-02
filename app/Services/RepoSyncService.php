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

    // OAI-PMH set codes
    const SET_THESIS = '74797065733D746865736973';
    const SET_ARTICLE = '74797065733D61727469636C65';

    public function sync(?string $type = null): array
    {
        $stats = ['thesis' => 0, 'article' => 0, 'skipped' => 0, 'errors' => 0];

        if (!$type || $type === 'thesis') {
            $result = $this->syncSet(self::SET_THESIS, 'thesis');
            $stats['thesis'] = $result['saved'];
            $stats['skipped'] += $result['skipped'];
            $stats['errors'] += $result['errors'];
        }

        if (!$type || $type === 'article') {
            $result = $this->syncSet(self::SET_ARTICLE, 'article');
            $stats['article'] = $result['saved'];
            $stats['skipped'] += $result['skipped'];
            $stats['errors'] += $result['errors'];
        }

        return $stats;
    }

    public function syncSet(string $setSpec, string $targetType): array
    {
        $stats = ['saved' => 0, 'skipped' => 0, 'errors' => 0];
        $url = "{$this->baseUrl}?verb=ListRecords&metadataPrefix=oai_dc&set={$setSpec}";
        $pageCount = 0;

        while ($url) {
            try {
                $pageCount++;
                Log::info("Syncing {$targetType} page {$pageCount}...");

                $response = Http::retry(3, 2000)->timeout($this->timeout)->get($url);
                if (!$response->successful()) {
                    Log::warning("HTTP error", ['page' => $pageCount, 'status' => $response->status()]);
                    $stats['errors']++;
                    break;
                }

                $result = $this->processPage($response->body(), $targetType);
                $stats['saved'] += $result['saved'];
                $stats['skipped'] += $result['skipped'];
                $stats['errors'] += $result['errors'];

                $url = $this->getNextPageUrl($response->body());
                if ($url) usleep(500000);
            } catch (\Exception $e) {
                Log::error('Sync error', ['type' => $targetType, 'page' => $pageCount, 'error' => $e->getMessage()]);
                $stats['errors']++;
                break;
            }
        }

        Log::info("Sync {$targetType} completed", ['pages' => $pageCount, 'stats' => $stats]);
        return $stats;
    }

    protected function processPage(string $xml, string $targetType): array
    {
        $stats = ['saved' => 0, 'skipped' => 0, 'errors' => 0];

        preg_match_all('/<record>(.*?)<\/record>/s', $xml, $matches);

        foreach ($matches[1] as $record) {
            try {
                $saved = $targetType === 'thesis'
                    ? $this->processThesis($record)
                    : $this->processArticle($record);
                $saved ? $stats['saved']++ : $stats['skipped']++;
            } catch (\Exception $e) {
                Log::warning('Record error', ['error' => substr($e->getMessage(), 0, 200)]);
                $stats['errors']++;
            }
        }

        return $stats;
    }

    protected function processThesis(string $record): bool
    {
        preg_match('/<identifier>oai:repo\.unida\.gontor\.ac\.id:(\d+)<\/identifier>/', $record, $m);
        $externalId = $m[1] ?? null;
        if (!$externalId) return false;

        if (Ethesis::where('source_type', 'repo')->where('external_id', $externalId)->exists()) {
            return false;
        }

        return (bool) $this->saveThesis($record, $externalId);
    }

    protected function processArticle(string $record): bool
    {
        preg_match('/<identifier>oai:repo\.unida\.gontor\.ac\.id:(\d+)<\/identifier>/', $record, $m);
        $externalId = $m[1] ?? null;
        if (!$externalId) return false;

        if (JournalArticle::where('source_type', 'repo')->where('external_id', $externalId)->exists()) {
            return false;
        }

        return (bool) $this->saveArticle($record, $externalId);
    }

    protected function saveThesis(string $record, string $externalId): ?string
    {
        $title = $this->extract($record, 'dc:title');
        if (!$title) return null;

        $creators = $this->extractAll($record, 'dc:creator');
        $author = $creators[0] ?? 'Unknown';

        $abstract = $this->extract($record, 'dc:description');
        $date = $this->extract($record, 'dc:date');
        $year = $date ? (int) substr($date, 0, 4) : null;

        $relations = $this->extractAll($record, 'dc:relation');
        $url = collect($relations)->first(fn($r) => str_contains($r, 'repo.unida.gontor.ac.id'));

        $identifiers = $this->extractAll($record, 'dc:identifier');
        $pdfUrl = collect($identifiers)->first(fn($i) => str_ends_with(strtolower($i), '.pdf'));

        Ethesis::create([
            'source_type' => 'repo',
            'external_id' => $externalId,
            'external_url' => $url ?? "https://repo.unida.gontor.ac.id/{$externalId}/",
            'title' => $title,
            'author' => $author,
            'abstract' => $abstract,
            'year' => $year,
            'type' => 'skripsi',
            'is_public' => true,
            'file_path' => $pdfUrl,
        ]);

        return 'thesis';
    }

    protected function saveArticle(string $record, string $externalId): ?string
    {
        $title = $this->extract($record, 'dc:title');
        if (!$title) return null;

        $creators = $this->extractAll($record, 'dc:creator');
        $authors = array_map(fn($c) => ['name' => trim($c)], $creators);

        $abstract = $this->extract($record, 'dc:description');
        $date = $this->extract($record, 'dc:date');
        $year = $date ? (int) substr($date, 0, 4) : null;

        $relations = $this->extractAll($record, 'dc:relation');
        $repoUrl = collect($relations)->first(fn($r) => str_contains($r, 'repo.unida.gontor.ac.id'));
        $ojsUrl = collect($relations)->first(fn($r) => str_contains($r, 'ejournal.unida.gontor.ac.id'));

        $identifiers = $this->extractAll($record, 'dc:identifier');
        $pdfUrl = collect($identifiers)->first(fn($i) => str_ends_with(strtolower($i), '.pdf'));

        JournalArticle::create([
            'source_type' => 'repo',
            'external_id' => $externalId,
            'journal_code' => 'repo',
            'journal_name' => 'UNIDA Repository',
            'title' => $title,
            'abstract' => $abstract,
            'authors' => $authors,
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
