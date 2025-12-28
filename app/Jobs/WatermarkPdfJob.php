<?php

namespace App\Jobs;

use App\Models\ThesisSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use TCPDF;

class WatermarkPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 600;

    public function __construct(public ThesisSubmission $submission) {}

    public function handle(): void
    {
        // Only watermark preview file (BAB 1-3) - public access
        if (!$this->submission->preview_file) {
            return;
        }

        $sourcePath = storage_path('app/thesis/' . $this->submission->preview_file);
        if (!file_exists($sourcePath)) {
            Log::error("Preview file not found: {$sourcePath}");
            return;
        }

        Log::info("Watermarking preview for submission #{$this->submission->id}");

        try {
            // Backup original if not exists
            $backupPath = str_replace('.pdf', '_original.pdf', $sourcePath);
            if (!file_exists($backupPath)) {
                copy($sourcePath, $backupPath);
            }

            $watermarkedPath = $this->applyWatermark($backupPath);
            
            if ($watermarkedPath && file_exists($watermarkedPath) && filesize($watermarkedPath) > 0) {
                copy($watermarkedPath, $sourcePath);
                @unlink($watermarkedPath);
                Log::info("Watermark applied to preview for submission #{$this->submission->id}");
            } else {
                Log::error("Watermark failed for submission #{$this->submission->id}");
            }
        } catch (\Exception $e) {
            Log::error("Watermark error: " . $e->getMessage());
            throw $e;
        }
    }

    protected function applyWatermark(string $sourcePath): ?string
    {
        // Get page info
        $pageCount = $this->getPageCount($sourcePath);
        if ($pageCount < 1) {
            Log::error("Cannot determine page count");
            return null;
        }

        // Create watermark PDF matching each page
        $watermarkPdf = $this->createWatermarkPdf($sourcePath, $pageCount);
        if (!$watermarkPdf) {
            return null;
        }

        // Use pdftk multistamp to overlay watermark on each page
        $outputPath = sys_get_temp_dir() . '/wm_output_' . uniqid() . '.pdf';
        $cmd = sprintf(
            'pdftk %s multistamp %s output %s 2>&1',
            escapeshellarg($sourcePath),
            escapeshellarg($watermarkPdf),
            escapeshellarg($outputPath)
        );

        exec($cmd, $output, $returnCode);
        @unlink($watermarkPdf);

        if ($returnCode !== 0) {
            Log::error("pdftk failed: " . implode("\n", $output));
            return null;
        }

        return $outputPath;
    }

    protected function getPageCount(string $path): int
    {
        exec("pdftk " . escapeshellarg($path) . " dump_data 2>/dev/null | grep NumberOfPages", $output);
        foreach ($output as $line) {
            if (preg_match('/NumberOfPages:\s*(\d+)/', $line, $m)) {
                return (int)$m[1];
            }
        }
        return 0;
    }

    protected function createWatermarkPdf(string $sourcePath, int $pageCount): ?string
    {
        // Get each page size using pdftk
        $pageSizes = $this->getPageSizes($sourcePath, $pageCount);
        
        $pdf = new TCPDF('P', 'pt', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Perpustakaan UNIDA');
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $text = 'Perpustakaan UNIDA Gontor - library.unida.gontor.ac.id';

        for ($i = 0; $i < $pageCount; $i++) {
            $w = $pageSizes[$i]['width'] ?? 595;
            $h = $pageSizes[$i]['height'] ?? 842;
            
            $pdf->AddPage($h > $w ? 'P' : 'L', [$w, $h]);
            
            // Footer watermark - gray text at bottom center
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetTextColor(100, 100, 100);
            $textWidth = $pdf->GetStringWidth($text);
            $x = ($w - $textWidth) / 2;
            $y = $h - 30;
            $pdf->SetXY($x, $y);
            $pdf->Cell($textWidth, 10, $text, 0, 0, 'C');
        }

        $outputPath = sys_get_temp_dir() . '/watermark_' . uniqid() . '.pdf';
        $pdf->Output($outputPath, 'F');

        return file_exists($outputPath) ? $outputPath : null;
    }

    protected function getPageSizes(string $path, int $pageCount): array
    {
        $sizes = [];
        
        // Use pdftk dump_data to get page sizes
        exec("pdftk " . escapeshellarg($path) . " dump_data 2>/dev/null", $output);
        
        $currentPage = 0;
        foreach ($output as $line) {
            if (preg_match('/PageMediaBegin/', $line)) {
                $currentPage++;
            }
            if (preg_match('/PageMediaDimensions:\s*([\d.]+)\s+([\d.]+)/', $line, $m)) {
                $sizes[$currentPage - 1] = [
                    'width' => (float)$m[1],
                    'height' => (float)$m[2]
                ];
            }
        }

        // Fill missing with A4 default
        for ($i = 0; $i < $pageCount; $i++) {
            if (!isset($sizes[$i])) {
                $sizes[$i] = ['width' => 595, 'height' => 842];
            }
        }

        return $sizes;
    }
}
