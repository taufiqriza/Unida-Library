<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportEmployees extends Command
{
    protected $signature = 'employees:import 
                            {--file= : Path to Excel file}
                            {--enrich : Fetch email from e-kinerja}
                            {--type= : Import only dosen or tendik}';

    protected $description = 'Import employees from Excel and optionally enrich with e-kinerja data';

    public function handle()
    {
        $file = $this->option('file') ?: storage_path('app/imports/data-dosen-tendik-2025.xlsx');
        
        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $spreadsheet = IOFactory::load($file);
        $type = $this->option('type');

        if (!$type || $type === 'dosen') {
            $this->importDosen($spreadsheet);
        }

        if (!$type || $type === 'tendik') {
            $this->importTendik($spreadsheet);
        }

        if ($this->option('enrich')) {
            $this->enrichFromEkinerja();
        }

        $this->info('Import completed!');
        return 0;
    }

    protected function importDosen($spreadsheet): void
    {
        $this->info('Importing Dosen...');
        $sheet = $spreadsheet->getSheetByName('Tenaga Pendidik');
        
        $imported = 0;
        $skipped = 0;

        for ($row = 3; $row <= 720; $row++) {
            $niy = trim($sheet->getCell('B' . $row)->getCalculatedValue());
            $name = trim($sheet->getCell('E' . $row)->getCalculatedValue());
            $status = trim($sheet->getCell('G' . $row)->getCalculatedValue());

            if (empty($niy) || empty($name)) continue;

            // Skip non-active
            $isActive = in_array($status, ['Aktif', 'Izin Studi', 'Tugas Belajar', 'Izin Belajar']);

            $data = [
                'niy' => $niy,
                'nidn' => trim($sheet->getCell('L' . $row)->getCalculatedValue()) ?: null,
                'name' => $name,
                'front_title' => trim($sheet->getCell('C' . $row)->getCalculatedValue()) ?: null,
                'back_title' => trim($sheet->getCell('D' . $row)->getCalculatedValue()) ?: null,
                'type' => 'dosen',
                'status' => $status ?: null,
                'yayasan' => trim($sheet->getCell('H' . $row)->getCalculatedValue()) ?: null,
                'category' => trim($sheet->getCell('I' . $row)->getCalculatedValue()) ?: null,
                'position' => trim($sheet->getCell('P' . $row)->getCalculatedValue()) ?: null,
                'join_year' => $this->parseYear($sheet->getCell('Q' . $row)->getCalculatedValue()),
                'faculty' => trim($sheet->getCell('N' . $row)->getCalculatedValue()) ?: null,
                'prodi' => trim($sheet->getCell('O' . $row)->getCalculatedValue()) ?: null,
                'campus' => trim($sheet->getCell('T' . $row)->getCalculatedValue()) ?: null,
                'domicile' => trim($sheet->getCell('U' . $row)->getCalculatedValue()) ?: null,
                'education_level' => trim($sheet->getCell('AA' . $row)->getCalculatedValue()) ?: null,
                'expertise' => trim($sheet->getCell('AC' . $row)->getCalculatedValue()) ?: null,
                'serdos' => strtolower(trim($sheet->getCell('M' . $row)->getCalculatedValue())) === 'ya',
                'gender' => $this->parseGender($sheet->getCell('AN' . $row)->getCalculatedValue()),
                'is_active' => $isActive,
            ];

            // Build full name
            $data['full_name'] = trim(
                ($data['front_title'] ? $data['front_title'] . ' ' : '') .
                $data['name'] .
                ($data['back_title'] ? ', ' . $data['back_title'] : '')
            );

            try {
                Employee::updateOrCreate(['niy' => $niy], $data);
                $imported++;
            } catch (\Exception $e) {
                $this->warn("Skip row {$row}: " . $e->getMessage());
                $skipped++;
            }
        }

        $this->info("Dosen: {$imported} imported, {$skipped} skipped");
    }

    protected function importTendik($spreadsheet): void
    {
        $this->info('Importing Tendik...');
        $sheet = $spreadsheet->getSheetByName('HOME BASE TENDIK');
        
        $imported = 0;
        $skipped = 0;

        for ($row = 4; $row <= 750; $row++) {
            $name = trim($sheet->getCell('D' . $row)->getCalculatedValue());
            $satker = trim($sheet->getCell('B' . $row)->getCalculatedValue());

            if (empty($name) || $name === 'Nama') continue;

            $niy = trim($sheet->getCell('I' . $row)->getCalculatedValue()) ?: null;
            $nitk = trim($sheet->getCell('J' . $row)->getCalculatedValue()) ?: null;

            $data = [
                'niy' => $niy,
                'nitk' => $nitk,
                'name' => $this->cleanName($name),
                'type' => 'tendik',
                'status' => trim($sheet->getCell('E' . $row)->getCalculatedValue()) ?: 'Aktif',
                'category' => trim($sheet->getCell('C' . $row)->getCalculatedValue()) ?: null,
                'satker' => $satker,
                'education_level' => trim($sheet->getCell('F' . $row)->getCalculatedValue()) ?: null,
                'gender' => $this->parseGender($sheet->getCell('G' . $row)->getCalculatedValue()),
                'birth_place_date' => trim($sheet->getCell('H' . $row)->getCalculatedValue()) ?: null,
                'is_active' => true,
            ];

            // Extract title from name
            if (preg_match('/,\s*([A-Z][A-Za-z\.]+\.?)$/', $name, $m)) {
                $data['back_title'] = $m[1];
            }

            try {
                // Use niy if available, otherwise use name+satker as unique key
                if ($niy) {
                    Employee::updateOrCreate(['niy' => $niy, 'type' => 'tendik'], $data);
                } else {
                    Employee::updateOrCreate(
                        ['name' => $data['name'], 'satker' => $satker, 'type' => 'tendik'],
                        $data
                    );
                }
                $imported++;
            } catch (\Exception $e) {
                $this->warn("Skip row {$row}: " . $e->getMessage());
                $skipped++;
            }
        }

        $this->info("Tendik: {$imported} imported, {$skipped} skipped");
    }

    protected function enrichFromEkinerja(): void
    {
        $this->info('Enriching emails from e-kinerja...');
        
        $dosens = Employee::dosen()->whereNull('email')->whereNotNull('niy')->get();
        $bar = $this->output->createProgressBar($dosens->count());

        $enriched = 0;
        foreach ($dosens as $dosen) {
            try {
                $email = $this->fetchEmailFromEkinerja($dosen->niy);
                if ($email) {
                    $dosen->update(['email' => $email]);
                    $enriched++;
                }
            } catch (\Exception $e) {
                // Skip silently
            }
            
            $bar->advance();
            usleep(300000); // 300ms delay to avoid rate limit
        }

        $bar->finish();
        $this->newLine();
        $this->info("Enriched {$enriched} emails from e-kinerja");
    }

    protected function fetchEmailFromEkinerja(string $niy): ?string
    {
        $response = Http::timeout(10)->get("https://ekinerja.unida.gontor.ac.id/dosen/view", [
            'NIY' => $niy
        ]);

        if (!$response->successful()) return null;

        $html = $response->body();
        
        // Extract email
        if (preg_match('/Email.*?editable.*?>([^<]+@[^<]+)</s', $html, $m)) {
            $email = trim($m[1]);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $email;
            }
        }

        return null;
    }

    protected function parseYear($value): ?int
    {
        if (empty($value)) return null;
        $year = (int) $value;
        return ($year >= 1950 && $year <= 2030) ? $year : null;
    }

    protected function parseGender($value): ?string
    {
        $v = strtoupper(trim($value));
        if (in_array($v, ['L', 'LAKI-LAKI', 'M', 'MALE'])) return 'L';
        if (in_array($v, ['P', 'PEREMPUAN', 'F', 'FEMALE'])) return 'P';
        return null;
    }

    protected function cleanName(string $name): string
    {
        // Remove title suffix for clean name storage
        return trim(preg_replace('/,\s*[A-Z][A-Za-z\.]+\.?$/', '', $name));
    }
}
