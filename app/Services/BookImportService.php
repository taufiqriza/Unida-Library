<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Author;
use App\Models\Branch;
use App\Models\BookItem;
use App\Models\ImportBatch;
use App\Models\Publisher;
use App\Models\Subject;
use App\Models\CollectionType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ZipArchive;

class BookImportService
{
    protected array $languages = [
        'id' => 'Indonesia',
        'ar' => 'Arab',
        'en' => 'Inggris',
    ];

    protected array $requiredColumns = ['judul', 'penulis', 'jumlah_eksemplar'];
    protected array $coverFiles = [];

    /**
     * Generate premium Excel template
     */
    public function generateTemplate(Branch $branch): string
    {
        $spreadsheet = new Spreadsheet();
        
        // Sheet 1: Data Koleksi
        $this->createDataSheet($spreadsheet, $branch);
        
        // Sheet 2: Panduan
        $this->createGuideSheet($spreadsheet);
        
        // Sheet 3: Daftar DDC
        $this->createDdcSheet($spreadsheet);
        
        // Sheet 4: Daftar Kategori
        $this->createCategorySheet($spreadsheet);

        // Set active sheet to first
        $spreadsheet->setActiveSheetIndex(0);

        // Save to temp file
        $filename = 'template_import_koleksi_' . Str::slug($branch->name) . '_' . date('Ymd') . '.xlsx';
        $path = storage_path('app/temp/' . $filename);
        
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return $path;
    }

    protected function createDataSheet(Spreadsheet $spreadsheet, Branch $branch): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('📚 Data Koleksi');

        // Header branding
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'PERPUSTAKAAN UNIDA GONTOR');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2', 'Template Import Koleksi Buku v1.0');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Info section
        $sheet->setCellValue('A4', 'Cabang:');
        $sheet->setCellValue('B4', $branch->name);
        $sheet->getStyle('B4')->getFont()->setBold(true);
        
        $sheet->setCellValue('D4', 'Tanggal:');
        $sheet->setCellValue('E4', date('d/m/Y'));

        // Instructions
        $sheet->mergeCells('A6:L6');
        $sheet->setCellValue('A6', '⚠️ PENTING: Jangan mengubah header kolom (baris 8). Mulai isi data dari baris 9. Kolom dengan tanda (*) wajib diisi.');
        $sheet->getStyle('A6')->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF6600'));

        // Column headers
        $headers = [
            'A' => 'No',
            'B' => 'Judul *',
            'C' => 'Penulis *',
            'D' => 'ISBN',
            'E' => 'Penerbit',
            'F' => 'Tahun',
            'G' => 'DDC *',
            'H' => 'Kategori',
            'I' => 'Bahasa',
            'J' => 'Jml Eks *',
            'K' => 'Lokasi Rak',
            'L' => 'Cover File',
        ];

        $row = 8;
        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . $row, $header);
        }

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A8:L8')->applyFromArray($headerStyle);

        // Add dropdowns and formatting for data rows
        $categories = Subject::orderBy('name')->pluck('name')->toArray();
        $categoryList = implode(',', array_slice($categories, 0, 50)); // Excel limit

        for ($i = 9; $i <= 508; $i++) {
            // Row number
            $sheet->setCellValue('A' . $i, $i - 8);
            
            // Bahasa dropdown
            $validation = $sheet->getCell('I' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setFormula1('"Indonesia,Arab,Inggris"');
            $validation->setShowDropDown(true);

            // Jumlah eksemplar default
            $sheet->setCellValue('J' . $i, 1);
        }

        // Column widths
        $widths = ['A' => 5, 'B' => 35, 'C' => 25, 'D' => 18, 'E' => 20, 'F' => 8, 'G' => 10, 'H' => 15, 'I' => 12, 'J' => 8, 'K' => 12, 'L' => 20];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Freeze header row
        $sheet->freezePane('A9');
    }

    protected function createGuideSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('📖 Panduan');

        $guide = [
            ['PANDUAN PENGISIAN TEMPLATE IMPORT KOLEKSI', ''],
            ['', ''],
            ['1. JUDUL (Wajib)', 'Tulis judul buku lengkap sesuai cover'],
            ['   Contoh:', 'Fiqih Islam Lengkap'],
            ['', ''],
            ['2. PENULIS (Wajib)', 'Jika lebih dari satu, pisahkan dengan titik koma (;)'],
            ['   Contoh:', 'Ahmad Syafi\'i; Muhammad Ridwan'],
            ['', ''],
            ['3. ISBN', 'Nomor ISBN jika ada (biasanya di halaman hak cipta)'],
            ['   Contoh:', '978-602-1234-56-7'],
            ['', ''],
            ['4. PENERBIT', 'Nama penerbit buku'],
            ['   Contoh:', 'Pustaka Imam Syafi\'i'],
            ['', ''],
            ['5. TAHUN', 'Tahun terbit (4 digit)'],
            ['   Contoh:', '2020'],
            ['', ''],
            ['6. DDC (Wajib)', 'Kode klasifikasi Dewey Decimal. Lihat sheet "Daftar DDC"'],
            ['   Contoh:', '297.4 untuk Fiqih Islam'],
            ['', ''],
            ['7. KATEGORI', 'Pilih dari dropdown atau lihat sheet "Daftar Kategori"'],
            ['', ''],
            ['8. BAHASA', 'Pilih: Indonesia, Arab, atau Inggris'],
            ['', ''],
            ['9. JUMLAH EKSEMPLAR (Wajib)', 'Jumlah copy buku yang akan diinput'],
            ['', ''],
            ['10. LOKASI RAK', 'Kode lokasi rak penyimpanan'],
            ['    Contoh:', 'A-02-15'],
            ['', ''],
            ['11. COVER FILE', 'Nama file foto cover (jika ada)'],
            ['    Cara:', ''],
            ['    a.', 'Foto cover buku dengan HP/kamera'],
            ['    b.', 'Simpan dengan nama unik, misal: buku001.jpg'],
            ['    c.', 'Tulis nama file di kolom Cover File'],
            ['    d.', 'Kumpulkan semua foto dalam 1 folder "covers"'],
            ['    e.', 'Compress folder menjadi covers.zip'],
            ['    f.', 'Upload ZIP bersama file Excel ini'],
            ['', ''],
            ['💡 TIPS:', ''],
            ['', '- Format foto: JPG atau PNG'],
            ['', '- Ukuran maksimal: 2MB per file'],
            ['', '- Jika tidak ada cover, kosongkan saja'],
        ];

        foreach ($guide as $i => $row) {
            $sheet->setCellValue('A' . ($i + 1), $row[0]);
            $sheet->setCellValue('B' . ($i + 1), $row[1]);
        }

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(50);
    }

    protected function createDdcSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('🔢 Daftar DDC');

        $sheet->setCellValue('A1', 'Kode DDC');
        $sheet->setCellValue('B1', 'Klasifikasi');
        $sheet->setCellValue('C1', 'Contoh Buku');
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);

        // Common DDC codes for Islamic library
        $commonDdc = [
            ['000', 'Ilmu Komputer & Informasi', 'Pemrograman, Internet'],
            ['100', 'Filsafat & Psikologi', 'Logika, Etika'],
            ['200', 'Agama', 'Agama Umum'],
            ['210', 'Filsafat Agama', ''],
            ['220', 'Alkitab', ''],
            ['290', 'Agama Lain', ''],
            ['297', 'Islam', 'Umum Islam'],
            ['297.1', 'Al-Quran & Ilmu Al-Quran', 'Tafsir, Ulumul Quran'],
            ['297.12', 'Tafsir Al-Quran', 'Tafsir Ibnu Katsir'],
            ['297.2', 'Hadits & Ilmu Hadits', 'Shahih Bukhari, Muslim'],
            ['297.21', 'Ilmu Hadits', 'Musthalah Hadits'],
            ['297.3', 'Aqidah & Teologi Islam', 'Tauhid'],
            ['297.4', 'Fiqih Islam', 'Fiqih Sunnah'],
            ['297.41', 'Fiqih Ibadah', 'Shalat, Puasa, Zakat'],
            ['297.42', 'Fiqih Muamalah', 'Jual Beli, Perbankan'],
            ['297.43', 'Fiqih Munakahat', 'Pernikahan'],
            ['297.5', 'Akhlak & Tasawuf', 'Ihya Ulumuddin'],
            ['297.6', 'Dakwah Islam', ''],
            ['297.7', 'Pendidikan Islam', 'Tarbiyah'],
            ['297.8', 'Aliran & Sekte Islam', ''],
            ['297.9', 'Sejarah Islam', 'Sirah Nabawiyah'],
            ['300', 'Ilmu Sosial', 'Sosiologi, Politik'],
            ['320', 'Ilmu Politik', ''],
            ['330', 'Ekonomi', 'Ekonomi Islam'],
            ['340', 'Hukum', ''],
            ['370', 'Pendidikan', ''],
            ['400', 'Bahasa', 'Linguistik'],
            ['410', 'Linguistik', ''],
            ['420', 'Bahasa Inggris', ''],
            ['490', 'Bahasa Lain', ''],
            ['492.7', 'Bahasa Arab', 'Nahwu, Shorof, Balaghah'],
            ['492.75', 'Tata Bahasa Arab', 'Nahwu Wadih, Alfiyah'],
            ['492.78', 'Kamus Arab', 'Lisanul Arab'],
            ['500', 'Sains', 'Matematika, Fisika'],
            ['600', 'Teknologi', 'Kedokteran, Pertanian'],
            ['610', 'Kedokteran', ''],
            ['630', 'Pertanian', ''],
            ['700', 'Seni & Rekreasi', 'Kaligrafi'],
            ['800', 'Sastra', 'Sastra Arab'],
            ['810', 'Sastra Amerika', ''],
            ['820', 'Sastra Inggris', ''],
            ['890', 'Sastra Lain', ''],
            ['892.7', 'Sastra Arab', 'Syair, Prosa'],
            ['900', 'Sejarah & Geografi', ''],
            ['910', 'Geografi', ''],
            ['950', 'Sejarah Asia', ''],
            ['959', 'Sejarah Asia Tenggara', ''],
        ];

        $row = 2;
        foreach ($commonDdc as $ddc) {
            $sheet->setCellValue('A' . $row, $ddc[0]);
            $sheet->setCellValue('B' . $row, $ddc[1]);
            $sheet->setCellValue('C' . $row, $ddc[2]);
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(30);
    }

    protected function createCategorySheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('📁 Daftar Kategori');

        $sheet->setCellValue('A1', 'Nama Kategori');
        $sheet->getStyle('A1')->getFont()->setBold(true);

        $categories = Subject::orderBy('name')->pluck('name');
        $row = 2;
        foreach ($categories as $cat) {
            $sheet->setCellValue('A' . $row++, $cat);
        }

        $sheet->getColumnDimension('A')->setWidth(40);
    }

    /**
     * Parse uploaded Excel and validate
     */
    public function parseAndValidate(ImportBatch $batch, UploadedFile $excel, ?UploadedFile $coversZip = null): array
    {
        // Extract covers if provided
        if ($coversZip) {
            $this->extractCovers($batch, $coversZip);
        }

        // Parse Excel
        $spreadsheet = IOFactory::load($excel->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $preview = [];
        $stats = ['total' => 0, 'valid' => 0, 'warning' => 0, 'error' => 0];

        // Find header row (contains "Judul")
        $headerRow = 8;
        $dataStartRow = 9;

        foreach ($rows as $rowNum => $row) {
            if ($rowNum < $dataStartRow) continue;
            
            // Skip empty rows
            if (empty(trim($row['B'] ?? ''))) continue;

            $stats['total']++;
            $rowData = $this->parseRow($row, $batch);
            
            if ($rowData['status'] === 'error') {
                $stats['error']++;
            } elseif ($rowData['status'] === 'warning') {
                $stats['warning']++;
            } else {
                $stats['valid']++;
            }

            $preview[] = $rowData;
        }

        // Update batch
        $batch->update([
            'total_rows' => $stats['total'],
            'success_count' => $stats['valid'],
            'warning_count' => $stats['warning'],
            'error_count' => $stats['error'],
            'preview_data' => $preview,
            'status' => 'ready',
        ]);

        return [
            'stats' => $stats,
            'preview' => $preview,
        ];
    }

    protected function parseRow(array $row, ImportBatch $batch): array
    {
        $errors = [];
        $warnings = [];

        $title = trim($row['B'] ?? '');
        $authors = trim($row['C'] ?? '');
        $isbn = trim($row['D'] ?? '');
        $publisher = trim($row['E'] ?? '');
        $year = trim($row['F'] ?? '');
        $ddc = trim($row['G'] ?? '');
        $category = trim($row['H'] ?? '');
        $language = trim($row['I'] ?? '');
        $quantity = (int) ($row['J'] ?? 1);
        $location = trim($row['K'] ?? '');
        $coverFile = trim($row['L'] ?? '');

        // Validations
        if (empty($title)) {
            $errors[] = 'Judul wajib diisi';
        }
        if (empty($authors)) {
            $errors[] = 'Penulis wajib diisi';
        }
        if ($quantity < 1) {
            $errors[] = 'Jumlah eksemplar minimal 1';
        }

        // DDC validation
        $ddcName = null;
        $callNumber = null;
        if (empty($ddc)) {
            $warnings[] = 'DDC kosong, call number tidak bisa digenerate';
        } else {
            // Generate call number
            $authorCode = $this->generateAuthorCode($authors);
            $titleCode = strtolower(substr(preg_replace('/^(the|a|an)\s+/i', '', $title), 0, 1));
            $callNumber = "{$ddc} {$authorCode} {$titleCode}";
            $ddcName = $this->getDdcName($ddc);
        }

        // Cover validation
        $coverFound = false;
        $coverPath = null;
        if (!empty($coverFile)) {
            $coverPath = $this->findCoverFile($batch, $coverFile);
            if ($coverPath) {
                $coverFound = true;
            } else {
                $warnings[] = "Cover '$coverFile' tidak ditemukan dalam ZIP";
            }
        }

        // ISBN duplicate check
        if (!empty($isbn)) {
            $existing = Book::where('isbn', $isbn)->first();
            if ($existing) {
                $warnings[] = "ISBN sudah ada: {$existing->title}";
            }
        }

        // Language mapping
        $langMap = ['indonesia' => 'id', 'arab' => 'ar', 'inggris' => 'en'];
        $langCode = $langMap[strtolower($language)] ?? 'id';

        $status = 'valid';
        if (!empty($errors)) {
            $status = 'error';
        } elseif (!empty($warnings)) {
            $status = 'warning';
        }

        return [
            'status' => $status,
            'errors' => $errors,
            'warnings' => $warnings,
            'data' => [
                'title' => $title,
                'authors' => $authors,
                'isbn' => $isbn,
                'publisher' => $publisher,
                'year' => $year ?: null,
                'ddc' => $ddc,
                'ddc_name' => $ddcName,
                'call_number' => $callNumber,
                'category' => $category,
                'language' => $langCode,
                'quantity' => $quantity,
                'location' => $location,
                'cover_file' => $coverFile,
                'cover_found' => $coverFound,
                'cover_path' => $coverPath,
            ],
        ];
    }

    protected function generateAuthorCode(string $authors): string
    {
        $firstAuthor = explode(';', $authors)[0];
        $parts = preg_split('/\s+/', trim($firstAuthor));
        $lastName = end($parts);
        return strtoupper(substr($lastName, 0, 3));
    }

    protected function getDdcName(string $code): ?string
    {
        $ddcMap = [
            '000' => 'Ilmu Komputer & Informasi',
            '100' => 'Filsafat & Psikologi',
            '200' => 'Agama',
            '297' => 'Islam',
            '297.1' => 'Al-Quran & Ilmu Al-Quran',
            '297.2' => 'Hadits & Ilmu Hadits',
            '297.3' => 'Aqidah & Teologi Islam',
            '297.4' => 'Fiqih Islam',
            '297.5' => 'Akhlak & Tasawuf',
            '300' => 'Ilmu Sosial',
            '400' => 'Bahasa',
            '492.7' => 'Bahasa Arab',
            '500' => 'Sains',
            '600' => 'Teknologi',
            '700' => 'Seni & Rekreasi',
            '800' => 'Sastra',
            '900' => 'Sejarah & Geografi',
        ];

        // Exact match
        if (isset($ddcMap[$code])) {
            return $ddcMap[$code];
        }

        // Try parent code (e.g., 297.41 -> 297.4 -> 297)
        $parts = explode('.', $code);
        if (isset($ddcMap[$parts[0]])) {
            return $ddcMap[$parts[0]];
        }

        return null;
    }

    protected function extractCovers(ImportBatch $batch, UploadedFile $zip): void
    {
        $extractPath = storage_path("app/imports/{$batch->id}/covers");
        
        if (!is_dir($extractPath)) {
            mkdir($extractPath, 0755, true);
        }

        $zipArchive = new ZipArchive();
        if ($zipArchive->open($zip->getPathname()) === true) {
            $zipArchive->extractTo($extractPath);
            $zipArchive->close();
        }

        // Index all image files
        $this->coverFiles = [];
        $this->indexCovers($extractPath);

        $batch->update(['covers_file' => $zip->getClientOriginalName()]);
    }

    protected function indexCovers(string $path): void
    {
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            
            $fullPath = $path . '/' . $file;
            if (is_dir($fullPath)) {
                $this->indexCovers($fullPath);
            } elseif (preg_match('/\.(jpg|jpeg|png|webp)$/i', $file)) {
                $this->coverFiles[strtolower($file)] = $fullPath;
            }
        }
    }

    protected function findCoverFile(ImportBatch $batch, string $filename): ?string
    {
        // Check indexed files
        $key = strtolower($filename);
        if (isset($this->coverFiles[$key])) {
            return $this->coverFiles[$key];
        }

        // Try to find in extract directory
        $basePath = storage_path("app/imports/{$batch->id}/covers");
        $possiblePaths = [
            $basePath . '/' . $filename,
            $basePath . '/covers/' . $filename,
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Execute import
     */
    public function executeImport(ImportBatch $batch, bool $includeWarnings = false): array
    {
        $batch->update(['status' => 'processing']);
        
        $preview = $batch->preview_data ?? [];
        $imported = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($preview as $index => $row) {
                if ($row['status'] === 'error') {
                    $skipped++;
                    continue;
                }

                if ($row['status'] === 'warning' && !$includeWarnings) {
                    $skipped++;
                    continue;
                }

                try {
                    $this->importBook($row['data'], $batch);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                    $skipped++;
                }
            }

            DB::commit();

            $batch->update([
                'status' => 'completed',
                'success_count' => $imported,
                'error_log' => $errors,
                'completed_at' => now(),
            ]);

            return [
                'success' => true,
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            
            $batch->update([
                'status' => 'failed',
                'error_log' => [$e->getMessage()],
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function importBook(array $data, ImportBatch $batch): Book
    {
        // Find or create publisher
        $publisher = null;
        if (!empty($data['publisher'])) {
            $publisher = Publisher::firstOrCreate(
                ['name' => $data['publisher']],
                ['slug' => Str::slug($data['publisher'])]
            );
        }

        // Find or create authors
        $authorIds = [];
        if (!empty($data['authors'])) {
            $authorNames = array_map('trim', explode(';', $data['authors']));
            foreach ($authorNames as $name) {
                $author = Author::firstOrCreate(
                    ['name' => $name],
                    ['slug' => Str::slug($name)]
                );
                $authorIds[] = $author->id;
            }
        }

        // Handle cover
        $coverPath = null;
        if (!empty($data['cover_path']) && file_exists($data['cover_path'])) {
            $ext = pathinfo($data['cover_path'], PATHINFO_EXTENSION);
            $newName = 'covers/' . Str::uuid() . '.' . $ext;
            Storage::disk('public')->put($newName, file_get_contents($data['cover_path']));
            $coverPath = $newName;
        }

        // Create book
        $book = Book::create([
            'branch_id' => $batch->branch_id,
            'title' => $data['title'],
            'isbn' => $data['isbn'] ?: null,
            'publisher_id' => $publisher?->id,
            'publish_year' => $data['year'],
            'call_number' => $data['call_number'],
            'ddc_code' => $data['ddc'],
            'language' => $data['language'],
            'cover' => $coverPath,
            'is_opac_visible' => true,
        ]);

        // Attach authors
        if (!empty($authorIds)) {
            $book->authors()->attach($authorIds);
        }

        // Attach subject/category
        if (!empty($data['category'])) {
            $subject = Subject::where('name', $data['category'])->first();
            if ($subject) {
                $book->subjects()->attach($subject->id);
            }
        }

        // Create book items (eksemplar)
        $collectionType = CollectionType::first();
        for ($i = 1; $i <= $data['quantity']; $i++) {
            BookItem::create([
                'book_id' => $book->id,
                'branch_id' => $batch->branch_id,
                'collection_type_id' => $collectionType?->id,
                'barcode' => $this->generateBarcode($book, $i),
                'shelf_location' => $data['location'],
                'status' => 'available',
            ]);
        }

        return $book;
    }

    protected function generateBarcode(Book $book, int $copy): string
    {
        return sprintf('%s-%04d-%02d', date('Ymd'), $book->id, $copy);
    }
}
