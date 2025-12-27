<?php

namespace App\Jobs;

use App\Models\ThesisSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
            // Backup original first
            $backupPath = str_replace('.pdf', '_original.pdf', $sourcePath);
            if (!file_exists($backupPath)) {
                copy($sourcePath, $backupPath);
            }

            $watermarkedPath = $this->applyWatermarkWithGhostscript($backupPath);
            
            if ($watermarkedPath && file_exists($watermarkedPath)) {
                copy($watermarkedPath, $sourcePath);
                unlink($watermarkedPath);
                Log::info("Watermark applied to {$this->fileType} for submission #{$this->submission->id}");
            } else {
                Log::error("Watermark output not created for submission #{$this->submission->id}");
            }

        } catch (\Exception $e) {
            Log::error("Watermark failed for submission #{$this->submission->id}: " . $e->getMessage());
            throw $e;
        }
    }

    protected function applyWatermarkWithGhostscript(string $sourcePath): ?string
    {
        $outputPath = sys_get_temp_dir() . '/wm_' . uniqid() . '.pdf';
        $watermarkPs = $this->createWatermarkPostScript();
        
        // Use Ghostscript to add watermark overlay
        $cmd = sprintf(
            'gs -dBATCH -dNOPAUSE -dSAFER -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 ' .
            '-dAutoRotatePages=/None -sOutputFile=%s %s %s 2>&1',
            escapeshellarg($outputPath),
            escapeshellarg($watermarkPs),
            escapeshellarg($sourcePath)
        );
        
        exec($cmd, $output, $returnCode);
        
        // Cleanup temp PostScript file
        if (file_exists($watermarkPs)) {
            unlink($watermarkPs);
        }
        
        if ($returnCode !== 0) {
            Log::error("GS watermark failed: " . implode("\n", $output));
            return null;
        }
        
        return $outputPath;
    }

    protected function createWatermarkPostScript(): string
    {
        $psPath = sys_get_temp_dir() . '/watermark_' . uniqid() . '.ps';
        
        // PostScript that adds footer text to each page
        $ps = <<<'PS'
%!PS-Adobe-3.0
/watermark {
    gsave
    /Helvetica-Bold findfont 8 scalefont setfont
    0.5 setgray
    % Position at bottom center (adjust based on page size)
    306 15 moveto
    (Perpustakaan UNIDA Gontor - library.unida.gontor.ac.id) dup
    stringwidth pop 2 div neg 0 rmoveto show
    grestore
} def

<< /EndPage {
    exch pop
    0 eq { watermark true } { true } ifelse
} bind >> setpagedevice
PS;
        
        file_put_contents($psPath, $ps);
        return $psPath;
    }
}
