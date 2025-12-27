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
        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        $pageCount = $pdf->setSourceFile($sourcePath);
        $logoPath = storage_path('app/public/logo-unida.png');
        
        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($templateId);
            
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);
            
            // Logo watermark - 70% of page, centered, 15% opacity
            if (file_exists($logoPath)) {
                $logoWidth = $size['width'] * 0.7;
                $logoX = ($size['width'] - $logoWidth) / 2;
                $logoY = ($size['height'] - $logoWidth * 0.8) / 2; // Adjust for aspect ratio
                
                $pdf->SetAlpha(0.15);
                $pdf->Image($logoPath, $logoX, $logoY, $logoWidth, 0, 'PNG');
                $pdf->SetAlpha(1);
            }
            
            // Footer text - bottom right corner
            $pdf->SetAlpha(0.5);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetTextColor(100, 100, 100);
            $pdf->SetXY($size['width'] - 75, $size['height'] - 8);
            $pdf->Cell(70, 5, 'Perpustakaan UNIDA Gontor', 0, 0, 'R');
            $pdf->SetAlpha(1);
        }
        
        $outputPath = sys_get_temp_dir() . '/wm_' . uniqid() . '.pdf';
        $pdf->Output($outputPath, 'F');
        
        return $outputPath;
    }
}
