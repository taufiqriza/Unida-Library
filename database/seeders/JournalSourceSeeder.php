<?php

namespace Database\Seeders;

use App\Models\JournalSource;
use Illuminate\Database\Seeder;

class JournalSourceSeeder extends Seeder
{
    public function run(): void
    {
        // [code, name, sinta_rank, issn]
        $journals = [
            // SINTA 2
            ['tsaqafah', 'TSAQAFAH', '2', '2460-4909'],
            ['tadib', 'At-Ta\'dib', '2', '2503-3514'],
            ['ettisal', 'ETTISAL', '2', '2614-2716'],
            
            // SINTA 3
            ['kalimah', 'Kalimah', '3', '2503-2364'],
            ['lisanu', 'Lisanudhad', '3', '2503-2518'],
            ['agrotech', 'Gontor Agrotech Science Journal', '3', '2549-4341'],
            ['ijtihad', 'Ijtihad', '3', '2502-8375'],
            
            // SINTA 4
            ['JEI', 'Journal of English and Islam', '4', null],
            ['JIEP', 'JIEP', '4', null],
            ['quranika', 'Quranika', '4', null],
            ['tasfiyah', 'Tasfiyah', '4', null],
            ['dauliyah', 'Dauliyah', '4', null],
            ['aliktisab', 'Al-Iktisab', '4', null],
            
            // Other Journals
            ['JCSR', 'JCSR', null, null],
            ['INJAS', 'INJAS', null, null],
            ['pharmasipha', 'Pharmasipha', null, null],
            ['nutrition', 'Nutrition Journal', null, null],
            ['trimurti', 'TRIMURTI', null, null],
            ['muamalat', 'Al-Muamalat', null, null],
            ['sahafa', 'Sahafa', null, null],
            ['mediasi', 'Mediasi', null, null],
            ['shibghoh', 'Shibghoh', null, null],
            ['educan', 'Educan', null, null],
            ['Afkar', 'Afkar', null, null],
            ['IBMJ', 'IBMJ', null, null],
            ['jicl', 'JICL', null, null],
            ['JIHOH', 'JIHOH', null, null],
            ['JRCS', 'JRCS', null, null],
            ['sosma', 'Sosma', null, null],
            ['SYARIAH', 'Syariah', null, null],
            ['altijarah', 'Al-Tijarah', null, null],
            ['khadimulummah', 'Khadimul Ummah', null, null],
            ['IJELAL', 'IJELAL', null, null],
            ['FIJ', 'FIJ', null, null],
            ['atj', 'ATJ', null, null],
        ];

        $baseUrl = 'https://ejournal.unida.gontor.ac.id/index.php';

        foreach ($journals as [$code, $name, $sinta, $issn]) {
            JournalSource::updateOrCreate(
                ['code' => strtolower($code)],
                [
                    'name' => $name,
                    'sinta_rank' => $sinta,
                    'issn' => $issn,
                    'base_url' => "{$baseUrl}/{$code}",
                    'feed_type' => 'atom',
                    'feed_url' => "{$baseUrl}/{$code}/gateway/plugin/WebFeedGatewayPlugin/atom",
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Registered ' . count($journals) . ' journal sources.');
    }
}
