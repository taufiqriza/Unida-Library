<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Member;
use App\Models\MemberType;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportMembersCommand extends Command
{
    protected $signature = 'import:members 
                            {file? : Path to Excel file} 
                            {--dry-run : Preview without saving}
                            {--limit= : Limit records per sheet}';

    protected $description = 'Import student data from UNIDA Excel file as library members';

    // Branch mapping: Kelas code => Branch code in database
    protected array $branchMapping = [
        'A' => 'PUSAT',
        'B' => 'ROBITHOH',
        'CG' => 'GP1-UNIDA',
        'CR' => 'UGP',
        'D' => 'G3-UNIDA',
        'E' => 'GP4-UNIDA',
        'F' => 'G5-UNIDA',
        'G' => 'G6-UNIDA',
        'H' => 'PUSAT',
        'L' => 'PUSAT',
    ];

    // Department mapping: Sheet name => department code
    protected array $departmentMapping = [
        'PAI' => 'PAI',
        'PBA' => 'PBA',
        'TBI' => 'PBI',
        'SAA' => 'SAA',
        'AFI' => 'AFI',
        'IQT' => 'IQT',
        'PM' => 'PM',
        'HES' => 'HES',
        'AGRO' => 'AGRO',
        'TI' => 'TI',
        'TIP' => 'TIP',
        'HI' => 'HI',
        'ILKOM' => 'ILKOM',
        'MNJ' => 'MNJ',
        'GIZI' => 'GIZI',
        'FARMASI' => 'FARMASI',
        'K3' => 'K3',
        'EI' => 'EI',
        'KEDOKTERAN' => 'KEDOKTERAN',
        'APOTEKER' => 'APOTEKER',
    ];

    protected array $branchCache = [];
    protected array $departmentCache = [];
    protected ?int $studentMemberTypeId = null;
    protected ?int $defaultFacultyId = null;

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

        // Setup caches
        $this->loadBranchCache();
        $this->loadDepartmentCache();
        $this->getStudentMemberType();

        try {
            $spreadsheet = IOFactory::load($file);
            $sheetNames = $spreadsheet->getSheetNames();
            
            $this->info("Found " . count($sheetNames) . " sheets to process");

            $totalImported = 0;
            $totalSkipped = 0;

            foreach ($sheetNames as $sheetIndex => $sheetName) {
                $this->info("\n[" . ($sheetIndex + 1) . "/" . count($sheetNames) . "] Processing: {$sheetName}");
                
                $sheet = $spreadsheet->getSheet($sheetIndex);
                $data = $sheet->toArray(null, true, true, true);
                
                $headers = array_shift($data);
                if (!$headers) {
                    $this->warn("  Empty sheet, skipping");
                    continue;
                }

                $headerMap = $this->mapHeaders($headers);
                
                $sheetImported = 0;
                $sheetSkipped = 0;
                
                $bar = $this->output->createProgressBar(count($data));
                $bar->start();

                foreach ($data as $row) {
                    if ($limit && $sheetImported >= $limit) {
                        break;
                    }

                    $bar->advance();

                    $nim = trim($row[$headerMap['NIM']] ?? '');
                    $name = trim($row[$headerMap['Nama Mahasiswa']] ?? '');
                    $kelas = trim($row[$headerMap['Kelas']] ?? 'A');
                    $angkatan = substr((string)trim($row[$headerMap['Angkatan']] ?? ''), 0, 4);
                    $gender = strtoupper(trim($row[$headerMap['Jenis Kelamin']] ?? ''));

                    if (empty($nim) || empty($name)) {
                        $sheetSkipped++;
                        continue;
                    }

                    // Get branch ID
                    $branchCode = $this->branchMapping[$kelas] ?? 'PUSAT';
                    $branchId = $this->branchCache[$branchCode] ?? $this->branchCache['PUSAT'] ?? 1;

                    // Get department ID
                    $deptCode = $this->departmentMapping[$sheetName] ?? null;
                    $departmentId = $deptCode ? ($this->departmentCache[$deptCode] ?? null) : null;

                    if (!$dryRun) {
                        Member::updateOrCreate(
                            ['member_id' => $nim],
                            [
                                'name' => $name,
                                'nim_nidn' => $nim,
                                'branch_id' => $branchId,
                                'department_id' => $departmentId,
                                'faculty_id' => $this->defaultFacultyId,
                                'member_type_id' => $this->studentMemberTypeId,
                                'gender' => $gender === 'L' ? 'M' : ($gender === 'P' ? 'F' : null),
                                'register_date' => now(),
                                'expire_date' => now()->addYears(4),
                                'is_active' => true,
                                'profile_completed' => false,
                                'registration_type' => 'internal',
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

    protected function loadDepartmentCache(): void
    {
        $departments = Department::all();
        foreach ($departments as $dept) {
            $this->departmentCache[$dept->code] = $dept->id;
        }
        
        // Get or create default faculty
        $faculty = Faculty::firstOrCreate(
            ['code' => 'UNIDA'],
            ['name' => 'UNIDA Gontor']
        );
        $this->defaultFacultyId = $faculty->id;
        
        $this->info("Loaded " . count($this->departmentCache) . " departments");
    }

    protected function getStudentMemberType(): void
    {
        $memberType = MemberType::where('name', 'like', '%mahasiswa%')
            ->orWhere('name', 'like', '%student%')
            ->first();
        
        if (!$memberType) {
            $memberType = MemberType::firstOrCreate(
                ['name' => 'Mahasiswa'],
                ['name' => 'Mahasiswa', 'max_loan_days' => 14, 'max_loan_items' => 3]
            );
        }
        
        $this->studentMemberTypeId = $memberType->id;
        $this->info("Using member type: {$memberType->name} (ID: {$memberType->id})");
    }
}
