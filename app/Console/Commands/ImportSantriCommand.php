<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Member;
use App\Models\MemberType;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class ImportSantriCommand extends Command
{
    protected $signature = 'import:santri 
                            {file? : Path to Excel file} 
                            {--branch= : Branch ID (default: OPPM GP1 = 18)}
                            {--dry-run : Preview without saving}';

    protected $description = 'Import santri data from OPPM Excel file';

    public function handle(): int
    {
        $file = $this->argument('file') ?? storage_path('app/imports/data-santri-oppm-gp1.xlsx');
        $branchId = $this->option('branch') ?? 18;
        $dryRun = $this->option('dry-run');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $branch = Branch::find($branchId);
        if (!$branch) {
            $this->error("Branch ID {$branchId} not found");
            return 1;
        }

        $santriType = MemberType::where('name', 'Santri')->first();
        if (!$santriType) {
            $this->error("MemberType 'Santri' not found");
            return 1;
        }

        $this->info("Importing santri to: {$branch->name}");
        $this->info("File: {$file}");
        if ($dryRun) $this->warn("DRY RUN MODE");

        try {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, true, true, true);
            
            $headers = array_shift($data);
            $headerMap = [];
            foreach ($headers as $col => $header) {
                $headerMap[trim($header ?? '')] = $col;
            }

            $imported = 0;
            $skipped = 0;
            $bar = $this->output->createProgressBar(count($data));

            foreach ($data as $row) {
                $bar->advance();

                $stambuk = trim($row[$headerMap['Stambuk']] ?? '');
                $name = trim($row[$headerMap['Nama Lengkap']] ?? '');
                
                if (empty($stambuk) || empty($name)) {
                    $skipped++;
                    continue;
                }

                $birthDate = $this->parseBirthDate($row[$headerMap['Tanggal Lahir']] ?? '');
                $address = trim($row[$headerMap['Alamat']] ?? '');
                $kelas = trim($row[$headerMap['Kelas']] ?? '');
                $rayon = trim($row[$headerMap['Rayon']] ?? '');
                $kamar = trim($row[$headerMap['Kamar Rayon']] ?? '');

                // Build notes from kelas, rayon, kamar
                $notes = collect([$kelas, $rayon, $kamar ? "Kamar {$kamar}" : null])
                    ->filter()->implode(' | ');

                if (!$dryRun) {
                    Member::updateOrCreate(
                        ['member_id' => $stambuk, 'branch_id' => $branchId],
                        [
                            'name' => $name,
                            'branch_id' => $branchId,
                            'member_type_id' => $santriType->id,
                            'birth_date' => $birthDate,
                            'address' => $address,
                            'notes' => $notes,
                            'register_date' => now(),
                            'expire_date' => now()->addYears(3),
                            'is_active' => true,
                        ]
                    );
                }
                $imported++;
            }

            $bar->finish();
            $this->newLine(2);
            $this->info("Imported: {$imported}, Skipped: {$skipped}");
            
            if ($dryRun) $this->warn("DRY RUN - No data saved");

            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }

    protected function parseBirthDate(string $date): ?string
    {
        if (empty($date)) return null;
        
        $months = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12
        ];

        // Format: "10 Oktober 2009"
        if (preg_match('/(\d+)\s+(\w+)\s+(\d{4})/', $date, $m)) {
            $month = $months[strtolower($m[2])] ?? null;
            if ($month) {
                return Carbon::create($m[3], $month, $m[1])->format('Y-m-d');
            }
        }
        
        return null;
    }
}
