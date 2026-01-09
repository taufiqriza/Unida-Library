<?php

namespace App\Services;

use App\Models\Ethesis;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FullTextExtractionService
{
    private Parser $pdfParser;
    
    public function __construct()
    {
        $this->pdfParser = new Parser();
    }
    
    public function extractFromPdf(string $filePath): ?string
    {
        try {
            $fullPath = Storage::disk('public')->path($filePath);
            
            if (!file_exists($fullPath)) {
                Log::warning("PDF file not found: {$fullPath}");
                return null;
            }
            
            $pdf = $this->pdfParser->parseFile($fullPath);
            $text = $pdf->getText();
            
            // Clean extracted text
            $text = $this->cleanExtractedText($text);
            
            return $text;
        } catch (\Exception $e) {
            Log::error("PDF extraction failed for {$filePath}: " . $e->getMessage());
            return null;
        }
    }
    
    public function processThesis(Ethesis $thesis): bool
    {
        if (!$thesis->file_path || !$thesis->is_fulltext_public) {
            return false;
        }
        
        $extractedText = $this->extractFromPdf('thesis/' . $thesis->file_path);
        
        if (!$extractedText) {
            return false;
        }
        
        // Store extracted text for search indexing
        $thesis->update([
            'searchable_content' => $extractedText,
            'content_indexed_at' => now(),
        ]);
        
        return true;
    }
    
    public function batchProcess(int $limit = 10): array
    {
        $processed = [];
        $failed = [];
        
        $theses = Ethesis::where('is_public', true)
            ->where('is_fulltext_public', true)
            ->whereNotNull('file_path')
            ->whereNull('content_indexed_at')
            ->limit($limit)
            ->get();
            
        foreach ($theses as $thesis) {
            if ($this->processThesis($thesis)) {
                $processed[] = $thesis->id;
            } else {
                $failed[] = $thesis->id;
            }
        }
        
        return [
            'processed' => $processed,
            'failed' => $failed,
            'total' => count($processed) + count($failed),
        ];
    }
    
    private function cleanExtractedText(string $text): string
    {
        // Remove excessive whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Remove non-printable characters
        $text = preg_replace('/[^\x20-\x7E\x0A\x0D]/', '', $text);
        
        // Trim and limit length for database storage
        $text = trim($text);
        
        // Limit to reasonable size (e.g., 50KB)
        if (strlen($text) > 50000) {
            $text = substr($text, 0, 50000) . '...';
        }
        
        return $text;
    }
}
