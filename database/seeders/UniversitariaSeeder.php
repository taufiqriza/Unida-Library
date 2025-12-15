<?php

namespace Database\Seeders;

use App\Models\DigitalCategory;
use App\Models\Ebook;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UniversitariaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating Universitaria categories...');
        
        // Create parent category for Universitaria
        $categories = [
            [
                'name' => 'Wardun Gontor',
                'slug' => 'wardun-gontor',
                'description' => 'Warta Dunia Gontor - Majalah resmi Pondok Modern Darussalam Gontor sejak 1968',
                'icon' => 'fa-newspaper',
                'sort_order' => 1,
                'pattern' => 'WARDUN',
            ],
            [
                'name' => 'Buku Peringatan',
                'slug' => 'buku-peringatan',
                'description' => 'Buku peringatan dan dokumentasi sejarah Pondok Modern Gontor',
                'icon' => 'fa-book-open',
                'sort_order' => 2,
                'pattern' => 'PERINGATAN|PERAYAAN',
            ],
            [
                'name' => 'Sejarah BPPM',
                'slug' => 'sejarah-bppm',
                'description' => 'Dokumentasi sejarah Balai Pendidikan Pondok Modern Gontor',
                'icon' => 'fa-landmark',
                'sort_order' => 3,
                'pattern' => 'SEJARAH|BPPM|PONDOK-PESANTREN',
            ],
            [
                'name' => 'Naskah Kuno',
                'slug' => 'naskah-kuno',
                'description' => 'Manuskrip dan naskah kuno bersejarah dari Pondok Gontor',
                'icon' => 'fa-scroll',
                'sort_order' => 4,
                'pattern' => 'Pelajaran|Tarbiyatul|Pusaka',
            ],
        ];

        foreach ($categories as $catData) {
            $pattern = $catData['pattern'];
            unset($catData['pattern']);
            
            $category = DigitalCategory::updateOrCreate(
                ['slug' => $catData['slug']],
                array_merge($catData, ['is_active' => true])
            );
            
            $this->command->info("Created category: {$category->name}");
        }

        // Import files from universitaria folder
        $this->command->info('Importing Universitaria files...');
        
        $basePath = storage_path('app/public/universitaria');
        if (!File::isDirectory($basePath)) {
            $this->command->error('Universitaria folder not found!');
            return;
        }

        $files = File::files($basePath);
        $imported = 0;
        $skipped = 0;

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $extension = strtolower($file->getExtension());
            
            if ($extension !== 'pdf') {
                continue;
            }

            // Skip duplicates (files with (1) suffix)
            if (preg_match('/\(\d+\)\.pdf$/', $filename)) {
                $skipped++;
                continue;
            }

            // Determine category based on filename
            $categoryId = $this->detectCategory($filename);
            
            // Clean title from filename
            $title = $this->cleanTitle($filename);
            
            // Extract year if present
            $year = $this->extractYear($filename);
            
            // Check if already exists
            $relativePath = 'universitaria/' . $filename;
            $existing = Ebook::where('file_path', $relativePath)->first();
            
            if ($existing) {
                $skipped++;
                continue;
            }

            // Create ebook entry
            Ebook::create([
                'title' => $title,
                'file_path' => $relativePath,
                'file_format' => 'pdf',
                'file_size' => round($file->getSize() / 1024 / 1024, 2), // MB
                'publish_year' => $year,
                'digital_category_id' => $categoryId,
                'collection_type' => 'universitaria',
                'access_type' => 'member', // Requires login
                'is_downloadable' => false, // Read only - protected
                'is_active' => true,
                'opac_hide' => false,
                'language' => 'id',
                'branch_id' => 1, // Default branch
            ]);

            $imported++;
            $this->command->info("  Imported: {$title}");
        }

        $this->command->info("Import complete! Imported: {$imported}, Skipped: {$skipped}");
    }

    protected function detectCategory(string $filename): ?int
    {
        $patterns = [
            'wardun-gontor' => '/WARDUN/i',
            'buku-peringatan' => '/PERINGATAN|PERAYAAN/i',
            'sejarah-bppm' => '/SEJARAH|BPPM|PONDOK-PESANTREN/i',
            'naskah-kuno' => '/Pelajaran|Tarbiyatul|Pusaka/i',
        ];

        foreach ($patterns as $slug => $pattern) {
            if (preg_match($pattern, $filename)) {
                $category = DigitalCategory::where('slug', $slug)->first();
                return $category?->id;
            }
        }

        return null;
    }

    protected function cleanTitle(string $filename): string
    {
        // Remove extension
        $title = pathinfo($filename, PATHINFO_FILENAME);
        
        // Replace dashes and underscores with spaces
        $title = str_replace(['-', '_'], ' ', $title);
        
        // Remove duplicate spaces
        $title = preg_replace('/\s+/', ' ', $title);
        
        // Capitalize properly
        $title = Str::title(trim($title));
        
        return $title;
    }

    protected function extractYear(string $filename): ?int
    {
        // Match year patterns like 1968, 1970-1971, etc
        if (preg_match('/(\d{4})/', $filename, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }
}
