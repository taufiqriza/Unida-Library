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

        if (!$filePath) return;

        $sourcePath = storage_path('app/thesis/' . $filePath);
        if (!file_exists($sourcePath)) {
            Log::error("File not found: {$sourcePath}");
            return;
        }

        try {
            $backupPath = str_replace('.pdf', '_original.pdf', $sourcePath);
            if (!file_exists($backupPath)) {
                copy($sourcePath, $backupPath);
            }

            // Get page count and size from original
            $pageInfo = $this->getPdfInfo($backupPath);
            if (!$pageInfo) {
                Log::error("Cannot get PDF info for submission #{$this->submission->id}");
                return;
            }

            // Create watermark PDF with same page count
            $watermarkPdf = $this->createWatermarkPdf($pageInfo['pages'], $pageInfo['width'], $pageInfo['height']);
            
            // Use pdftk to stamp watermark onto original
            $outputPath = sys_get_temp_dir() . '/stamped_' . uniqid() . '.pdf';
            $cmd = sprintf(
                'pdftk %s stamp %s output %s 2>&1',
                escapeshellarg($backupPath),
                escapeshellarg($watermarkPdf),
                escapeshellarg($outputPath)
            );
            
            exec($cmd, $output, $returnCode);
            @unlink($watermarkPdf);
            
            if ($returnCode === 0 && file_exists($outputPath)) {
                copy($outputPath, $sourcePath);
                @unlink($outputPath);
                Log::info("Watermark applied to {$this->fileType} for submission #{$this->submission->id}");
            } else {
                Log::error("pdftk failed: " . implode("\n", $output));
            }

        } catch (\Exception $e) {
            Log::error("Watermark failed: " . $e->getMessage());
            throw $e;
        }
    }

    protected function getPdfInfo(string $path): ?array
    {
        // Use pdfinfo or pdftk to get page count
        exec("pdftk " . escapeshellarg($path) . " dump_data 2>/dev/null | grep -E 'NumberOfPages|PageMediaDimensions'", $output);
        
        $pages = 1;
        $width = 595; // A4 default
        $height = 842;
        
        foreach ($output as $line) {
            if (preg_match('/NumberOfPages:\s*(\d+)/', $line, $m)) {
                $pages = (int)$m[1];
            }
            if (preg_match('/PageMediaDimensions:\s*([\d.]+)\s+([\d.]+)/', $line, $m)) {
                $width = (float)$m[1];
                $height = (float)$m[2];
            }
        }
        
        return ['pages' => $pages, 'width' => $width, 'height' => $height];
    }

    protected function createWatermarkPdf(int $pages, float $width, float $height): string
    {
        $pdf = new TCPDF('P', 'pt', [$width, $height], true, 'UTF-8', false);
        $pdf->SetCreator('Perpustakaan UNIDA');
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        
        $text = 'Perpustakaan UNIDA Gontor - library.unida.gontor.ac.id';
        
        for ($i = 0; $i < $pages; $i++) {
            $pdf->AddPage();
            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetTextColor(128, 128, 128);
            
            // Footer text centered
            $textWidth = $pdf->GetStringWidth($text);
            $x = ($width - $textWidth) / 2;
            $pdf->SetXY($x, $height - 25);
            $pdf->Cell($textWidth, 10, $text, 0, 0, 'C');
        }
        
        $outputPath = sys_get_temp_dir() . '/watermark_' . uniqid() . '.pdf';
        $pdf->Output($outputPath, 'F');
        
        return $outputPath;
    }
}
