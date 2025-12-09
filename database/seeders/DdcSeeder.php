<?php

namespace Database\Seeders;

use App\Models\DdcClassification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DdcSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DdcClassification::truncate();

        // Import from ddc_db.sql file
        $sqlFile = base_path('docs/ddc_db.sql');
        
        if (!file_exists($sqlFile)) {
            $this->command->error('DDC SQL file not found at docs/ddc_db.sql');
            return;
        }

        $content = file_get_contents($sqlFile);
        
        // Match all rows like: ('<small>004', '<small><b>Description</b>...')
        // Pattern to match each row entry
        preg_match_all("/\('(<small>[^']*)',\s*'((?:[^'\\\\]|\\\\.|'')*?)'\)/s", $content, $matches, PREG_SET_ORDER);
        
        if (empty($matches)) {
            $this->command->error('No DDC data found in SQL file');
            return;
        }

        $count = 0;
        $data = [];
        
        foreach ($matches as $match) {
            $code = $match[1] ?? '';
            $description = $match[2] ?? '';
            
            // Clean up the code - remove HTML tags like <small>
            $cleanCode = strip_tags($code);
            $cleanCode = trim($cleanCode);
            
            // Clean up description - remove HTML tags and decode entities
            $cleanDesc = strip_tags($description);
            $cleanDesc = html_entity_decode($cleanDesc, ENT_QUOTES, 'UTF-8');
            $cleanDesc = preg_replace('/\s+/', ' ', $cleanDesc); // normalize whitespace
            $cleanDesc = trim($cleanDesc);
            
            if (!empty($cleanCode) && !empty($cleanDesc)) {
                $data[] = [
                    'code' => $cleanCode,
                    'description' => $cleanDesc,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $count++;
                
                // Batch insert every 100 records
                if (count($data) >= 100) {
                    DdcClassification::insert($data);
                    $data = [];
                }
            }
        }
        
        // Insert remaining records
        if (!empty($data)) {
            DdcClassification::insert($data);
        }

        $this->command->info("Imported {$count} DDC classifications");
    }
}
