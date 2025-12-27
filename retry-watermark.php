<?php
// Retry watermark for pending submissions

use App\Models\ThesisSubmission;
use App\Jobs\WatermarkPdfJob;

$submissions = ThesisSubmission::whereIn('status', ['submitted', 'under_review'])->get();

foreach ($submissions as $s) {
    echo "Retry watermark for #{$s->id} - {$s->author}\n";
    if ($s->preview_file) {
        WatermarkPdfJob::dispatch($s, 'preview');
        echo "  - Dispatched preview\n";
    }
    if ($s->fulltext_file) {
        WatermarkPdfJob::dispatch($s, 'fulltext');
        echo "  - Dispatched fulltext\n";
    }
}

echo "\nDone! Check logs for results.\n";
