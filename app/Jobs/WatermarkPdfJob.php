<?php

namespace App\Jobs;

use App\Models\ThesisSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;

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
            // Backup original
            $backupPath = str_replace('.pdf', '_original.pdf', $sourcePath);
            if (!file_exists($backupPath)) {
                copy($sourcePath, $backupPath);
            }

            // Decompress PDF first for FPDI compatibility
            $decompressedPath = $this->decompressPdf($backupPath);
            if (!$decompressedPath) {
                Log::error("Failed to decompress PDF for submission #{$this->submission->id}");
                return;
            }

            // Apply watermark with FPDI
            $watermarkedPath = $this->applyWatermark($decompressedPath);
            
            if ($watermarkedPath && file_exists($watermarkedPath)) {
                copy($watermarkedPath, $sourcePath);
                @unlink($watermarkedPath);
                @unlink($decompressedPath);
                Log::info("Watermark applied to {$this->fileType} for submission #{$this->submission->id}");
            }

        } catch (\Exception $e) {
            Log::error("Watermark failed for submission #{$this->submission->id}: " . $e->getMessage());
            throw $e;
        }
    }

    protected function decompressPdf(string $sourcePath): ?string
    {
        $outputPath = sys_get_temp_dir() . '/decomp_' . uniqid() . '.pdf';
        
        // Use Ghostscript to decompress/normalize PDF
        $cmd = sprintf(
            'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH ' .
            '-dCompressFonts=false -dCompressPages=false ' .
            '-sOutputFile=%s %s 2>&1',
            escapeshellarg($outputPath),
            escapeshellarg($sourcePath)
        );
        
        exec($cmd, $output, $returnCode);
        
        if ($returnCode !== 0 || !file_exists($outputPath)) {
            Log::error("GS decompress failed: " . implode("\n", $output));
            return null;
        }
        
        return $outputPath;
    }

    protected function applyWatermark(string $sourcePath): ?string
    {
        $outputPath = sys_get_temp_dir() . '/wm_' . uniqid() . '.pdf';
        
        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);
        
        $pageCount = $pdf->setSourceFile($sourcePath);
        
        for ($i = 1; $i <= $pageCount; $i++) {
            $tplId = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($tplId);
            
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tplId, 0, 0, $size['width'], $size['height']);
            
            // Add footer watermark
            $pdf->SetFont('Helvetica', '', 8);
            $pdf->SetTextColor(128, 128, 128);
            $text = 'Perpustakaan UNIDA Gontor - library.unida.gontor.ac.id';
            $textWidth = $pdf->GetStringWidth($text);
            $x = ($size['width'] - $textWidth) / 2;
            $y = $size['height'] - 10;
            $pdf->SetXY($x, $y);
            $pdf->Cell($textWidth, 5, $text, 0, 0, 'C');
        }
        
        $pdf->Output($outputPath, 'F');
        
        return file_exists($outputPath) ? $outputPath : null;
    }
}
