<?php

namespace App\Livewire\Staff\Elibrary;

use App\Models\Ethesis;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\FullTextExtractionService;
use App\Http\Controllers\SitemapController;

class RepositoryAnalytics extends Component
{
    public array $indexingStats = [];
    public array $recentIndexed = [];
    public array $oaiStats = [];
    
    public function mount()
    {
        $this->loadIndexingStats();
        $this->loadRecentIndexed();
        $this->loadOaiStats();
    }
    
    public function loadIndexingStats()
    {
        $total = Ethesis::where('is_public', true)->count();
        $withMetadata = Ethesis::where('is_public', true)
            ->whereNotNull('abstract')
            ->whereNotNull('keywords')
            ->count();
        $withFulltext = Ethesis::where('is_public', true)
            ->whereNotNull('file_path')
            ->count();
        $withSearchableContent = Ethesis::where('is_public', true)
            ->whereNotNull('searchable_content')
            ->count();
            
        $this->indexingStats = [
            'total_public' => $total,
            'with_metadata' => $withMetadata,
            'with_fulltext' => $withFulltext,
            'with_searchable_content' => $withSearchableContent,
            'metadata_completeness' => $total > 0 ? round(($withMetadata / $total) * 100, 1) : 0,
            'fulltext_availability' => $total > 0 ? round(($withFulltext / $total) * 100, 1) : 0,
            'content_indexed' => $total > 0 ? round(($withSearchableContent / $total) * 100, 1) : 0,
        ];
    }
    
    public function generateSitemap()
    {
        try {
            $controller = app(SitemapController::class);
            $result = $controller->generate();
            
            $this->dispatch('alert', ['type' => 'success', 'message' => "Sitemap generated! {$result['total_urls']} URLs indexed."]);
        } catch (\Exception $e) {
            \Log::error('Sitemap generation failed: ' . $e->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Sitemap generation failed: ' . $e->getMessage()]);
        }
    }
    
    public function loadRecentIndexed()
    {
        $this->recentIndexed = Ethesis::where('is_public', true)
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->map(fn($thesis) => [
                'id' => $thesis->id,
                'title' => $thesis->title,
                'author' => $thesis->author,
                'updated_at' => $thesis->updated_at,
                'has_metadata' => !empty($thesis->abstract) && !empty($thesis->keywords),
                'has_fulltext' => $thesis->is_fulltext_public && !empty($thesis->file_path),
            ])
            ->toArray();
    }
    
    public function loadOaiStats()
    {
        $this->oaiStats = [
            'endpoint_active' => true, // Will implement OAI-PMH endpoint
            'total_records' => Ethesis::where('is_public', true)->count(),
            'last_harvest' => Cache::get('oai_last_harvest', 'Never'),
            'harvest_count' => Cache::get('oai_harvest_count', 0),
        ];
    }
    
    public function testGoogleScholar()
    {
        try {
            // Test if Google Scholar can access our pages
            $sampleThesis = Ethesis::where('is_public', true)->first();
            if (!$sampleThesis) {
                $this->dispatch('alert', ['type' => 'warning', 'message' => 'No public thesis found for testing']);
                return;
            }
            
            $url = route('opac.ethesis.show', $sampleThesis->id);
            
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1)'])
                ->get($url);
                
            if ($response->successful()) {
                $this->dispatch('alert', ['type' => 'success', 'message' => 'Test successful! Page is accessible to crawlers. Sample: ' . $sampleThesis->title]);
            } else {
                $this->dispatch('alert', ['type' => 'error', 'message' => 'Test failed! HTTP ' . $response->status()]);
            }
        } catch (\Exception $e) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Test failed: ' . $e->getMessage()]);
        }
    }
    
    public function processFullText()
    {
        try {
            $service = app(FullTextExtractionService::class);
            $result = $service->batchProcess(10); // Process 10 at a time for better performance
            
            if (count($result['processed']) > 0) {
                $message = "✅ Successfully processed {$result['total']} thesis. Success: " . count($result['processed']) . ", Failed: " . count($result['failed']);
                $type = count($result['failed']) > 0 ? 'warning' : 'success';
            } else {
                $message = "ℹ️ No thesis found for processing. All may already be processed.";
                $type = 'info';
            }
            
            $this->dispatch('alert', ['type' => $type, 'message' => $message]);
            $this->loadIndexingStats(); // Refresh stats
        } catch (\Exception $e) {
            \Log::error('Full-text processing failed: ' . $e->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Processing failed: ' . $e->getMessage()]);
        }
    }
    
    public function render()
    {
        return view('livewire.staff.elibrary.repository-analytics');
    }
}
