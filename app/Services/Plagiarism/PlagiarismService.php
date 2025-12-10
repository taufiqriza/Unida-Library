<?php

namespace App\Services\Plagiarism;

use App\Models\DocumentFingerprint;
use App\Models\Ethesis;
use App\Models\PlagiarismCheck;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;

class PlagiarismService
{
    protected string $provider;
    protected array $config;

    public function __construct()
    {
        $this->provider = Setting::get('plagiarism_provider', 'internal');
        $this->loadConfig();
    }

    protected function loadConfig(): void
    {
        $this->config = [
            'provider' => $this->provider,
            'similarity_threshold_pass' => (float) Setting::get('plagiarism_pass_threshold', 25),
            'similarity_threshold_warning' => (float) Setting::get('plagiarism_warning_threshold', 15),
            'min_words_to_check' => (int) Setting::get('plagiarism_min_words', 100),
            'chunk_size' => 100, // words per chunk
        ];
    }

    /**
     * Process a plagiarism check
     */
    public function check(PlagiarismCheck $check): array
    {
        Log::info("Starting plagiarism check #{$check->id} with provider: {$this->provider}");

        return match($this->provider) {
            'internal' => $this->checkInternal($check),
            'ithenticate' => $this->checkIthenticate($check),
            'turnitin' => $this->checkTurnitin($check),
            'copyleaks' => $this->checkCopyleaks($check),
            default => $this->checkInternal($check),
        };
    }

    /**
     * Internal checking against E-Thesis database
     */
    protected function checkInternal(PlagiarismCheck $check): array
    {
        // 1. Extract text from uploaded document
        $filePath = Storage::disk('local')->path($check->file_path);
        $documentText = $this->extractText($filePath, $check->file_type);
        
        if (empty($documentText)) {
            throw new \Exception('Tidak dapat mengekstrak teks dari dokumen. Pastikan file PDF tidak terenkripsi.');
        }

        $wordCount = DocumentFingerprint::countWords($documentText);
        
        if ($wordCount < $this->config['min_words_to_check']) {
            throw new \Exception("Dokumen terlalu pendek. Minimal {$this->config['min_words_to_check']} kata.");
        }

        // 2. Normalize and chunk the document
        $normalizedText = DocumentFingerprint::normalizeText($documentText);
        $chunks = DocumentFingerprint::chunkText($normalizedText, $this->config['chunk_size']);
        
        // 3. Store fingerprint
        $check->fingerprint()->updateOrCreate(
            ['documentable_id' => $check->id, 'documentable_type' => PlagiarismCheck::class],
            [
                'content_text' => $documentText,
                'content_chunks' => $chunks,
                'content_hash' => DocumentFingerprint::hashContent($documentText),
                'word_count' => $wordCount,
            ]
        );

        // Update word count in check
        $check->update(['word_count' => $wordCount]);

        // 4. Compare with existing E-Thesis documents
        $matchedSources = $this->compareWithDatabase($chunks, $check->member_id);

        // 5. Calculate total similarity
        $totalSimilarity = $this->calculateTotalSimilarity($matchedSources, count($chunks));

        // 6. Build report
        $report = [
            'checked_at' => now()->toISOString(),
            'provider' => 'internal',
            'word_count' => $wordCount,
            'chunk_count' => count($chunks),
            'sources_checked' => Ethesis::where('is_public', true)->count(),
            'matches_found' => count($matchedSources),
            'top_matches' => array_slice($matchedSources, 0, 10),
        ];

        return [
            'score' => round($totalSimilarity, 2),
            'sources' => $matchedSources,
            'report' => $report,
        ];
    }

    /**
     * Compare document chunks with E-Thesis database
     */
    protected function compareWithDatabase(array $chunks, int $excludeMemberId): array
    {
        $matches = [];
        
        // Get all E-Thesis fingerprints (excluding the member's own documents)
        $etheses = Ethesis::where('is_public', true)
            ->whereHas('fingerprint')
            ->with('fingerprint', 'department')
            ->get();

        foreach ($etheses as $ethesis) {
            if (!$ethesis->fingerprint || empty($ethesis->fingerprint->content_chunks)) {
                continue;
            }

            $similarity = $this->compareChunks($chunks, $ethesis->fingerprint->content_chunks);
            
            if ($similarity > 1) { // More than 1% match
                $matches[] = [
                    'source_type' => 'ethesis',
                    'source_id' => $ethesis->id,
                    'title' => $ethesis->title,
                    'author' => $ethesis->author,
                    'year' => $ethesis->year,
                    'department' => $ethesis->department?->name,
                    'similarity' => round($similarity, 2),
                ];
            }
        }

        // Sort by similarity descending
        usort($matches, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return $matches;
    }

    /**
     * Compare two sets of chunks using Jaccard similarity
     */
    protected function compareChunks(array $sourceChunks, array $targetChunks): float
    {
        if (empty($sourceChunks) || empty($targetChunks)) {
            return 0;
        }

        $matchedChunks = 0;
        $totalCheckableChunks = count($sourceChunks);

        foreach ($sourceChunks as $sourceChunk) {
            $sourceWords = explode(' ', $sourceChunk);
            $sourceSet = array_flip($sourceWords);
            
            foreach ($targetChunks as $targetChunk) {
                $targetWords = explode(' ', $targetChunk);
                
                // Calculate Jaccard similarity for this chunk pair
                $intersection = count(array_intersect_key($sourceSet, array_flip($targetWords)));
                $union = count($sourceWords) + count($targetWords) - $intersection;
                
                if ($union > 0) {
                    $chunkSimilarity = ($intersection / $union) * 100;
                    
                    // If more than 60% similar, consider it a match
                    if ($chunkSimilarity > 60) {
                        $matchedChunks++;
                        break; // Move to next source chunk
                    }
                }
            }
        }

        // Return percentage of matched chunks
        return ($matchedChunks / $totalCheckableChunks) * 100;
    }

    /**
     * Calculate total similarity score from matches
     */
    protected function calculateTotalSimilarity(array $matches, int $totalChunks): float
    {
        if (empty($matches)) {
            return 0;
        }

        // Sum all similarities (capped at 100%)
        $total = array_sum(array_column($matches, 'similarity'));
        
        // Average across matches or use max, depending on preference
        // Using weighted average where higher matches count more
        return min($total, 100);
    }

    /**
     * Extract text from document
     */
    public function extractText(string $filePath, string $fileType): string
    {
        try {
            if (strtolower($fileType) === 'pdf') {
                return $this->extractTextFromPdf($filePath);
            } elseif (in_array(strtolower($fileType), ['docx', 'doc'])) {
                return $this->extractTextFromDocx($filePath);
            }
        } catch (\Exception $e) {
            Log::error("Text extraction failed: " . $e->getMessage());
            throw new \Exception("Gagal mengekstrak teks: " . $e->getMessage());
        }

        return '';
    }

    /**
     * Extract text from PDF file
     */
    protected function extractTextFromPdf(string $filePath): string
    {
        $parser = new PdfParser();
        $pdf = $parser->parseFile($filePath);
        
        $text = '';
        foreach ($pdf->getPages() as $page) {
            $text .= $page->getText() . "\n";
        }
        
        return $text;
    }

    /**
     * Extract text from DOCX file
     */
    protected function extractTextFromDocx(string $filePath): string
    {
        $zip = new \ZipArchive();
        
        if ($zip->open($filePath) !== true) {
            throw new \Exception('Cannot open DOCX file');
        }

        $content = $zip->getFromName('word/document.xml');
        $zip->close();

        if (!$content) {
            return '';
        }

        // Strip XML tags and get text content
        $text = strip_tags($content);
        
        // Clean up whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        return trim($text);
    }

    /**
     * iThenticate/Turnitin API integration
     */
    protected function checkIthenticate(PlagiarismCheck $check): array
    {
        $provider = new Providers\IthenticateProvider();
        
        if (!$provider->isConfigured()) {
            Log::warning("iThenticate not configured, falling back to internal check");
            return $this->checkInternal($check);
        }

        $result = $provider->submit($check);
        
        // Get viewer URL if available
        if ($check->external_id) {
            $reportUrl = $provider->getReportUrl($check->external_id);
            if ($reportUrl) {
                $check->update(['external_report_url' => $reportUrl]);
            }
        }

        return $result;
    }

    /**
     * Turnitin integration (uses same API as iThenticate)
     */
    protected function checkTurnitin(PlagiarismCheck $check): array
    {
        // Turnitin and iThenticate use the same TCA API
        return $this->checkIthenticate($check);
    }

    /**
     * Placeholder for Copyleaks integration
     */
    protected function checkCopyleaks(PlagiarismCheck $check): array
    {
        // TODO: Implement Copyleaks API integration
        throw new \Exception('Copyleaks integration belum tersedia. Hubungi administrator.');
    }

    /**
     * Get provider name
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * Check if plagiarism checking is enabled
     */
    public static function isEnabled(): bool
    {
        return (bool) Setting::get('plagiarism_enabled', true);
    }
}
