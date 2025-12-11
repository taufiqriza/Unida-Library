<?php

namespace App\Services;

use App\Models\JournalArticle;
use App\Models\JournalSource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OjsSyncService
{
    /**
     * Sync all active journal sources
     */
    public function syncAll(): array
    {
        $results = [];
        $sources = JournalSource::where('is_active', true)->get();

        foreach ($sources as $source) {
            $results[$source->code] = $this->syncSource($source);
        }

        return $results;
    }

    /**
     * Sync a single journal source
     */
    public function syncSource(JournalSource $source): array
    {
        Log::info("Syncing journal: {$source->name}");

        try {
            $articles = match ($source->feed_type) {
                'atom' => $this->parseAtomFeed($source),
                'rss' => $this->parseRssFeed($source),
                default => [],
            };

            $created = 0;
            $updated = 0;

            foreach ($articles as $articleData) {
                $article = JournalArticle::updateOrCreate(
                    ['external_id' => $articleData['external_id']],
                    $articleData
                );

                $article->wasRecentlyCreated ? $created++ : $updated++;
            }

            $source->update([
                'last_synced_at' => now(),
                'article_count' => $source->articles()->count(),
            ]);

            Log::info("Synced {$source->code}: {$created} created, {$updated} updated");

            return [
                'success' => true,
                'created' => $created,
                'updated' => $updated,
                'total' => count($articles),
            ];

        } catch (\Exception $e) {
            Log::error("Failed to sync {$source->code}: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Parse Atom feed from OJS
     */
    protected function parseAtomFeed(JournalSource $source): array
    {
        $response = Http::timeout(60)->get($source->feed_url);

        if (!$response->successful()) {
            throw new \Exception("Failed to fetch feed: HTTP {$response->status()}");
        }

        $xml = simplexml_load_string($response->body());
        if (!$xml) {
            throw new \Exception("Invalid XML response");
        }

        $articles = [];
        $xml->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');

        foreach ($xml->entry as $entry) {
            $id = (string) $entry->id;
            $externalId = $this->extractArticleId($id);

            $authors = [];
            foreach ($entry->author as $author) {
                $authors[] = [
                    'name' => (string) $author->name,
                    'email' => (string) ($author->email ?? ''),
                ];
            }

            $publishedAt = null;
            if (!empty($entry->published)) {
                $publishedAt = date('Y-m-d', strtotime((string) $entry->published));
            }

            $abstract = strip_tags((string) $entry->summary);
            $abstract = html_entity_decode($abstract, ENT_QUOTES, 'UTF-8');
            $abstract = Str::limit(trim($abstract), 5000);

            $articles[] = [
                'external_id' => $externalId,
                'journal_code' => $source->code,
                'journal_name' => $source->name,
                'title' => (string) $entry->title,
                'abstract' => $abstract,
                'authors' => $authors,
                'url' => (string) $entry->link['href'],
                'published_at' => $publishedAt,
                'publish_year' => $publishedAt ? date('Y', strtotime($publishedAt)) : null,
                'rights' => (string) ($entry->rights ?? ''),
                'synced_at' => now(),
            ];
        }

        return $articles;
    }

    /**
     * Parse RSS feed
     */
    protected function parseRssFeed(JournalSource $source): array
    {
        $response = Http::timeout(60)->get($source->feed_url);

        if (!$response->successful()) {
            throw new \Exception("Failed to fetch feed: HTTP {$response->status()}");
        }

        $xml = simplexml_load_string($response->body());
        if (!$xml) {
            throw new \Exception("Invalid XML response");
        }

        $articles = [];

        foreach ($xml->channel->item as $item) {
            $link = (string) $item->link;
            $externalId = $this->extractArticleId($link);

            $articles[] = [
                'external_id' => $externalId,
                'journal_code' => $source->code,
                'journal_name' => $source->name,
                'title' => (string) $item->title,
                'abstract' => Str::limit(strip_tags((string) $item->description), 5000),
                'authors' => [['name' => (string) ($item->author ?? '')]],
                'url' => $link,
                'published_at' => date('Y-m-d', strtotime((string) $item->pubDate)),
                'publish_year' => date('Y', strtotime((string) $item->pubDate)),
                'synced_at' => now(),
            ];
        }

        return $articles;
    }

    /**
     * Extract article ID from OJS URL
     */
    protected function extractArticleId(string $url): string
    {
        // Pattern: /article/view/12345
        if (preg_match('/article\/view\/(\d+)/', $url, $matches)) {
            return 'ojs-' . $matches[1];
        }
        return 'ojs-' . md5($url);
    }

    /**
     * Register a new journal source
     */
    public static function registerJournal(string $code, string $name, string $baseUrl): JournalSource
    {
        $feedUrl = rtrim($baseUrl, '/') . '/gateway/plugin/WebFeedGatewayPlugin/atom';

        return JournalSource::updateOrCreate(
            ['code' => $code],
            [
                'name' => $name,
                'base_url' => $baseUrl,
                'feed_type' => 'atom',
                'feed_url' => $feedUrl,
                'is_active' => true,
            ]
        );
    }
}
