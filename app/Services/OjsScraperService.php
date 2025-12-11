<?php

namespace App\Services;

use App\Models\JournalArticle;
use App\Models\JournalSource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OjsScraperService
{
    protected int $timeout = 30;
    protected int $delay = 500; // ms between requests

    public function scrapeAllJournals(): array
    {
        $stats = ['journals' => 0, 'issues' => 0, 'articles' => 0, 'errors' => []];
        
        $sources = JournalSource::where('is_active', true)->get();
        
        foreach ($sources as $source) {
            try {
                $result = $this->scrapeJournal($source);
                $stats['journals']++;
                $stats['issues'] += $result['issues'];
                $stats['articles'] += $result['articles'];
                
                $source->update([
                    'last_synced_at' => now(),
                    'article_count' => $source->articles()->count(),
                ]);
            } catch (\Exception $e) {
                $stats['errors'][] = "{$source->code}: {$e->getMessage()}";
                Log::error("Scrape error for {$source->code}", ['error' => $e->getMessage()]);
            }
            
            usleep($this->delay * 1000);
        }
        
        return $stats;
    }

    public function scrapeJournal(JournalSource $source): array
    {
        $stats = ['issues' => 0, 'articles' => 0];
        $issueUrls = $this->getIssueUrls($source);
        
        foreach ($issueUrls as $issueUrl) {
            try {
                $articles = $this->scrapeIssue($source, $issueUrl);
                $stats['issues']++;
                $stats['articles'] += count($articles);
            } catch (\Exception $e) {
                Log::warning("Issue scrape error: {$issueUrl}", ['error' => $e->getMessage()]);
            }
            
            usleep($this->delay * 1000);
        }
        
        return $stats;
    }

    protected function getIssueUrls(JournalSource $source): array
    {
        $urls = [];
        $page = 1;
        
        while (true) {
            try {
                $archiveUrl = "{$source->base_url}/issue/archive" . ($page > 1 ? "/{$page}" : "");
                $response = Http::timeout($this->timeout)->get($archiveUrl);
                
                if (!$response->successful()) break;
                
                preg_match_all('/href="([^"]*\/issue\/view\/[^"]+)"/', $response->body(), $matches);
                $pageUrls = array_unique(array_map('trim', $matches[1]));
                
                if (empty($pageUrls)) break;
                
                $urls = array_merge($urls, $pageUrls);
                $page++;
                
                // Check if there's next page
                if (!str_contains($response->body(), "archive/{$page}")) break;
                
                usleep($this->delay * 1000);
            } catch (\Exception $e) {
                Log::warning("Archive page error: {$source->code} page {$page}", ['error' => $e->getMessage()]);
                break;
            }
        }
        
        return array_unique($urls);
    }

    protected function scrapeIssue(JournalSource $source, string $issueUrl): array
    {
        $response = Http::timeout($this->timeout)->get($issueUrl);
        if (!$response->successful()) return [];
        
        $html = $response->body();
        $articles = [];
        
        // Extract issue info
        preg_match('/Vol\.?\s*(\d+)\s*No\.?\s*(\d+)/i', $html, $volMatch);
        $volume = $volMatch[1] ?? null;
        $issue = $volMatch[2] ?? null;
        
        // Extract issue title
        preg_match('/<h1[^>]*>([^<]+)<\/h1>/i', $html, $titleMatch);
        $issueTitle = trim($titleMatch[1] ?? '');
        
        // Get article URLs
        preg_match_all('/href="([^"]*\/article\/view\/(\d+))"/', $html, $matches);
        $articleUrls = array_unique($matches[1]);
        
        foreach ($articleUrls as $articleUrl) {
            $articleUrl = trim($articleUrl);
            if (empty($articleUrl)) continue;
            
            // Extract external_id from URL
            preg_match('/\/article\/view\/(\d+)/', $articleUrl, $idMatch);
            $externalId = $idMatch[1] ?? null;
            if (!$externalId) continue;
            
            // Skip if already exists
            if (JournalArticle::where('external_id', $externalId)->exists()) {
                continue;
            }
            
            try {
                $article = $this->scrapeArticle($source, $articleUrl, $volume, $issue, $issueTitle);
                if ($article) $articles[] = $article;
            } catch (\Exception $e) {
                Log::warning("Article scrape error: {$articleUrl}", ['error' => $e->getMessage()]);
            }
            
            usleep($this->delay * 1000);
        }
        
        return $articles;
    }

    protected function scrapeArticle(JournalSource $source, string $url, ?string $volume, ?string $issue, ?string $issueTitle): ?JournalArticle
    {
        $response = Http::timeout($this->timeout)->get($url);
        if (!$response->successful()) return null;
        
        $html = $response->body();
        
        // Extract external_id
        preg_match('/\/article\/view\/(\d+)/', $url, $idMatch);
        $externalId = $idMatch[1] ?? md5($url);
        
        // Title
        $title = $this->extractMeta($html, 'DC.Title') 
            ?? $this->extractMeta($html, 'citation_title')
            ?? $this->extractTag($html, 'h1', 'page_title');
        
        if (!$title) return null;
        
        // Abstract
        $abstract = $this->extractMeta($html, 'DC.Description')
            ?? $this->extractMeta($html, 'description');
        
        // Authors
        $authors = $this->extractAuthors($html);
        
        // DOI
        $doi = $this->extractMeta($html, 'DC.Identifier.DOI')
            ?? $this->extractMeta($html, 'citation_doi');
        
        // PDF URL
        $pdfUrl = $this->extractMeta($html, 'citation_pdf_url');
        
        // Cover image
        $coverUrl = $this->extractMeta($html, 'og:image');
        
        // Keywords
        $keywords = $this->extractKeywords($html);
        
        // Date
        $dateStr = $this->extractMeta($html, 'DC.Date.issued')
            ?? $this->extractMeta($html, 'citation_publication_date');
        $publishedAt = $dateStr ? date('Y-m-d', strtotime($dateStr)) : null;
        $publishYear = $publishedAt ? date('Y', strtotime($publishedAt)) : ($volume ? (int)$volume + 2000 : null);
        
        // Pages
        $firstPage = $this->extractMeta($html, 'citation_firstpage');
        $lastPage = $this->extractMeta($html, 'citation_lastpage');
        $pages = $firstPage ? ($lastPage ? "{$firstPage}-{$lastPage}" : $firstPage) : null;
        
        return JournalArticle::create([
            'external_id' => $externalId,
            'journal_code' => $source->code,
            'journal_name' => $source->name,
            'title' => html_entity_decode($title, ENT_QUOTES, 'UTF-8'),
            'abstract' => $abstract ? html_entity_decode(strip_tags($abstract), ENT_QUOTES, 'UTF-8') : null,
            'authors' => $authors,
            'doi' => $doi,
            'volume' => $volume ?? $this->extractMeta($html, 'citation_volume'),
            'issue' => $issue ?? $this->extractMeta($html, 'citation_issue'),
            'issue_title' => $issueTitle,
            'pages' => $pages,
            'publish_year' => $publishYear,
            'published_at' => $publishedAt,
            'url' => $url,
            'pdf_url' => $pdfUrl,
            'cover_url' => $coverUrl,
            'keywords' => $keywords,
            'language' => 'id',
            'synced_at' => now(),
        ]);
    }

    protected function extractMeta(string $html, string $name): ?string
    {
        // Try name attribute
        if (preg_match('/<meta\s+name=["\']' . preg_quote($name) . '["\']\s+content=["\']([^"\']+)["\']/i', $html, $match)) {
            return trim($match[1]);
        }
        // Try property attribute (for og:)
        if (preg_match('/<meta\s+property=["\']' . preg_quote($name) . '["\']\s+content=["\']([^"\']+)["\']/i', $html, $match)) {
            return trim($match[1]);
        }
        // Try reversed order
        if (preg_match('/<meta\s+content=["\']([^"\']+)["\']\s+name=["\']' . preg_quote($name) . '["\']/i', $html, $match)) {
            return trim($match[1]);
        }
        return null;
    }

    protected function extractTag(string $html, string $tag, string $class): ?string
    {
        if (preg_match('/<' . $tag . '[^>]*class=["\'][^"\']*' . $class . '[^"\']*["\'][^>]*>([^<]+)<\/' . $tag . '>/i', $html, $match)) {
            return trim($match[1]);
        }
        return null;
    }

    protected function extractAuthors(string $html): array
    {
        $authors = [];
        
        // Try citation_author meta tags
        preg_match_all('/<meta\s+name=["\']citation_author["\']\s+content=["\']([^"\']+)["\']/i', $html, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $name) {
                $authors[] = ['name' => trim(html_entity_decode($name, ENT_QUOTES, 'UTF-8'))];
            }
        }
        
        // Fallback to DC.Creator
        if (empty($authors)) {
            preg_match_all('/<meta\s+name=["\']DC\.Creator["\']\s+content=["\']([^"\']+)["\']/i', $html, $matches);
            foreach ($matches[1] as $name) {
                $authors[] = ['name' => trim(html_entity_decode($name, ENT_QUOTES, 'UTF-8'))];
            }
        }
        
        return $authors;
    }

    protected function extractKeywords(string $html): array
    {
        $keywords = [];
        
        // Try citation_keywords
        $kw = $this->extractMeta($html, 'citation_keywords');
        if ($kw) {
            $keywords = array_map('trim', preg_split('/[,;]/', $kw));
        }
        
        // Try DC.Subject
        if (empty($keywords)) {
            preg_match_all('/<meta\s+name=["\']DC\.Subject["\']\s+content=["\']([^"\']+)["\']/i', $html, $matches);
            $keywords = array_map('trim', $matches[1] ?? []);
        }
        
        return array_filter($keywords);
    }
}
