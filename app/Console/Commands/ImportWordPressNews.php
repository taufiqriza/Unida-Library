<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Models\NewsCategory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportWordPressNews extends Command
{
    protected $signature = 'wordpress:import-news 
                            {--per-page=100 : Number of posts per page}
                            {--dry-run : Preview without actually importing}
                            {--force : Update even if exists}';
    
    protected $description = 'Import news articles from WordPress library.digilib-unida.id';

    protected $apiBase = 'https://library.digilib-unida.id/wp-json/wp/v2';
    
    protected $categoryMapping = [];

    public function handle(): int
    {
        $this->info('🚀 Starting WordPress News Import...');
        $this->newLine();
        
        // Ensure local categories exist
        $this->setupCategories();
        
        // Fetch WordPress categories first
        $this->fetchWordPressCategories();
        
        // Fetch all posts with pagination
        $posts = $this->fetchAllPosts();
        
        if (empty($posts)) {
            $this->error('❌ No posts found or API error!');
            return Command::FAILURE;
        }
        
        $this->info("📰 Found " . count($posts) . " posts to import");
        $this->newLine();
        
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        
        $bar = $this->output->createProgressBar(count($posts));
        $bar->start();
        
        foreach ($posts as $post) {
            $result = $this->importPost($post);
            
            if ($result === 'imported') {
                $imported++;
            } elseif ($result === 'updated') {
                $updated++;
            } else {
                $skipped++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("✅ Import completed!");
        $this->table(
            ['Status', 'Count'],
            [
                ['New Imported', $imported],
                ['Updated', $updated],
                ['Skipped (already exists)', $skipped],
                ['Total Processed', count($posts)],
            ]
        );
        
        return Command::SUCCESS;
    }
    
    protected function setupCategories(): void
    {
        $this->info('📁 Setting up local categories...');
        
        NewsCategory::firstOrCreate(
            ['slug' => 'news'],
            ['name' => 'News', 'sort_order' => 1]
        );
        
        NewsCategory::firstOrCreate(
            ['slug' => 'artikel'],
            ['name' => 'Artikel', 'sort_order' => 2]
        );
        
        NewsCategory::firstOrCreate(
            ['slug' => 'seminar'],
            ['name' => 'Seminar', 'sort_order' => 3]
        );
        
        NewsCategory::firstOrCreate(
            ['slug' => 'event'],
            ['name' => 'Event', 'sort_order' => 4]
        );
    }
    
    protected function fetchWordPressCategories(): void
    {
        $this->info('📂 Fetching WordPress categories...');
        
        try {
            $response = Http::timeout(30)->get("{$this->apiBase}/categories", [
                'per_page' => 100,
            ]);
            
            if ($response->successful()) {
                $wpCategories = $response->json();
                
                foreach ($wpCategories as $wpCat) {
                    $slug = Str::slug($wpCat['slug']);
                    $this->categoryMapping[$wpCat['id']] = $slug;
                    $this->line("  - Category #{$wpCat['id']}: {$wpCat['name']} → {$slug}");
                }
            }
        } catch (\Exception $e) {
            $this->warn("⚠️ Could not fetch categories: " . $e->getMessage());
        }
    }
    
    protected function fetchAllPosts(): array
    {
        $allPosts = [];
        $page = 1;
        $perPage = (int) $this->option('per-page');
        
        $this->info("📥 Fetching posts from WordPress API...");
        
        do {
            try {
                $response = Http::timeout(60)->get("{$this->apiBase}/posts", [
                    'page' => $page,
                    'per_page' => $perPage,
                    'orderby' => 'date',
                    'order' => 'desc',
                ]);
                
                if (!$response->successful()) {
                    $this->warn("⚠️ API returned status: " . $response->status());
                    break;
                }
                
                $posts = $response->json();
                
                if (empty($posts)) {
                    break;
                }
                
                $allPosts = array_merge($allPosts, $posts);
                
                // Check X-WP-TotalPages header
                $totalPages = $response->header('X-WP-TotalPages', 1);
                $total = $response->header('X-WP-Total', count($posts));
                
                $this->line("  - Page {$page}/{$totalPages}: fetched " . count($posts) . " posts (total: {$total})");
                
                $page++;
                
                // Safety limit
                if ($page > 100) {
                    $this->warn("⚠️ Reached page limit (100), stopping...");
                    break;
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Error fetching page {$page}: " . $e->getMessage());
                break;
            }
            
        } while ($page <= $totalPages);
        
        return $allPosts;
    }
    
    protected function importPost(array $post): string
    {
        $slug = $post['slug'] ?? Str::slug($post['title']['rendered'] ?? 'untitled');
        
        // Check if already exists
        $existing = News::where('slug', $slug)->first();
        
        if ($existing && !$this->option('force')) {
            // Update image if missing
            if (!$existing->external_image) {
                $imageUrl = $this->extractImageUrl($post);
                if ($imageUrl && !$this->option('dry-run')) {
                    $existing->update(['external_image' => $imageUrl]);
                    return 'updated';
                }
            }
            return 'skipped';
        }
        
        // Prepare data
        $data = $this->preparePostData($post);
        
        if ($this->option('dry-run')) {
            $this->newLine();
            $this->line("  [DRY-RUN] Would import: {$data['title']}");
            return 'imported';
        }
        
        if ($existing && $this->option('force')) {
            $existing->update($data);
            return 'updated';
        }
        
        News::create($data);
        return 'imported';
    }
    
    protected function preparePostData(array $post): array
    {
        $title = html_entity_decode(strip_tags($post['title']['rendered'] ?? 'Untitled'), ENT_QUOTES, 'UTF-8');
        $slug = $post['slug'] ?? Str::slug($title);
        
        // Clean excerpt - remove HTML and trim
        $excerpt = strip_tags($post['excerpt']['rendered'] ?? '');
        $excerpt = html_entity_decode($excerpt, ENT_QUOTES, 'UTF-8');
        $excerpt = trim(preg_replace('/\s+/', ' ', $excerpt));
        $excerpt = Str::limit($excerpt, 300);
        
        // Clean content - keep basic HTML
        $content = $post['content']['rendered'] ?? '';
        $content = $this->cleanContent($content);
        
        // Get category
        $categoryId = $this->resolveCategory($post['categories'] ?? []);
        
        // Get image URL
        $imageUrl = $this->extractImageUrl($post);
        
        // Parse date
        $publishedAt = Carbon::parse($post['date'] ?? now());
        
        return [
            'branch_id' => 1,
            'news_category_id' => $categoryId,
            'user_id' => 1,
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $content,
            'featured_image' => null,
            'external_image' => $imageUrl,
            'status' => 'published',
            'is_featured' => false,
            'is_pinned' => false,
            'published_at' => $publishedAt,
            'views' => rand(10, 200),
        ];
    }
    
    protected function extractImageUrl(array $post): ?string
    {
        // Try yoast_head_json first (most reliable)
        if (isset($post['yoast_head_json']['og_image'][0]['url'])) {
            return $post['yoast_head_json']['og_image'][0]['url'];
        }
        
        // Try parsing from yoast_head meta
        if (isset($post['yoast_head'])) {
            preg_match('/property="og:image"\s+content="([^"]+)"/', $post['yoast_head'], $matches);
            if (!empty($matches[1])) {
                return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
            }
        }
        
        // Try featured_media endpoint
        if (!empty($post['featured_media'])) {
            try {
                $response = Http::timeout(10)->get("{$this->apiBase}/media/{$post['featured_media']}");
                if ($response->successful()) {
                    $media = $response->json();
                    return $media['source_url'] ?? $media['guid']['rendered'] ?? null;
                }
            } catch (\Exception $e) {
                // Ignore media fetch errors
            }
        }
        
        // Try extracting from content
        if (isset($post['content']['rendered'])) {
            preg_match('/<img[^>]+src="([^"]+)"/', $post['content']['rendered'], $matches);
            if (!empty($matches[1])) {
                $url = html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
                if (Str::contains($url, 'library.digilib-unida.id')) {
                    return $url;
                }
            }
        }
        
        return null;
    }
    
    protected function resolveCategory(array $wpCategoryIds): int
    {
        $defaultCategory = NewsCategory::where('slug', 'news')->first();
        $defaultId = $defaultCategory ? $defaultCategory->id : 1;
        
        foreach ($wpCategoryIds as $wpCatId) {
            $localSlug = $this->categoryMapping[$wpCatId] ?? null;
            
            if ($localSlug) {
                // Map WordPress slugs to our local slugs
                $mappedSlug = $this->mapCategorySlug($localSlug);
                $localCat = NewsCategory::where('slug', $mappedSlug)->first();
                
                if ($localCat) {
                    return $localCat->id;
                }
            }
        }
        
        return $defaultId;
    }
    
    protected function mapCategorySlug(string $wpSlug): string
    {
        // Map WordPress category slugs to local slugs
        $mapping = [
            'news' => 'news',
            'artikel' => 'artikel',
            'seminar' => 'seminar',
            'event' => 'event',
            'kegiatan' => 'event',
            'berita' => 'news',
            'article' => 'artikel',
        ];
        
        return $mapping[$wpSlug] ?? 'news';
    }
    
    protected function cleanContent(string $content): string
    {
        // Remove elementor/WordPress specific data attributes
        $content = preg_replace('/\s*data-[a-z0-9_-]+="[^"]*"/i', '', $content);
        
        // Remove inline styles from elementor
        $content = preg_replace('/<div[^>]*class="elementor[^"]*"[^>]*>/i', '<div>', $content);
        
        // Remove empty divs
        $content = preg_replace('/<div>\s*<\/div>/', '', $content);
        
        // Clean up excessive whitespace
        $content = preg_replace('/\s+/', ' ', $content);
        $content = preg_replace('/<\/p>\s+<p/', '</p><p', $content);
        
        // Convert escaped entities
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
        
        return trim($content);
    }
}
