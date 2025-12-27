<?php

namespace App\Jobs;

use App\Models\ThesisSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Tcpdf\Fpdi;

class WatermarkPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 600;

    public function __construct(
        public ThesisSubmission $submission,
        public string $fileType = 'fulltext'
    ) {}

    public function handle(): void
    {
        Log::info("Watermarking {$this->fileType} for submission #{$this->submission->id}");

        $filePath = match($this->fileType) {
            'fulltext' => $this->submission->fulltext_file,
            'preview' => $this->submission->preview_file,
            default => null,
        };

        if (!$filePath) {
            Log::warning("No {$this->fileType} file for submission #{$this->submission->id}");
            return;
        }

        $sourcePath = storage_path('app/thesis/' . $filePath);
        if (!file_exists($sourcePath)) {
            Log::error("File not found: {$sourcePath}");
            return;
        }

        try {
            $watermarkedPath = $this->applyWatermark($sourcePath);
            
            // Replace original with watermarked version
            if (file_exists($watermarkedPath)) {
                // Backup original
                $backupPath = str_replace('.pdf', '_original.pdf', $sourcePath);
                copy($sourcePath, $backupPath);
                
                // Replace with watermarked
                rename($watermarkedPath, $sourcePath);
                
                Log::info("Watermark applied to {$this->fileType} for submission #{$this->submission->id}");
            }

        } catch (\Exception $e) {
            Log::error("Watermark failed for submission #{$this->submission->id}: " . $e->getMessage());
            throw $e;
        }
    }

    protected function applyWatermark(string $sourcePath): string
    {
        // First, decompress PDF using Ghostscript for compatibility
        $decompressedPath = sys_get_temp_dir() . '/decomp_' . uniqid() . '.pdf';
        $gsCmd = sprintf(
            'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=%s %s 2>&1',
            escapeshellarg($decompressedPath),
            escapeshellarg($sourcePath)
        );
        exec($gsCmd, $output, $returnCode);
        
        if ($returnCode !== 0 || !file_exists($decompressedPath)) {
            Log::warning("GS decompress failed, using original: " . implode("\n", $output));
            $decompressedPath = $sourcePath;
        }
        
        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        $pageCount = $pdf->setSourceFile($decompressedPath);
        
        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($templateId);
            
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);
            
            // Footer text watermark - bottom center
            $pdf->SetAlpha(0.4);
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetTextColor(100, 100, 100);
            $pdf->SetXY(0, $size['height'] - 10);
            $pdf->Cell($size['width'], 5, 'Perpustakaan UNIDA Gontor - library.unida.gontor.ac.id', 0, 0, 'C');
            $pdf->SetAlpha(1);
        }
        
        $outputPath = sys_get_temp_dir() . '/wm_' . uniqid() . '.pdf';
        $pdf->Output($outputPath, 'F');
        
        // Cleanup decompressed file
        if ($decompressedPath !== $sourcePath && file_exists($decompressedPath)) {
            unlink($decompressedPath);
        }
        
        return $outputPath;
    }
}
