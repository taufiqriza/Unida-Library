<?php

namespace App\Console\Commands;

use App\Models\Ebook;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportGoogleDriveEbooks extends Command
{
    protected $signature = 'ebooks:import-gdrive 
                            {--type=single : Type of import: single or multipart}
                            {--file= : Path to CSV file}
                            {--dry-run : Preview without inserting}';
    
    protected $description = 'Import ebooks from Google Drive CSV export';

    public function handle(): int
    {
        $type = $this->option('type');
        $file = $this->option('file');
        $dryRun = $this->option('dry-run');

        if (!$file) {
            $file = $type === 'multipart' 
                ? storage_path('app/imports/Ebook_MultiPart.csv')
                : storage_path('app/imports/Ebook_Satuan.csv');
        }

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $this->info("📚 Importing from: {$file}");
        $this->info("Type: {$type}");
        
        if ($dryRun) {
            $this->warn("🔍 DRY RUN - No data will be inserted");
        }

        $handle = fopen($file, 'r');
        $header = fgetcsv($handle);
        
        $this->info("Columns: " . implode(', ', $header));
        
        $count = 0;
        $errors = 0;
        $skipped = 0;

        $progressBar = $this->output->createProgressBar();
        $progressBar->start();

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $data = array_combine($header, $row);
                
                if ($type === 'single') {
                    $result = $this->importSingleEbook($data, $dryRun);
                } else {
                    $result = $this->importMultiPartEbook($data, $dryRun);
                }

                if ($result === 'created') {
                    $count++;
                } elseif ($result === 'skipped') {
                    $skipped++;
                } else {
                    $errors++;
                }

                $progressBar->advance();
            }

            if (!$dryRun) {
                DB::commit();
            } else {
                DB::rollBack();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\n❌ Error: " . $e->getMessage());
            return 1;
        }

        fclose($handle);
        $progressBar->finish();

        $this->newLine(2);
        $this->info("✅ Import completed!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Created', $count],
                ['Skipped (duplicate)', $skipped],
                ['Errors', $errors],
            ]
        );

        return 0;
    }

    protected function importSingleEbook(array $data, bool $dryRun): string
    {
        $googleDriveId = $data['google_drive_id'] ?? null;
        
        if (!$googleDriveId) {
            return 'error';
        }

        // Check if already exists
        if (Ebook::where('google_drive_id', $googleDriveId)->exists()) {
            return 'skipped';
        }

        // Extract category from folder_path
        $folderPath = $data['folder_path'] ?? '';
        $category = $this->extractCategory($folderPath);

        $ebookData = [
            'branch_id' => 1, // Default to main branch
            'title' => $data['title'] ?? 'Untitled',
            'file_source' => 'google_drive',
            'google_drive_id' => $googleDriveId,
            'google_drive_url' => "https://drive.google.com/file/d/{$googleDriveId}/view",
            'file_format' => strtoupper($data['file_format'] ?? 'PDF'),
            'file_size' => ($data['file_size_kb'] ?? 0) . ' KB',
            'language' => $this->detectLanguage($data['title'] ?? ''),
            'access_type' => 'member',
            'is_active' => true,
            'opac_hide' => false,
            'is_downloadable' => false,
        ];

        if (!$dryRun) {
            Ebook::create($ebookData);
        }

        return 'created';
    }

    protected function importMultiPartEbook(array $data, bool $dryRun): string
    {
        $folderId = $data['google_drive_folder_id'] ?? null;
        
        if (!$folderId) {
            return 'error';
        }

        // Check if already exists (using folder ID as google_drive_id)
        if (Ebook::where('google_drive_id', $folderId)->exists()) {
            return 'skipped';
        }

        $totalParts = (int)($data['total_parts'] ?? 0);

        $ebookData = [
            'branch_id' => 1, // Default to main branch
            'title' => $data['title'] ?? 'Untitled',
            'file_source' => 'google_drive',
            'google_drive_id' => $folderId,
            'google_drive_url' => "https://drive.google.com/drive/folders/{$folderId}",
            'file_format' => 'PDF',
            'pages' => $totalParts > 0 ? "{$totalParts} parts" : null,
            'language' => $this->detectLanguage($data['title'] ?? ''),
            'access_type' => 'member',
            'is_active' => true,
            'opac_hide' => false,
            'is_downloadable' => false,
        ];

        if (!$dryRun) {
            Ebook::create($ebookData);
        }

        return 'created';
    }

    protected function extractCategory(string $path): ?string
    {
        // Extract category from folder path like "/E-BOOK KUTUB 32GB/1. KUTUB PDF..."
        $parts = explode('/', trim($path, '/'));
        return $parts[0] ?? null;
    }

    protected function detectLanguage(string $title): string
    {
        // Simple detection based on characters
        if (preg_match('/[\x{0600}-\x{06FF}]/u', $title)) {
            return 'ar'; // Arabic
        }
        if (preg_match('/^[a-zA-Z\s\-]+$/', $title)) {
            return 'en'; // English
        }
        return 'id'; // Default Indonesian
    }
}
