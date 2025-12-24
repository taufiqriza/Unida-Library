<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\PddiktiData;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportStudentsCommand extends Command
{
    protected $signature = 'import:students 
                            {file? : Path to Excel file} 
                            {--dry-run : Preview without saving}
                            {--limit= : Limit records per sheet}';

    protected $description = 'Import student data from UNIDA Excel file to pddikti_data table';

    // Branch mapping: Kelas code => Branch code in database
    protected array $branchMapping = [
        'A' => 'PUSAT',         // Siman = PUSAT
        'B' => 'ROBITHOH',      // Gontor = ROBITHOH
        'CG' => 'GP1-UNIDA',    // Mantingan Guru = GP1/GP3
        'CR' => 'UGP',          // Mantingan Reguler = UGP
        'D' => 'G3-UNIDA',      // Kediri = G3
        'E' => 'GP4-UNIDA',     // Kandangan = GP4
        'F' => 'G5-UNIDA',      // Magelang = G5
        'G' => 'G6-UNIDA',      // Banyuwangi = G6
        'H' => 'PUSAT',         // Default to PUSAT (needs clarification)
        'L' => 'PUSAT',         // Default to PUSAT (needs clarification)
    ];

    // Department mapping: Sheet name => [code, full_name]
    protected array $departmentMapping = [
        'PAI' => ['PAI', 'Pendidikan Agama Islam'],
        'PBA' => ['PBA', 'Pendidikan Bahasa Arab'],
        'TBI' => ['PBI', 'Pendidikan Bahasa Inggris'],
        'SAA' => ['SAA', 'Studi Agama-Agama'],
        'AFI' => ['AFI', 'Aqidah dan Filsafat Islam'],
        'IQT' => ['IQT', 'Ilmu Al-Quran dan Tafsir'],
        'PM' => ['PM', 'Perbandingan Madzhab'],
        'HES' => ['HES', 'Hukum Ekonomi Syariah'],
        'AGRO' => ['AGRO', 'Agroteknologi'],
        'TI' => ['TI', 'Teknik Informatika'],
        'TIP' => ['TIP', 'Teknologi Industri Pertanian'],
        'HI' => ['HI', 'Hubungan Internasional'],
        'ILKOM' => ['ILKOM', 'Ilmu Komunikasi'],
        'MNJ' => ['MNJ', 'Manajemen'],
        'GIZI' => ['GIZI', 'Gizi'],
        'FARMASI' => ['FARMASI', 'Farmasi'],
        'K3' => ['K3', 'Kesehatan dan Keselamatan Kerja'],
        'EI' => ['EI', 'Ekonomi Islam'],
        'KEDOKTERAN' => ['KEDOKTERAN', 'Kedokteran'],
        'APOTEKER' => ['APOTEKER', 'Profesi Apoteker'],
    ];

    protected array $branchCache = [];
    protected array $departmentCache = [];

    public function handle(): int
    {
        $file = $this->argument('file') ?? storage_path('app/imports/DataMhs-UNIDA.xlsx');
        $dryRun = $this->option('dry-run');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $this->info("Loading Excel file: {$file}");
        
        if ($dryRun) {
            $this->warn("DRY RUN MODE - No data will be saved");
        }

        // Ensure departments exist
        $this->ensureDepartmentsExist($dryRun);

        // Load branch cache
        $this->loadBranchCache();

        try {
            $spreadsheet = IOFactory::load($file);
            $sheetNames = $spreadsheet->getSheetNames();
            
            $this->info("Found " . count($sheetNames) . " sheets to process");

            $totalImported = 0;
            $totalSkipped = 0;

            foreach ($sheetNames as $sheetIndex => $sheetName) {
                $this->info("\n[" . ($sheetIndex + 1) . "/" . count($sheetNames) . "] Processing sheet: {$sheetName}");
                
                $sheet = $spreadsheet->getSheet($sheetIndex);
                $data = $sheet->toArray(null, true, true, true);
                
                // Get headers from first row
                $headers = array_shift($data);
                if (!$headers) {
                    $this->warn("  Empty sheet, skipping");
                    continue;
                }

                // Map headers
                $headerMap = $this->mapHeaders($headers);
                
                $sheetImported = 0;
                $sheetSkipped = 0;
                
                $bar = $this->output->createProgressBar(count($data));
                $bar->start();

                foreach ($data as $rowIndex => $row) {
                    if ($limit && $sheetImported >= $limit) {
                        break;
                    }

                    $bar->advance();

                    // Skip empty rows
                    if (empty($row[$headerMap['NIM']])) {
                        $sheetSkipped++;
                        continue;
                    }

                    $nim = trim($row[$headerMap['NIM']] ?? '');
                    $name = trim($row[$headerMap['Nama Mahasiswa']] ?? '');
                    $kelas = trim($row[$headerMap['Kelas']] ?? 'A');
                    $prodi = trim($row[$headerMap['Prodi']] ?? '');
                    $angkatan = trim($row[$headerMap['Angkatan']] ?? '');
                    $status = trim($row[$headerMap['Status Aktif']] ?? 'AKTIF');
                    $gender = trim($row[$headerMap['Jenis Kelamin']] ?? '');
                    $birthPlace = trim($row[$headerMap['Tempat Lahir']] ?? '');
                    $birthDate = $row[$headerMap['Tgl Lahir']] ?? null;

                    if (empty($nim) || empty($name)) {
                        $sheetSkipped++;
                        continue;
                    }

                    // Get branch ID from Kelas
                    $branchCode = $this->branchMapping[$kelas] ?? 'PUSAT';
                    $branchId = $this->branchCache[$branchCode] ?? $this->branchCache['PUSAT'] ?? null;

                    // Get department from sheet name
                    $deptInfo = $this->departmentMapping[$sheetName] ?? null;
                    $departmentId = $deptInfo ? ($this->departmentCache[$deptInfo[0]] ?? null) : null;

                    if (!$dryRun) {
                        PddiktiData::updateOrCreate(
                            ['nim_nidn' => $nim],
                            [
                                'pddikti_id' => 'UNIDA-' . $nim,
                                'type' => 'mahasiswa',
                                'name' => $name,
                                'prodi' => $prodi ?: ($deptInfo[1] ?? $sheetName),
                                'status' => $status,
                                'angkatan' => substr((string)$angkatan, 0, 4),
                                'pt_name' => 'Universitas Darussalam Gontor',
                                'synced_at' => now(),
                            ]
                        );
                    }

                    $sheetImported++;
                }

                $bar->finish();
                $this->newLine();
                $this->info("  Imported: {$sheetImported}, Skipped: {$sheetSkipped}");

                $totalImported += $sheetImported;
                $totalSkipped += $sheetSkipped;
            }

            $this->newLine();
            $this->info("=== IMPORT COMPLETE ===");
            $this->info("Total Imported: {$totalImported}");
            $this->info("Total Skipped: {$totalSkipped}");

            if ($dryRun) {
                $this->warn("DRY RUN - No data was actually saved");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }

    protected function mapHeaders(array $headers): array
    {
        $map = [];
        foreach ($headers as $col => $header) {
            $map[trim($header)] = $col;
        }
        return $map;
    }

    protected function loadBranchCache(): void
    {
        $branches = Branch::all();
        foreach ($branches as $branch) {
            $this->branchCache[$branch->code] = $branch->id;
        }
        $this->info("Loaded " . count($this->branchCache) . " branches");
    }

    protected function ensureDepartmentsExist(bool $dryRun): void
    {
        $this->info("Checking departments...");
        
        // Get default faculty (create if not exists)
        $faculty = Faculty::firstOrCreate(
            ['code' => 'UNIDA'],
            ['name' => 'UNIDA Gontor', 'code' => 'UNIDA']
        );

        $created = 0;
        foreach ($this->departmentMapping as $sheetName => [$code, $fullName]) {
            $existing = Department::where('code', $code)->first();
            
            if (!$existing) {
                if (!$dryRun) {
                    $dept = Department::create([
                        'faculty_id' => $faculty->id,
                        'name' => $fullName,
                        'code' => $code,
                    ]);
                    $this->departmentCache[$code] = $dept->id;
                    $created++;
                } else {
                    $this->line("  Would create: {$code} - {$fullName}");
                }
            } else {
                $this->departmentCache[$code] = $existing->id;
            }
        }

        if ($created > 0) {
            $this->info("Created {$created} new departments");
        }
    }
}
