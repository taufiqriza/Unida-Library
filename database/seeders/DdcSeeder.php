<?php

namespace Database\Seeders;

use App\Models\DdcClassification;
use Illuminate\Database\Seeder;

class DdcSeeder extends Seeder
{
    public function run(): void
    {
        DdcClassification::truncate();

        $jsonFile = database_path('data/ddc.json');
        
        if (!file_exists($jsonFile)) {
            $this->command->error('DDC JSON file not found at database/data/ddc.json');
            return;
        }

        $data = json_decode(file_get_contents($jsonFile), true);
        
        if (empty($data)) {
            $this->command->error('No DDC data found in JSON file');
            return;
        }

        $chunks = array_chunk($data, 100);
        foreach ($chunks as $chunk) {
            $records = array_map(fn($item) => [
                'code' => $item['code'],
                'description' => $item['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ], $chunk);
            DdcClassification::insert($records);
        }

        $this->command->info("Imported " . count($data) . " DDC classifications from JSON");
    }
}
