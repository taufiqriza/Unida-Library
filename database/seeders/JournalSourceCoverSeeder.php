<?php

namespace Database\Seeders;

use App\Models\JournalSource;
use Illuminate\Database\Seeder;

class JournalSourceCoverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // UNIDA Gontor Journal Covers
        // Source: https://ejournal.unida.gontor.ac.id
        $journals = [
            // SINTA 2
            'al-tijarah' => [
                'name' => 'Al-Tijarah',
                'sinta_rank' => 2,
                'issn' => '2460-4089',
                'cover_url' => 'https://ejournal.unida.gontor.ac.id/public/journals/1/journalThumbnail_id_ID.png',
            ],
            'tsaqafah' => [
                'name' => 'Tsaqafah',
                'sinta_rank' => 2,
                'issn' => '0800-1723',
                'cover_url' => 'https://ejournal.unida.gontor.ac.id/public/journals/6/journalThumbnail_id_ID.jpg',
            ],
            'al-tahrir' => [
                'name' => 'Al-Tahrir',
                'sinta_rank' => 2,
                'issn' => '2502-2253',
                'cover_url' => 'https://ejournal.unida.gontor.ac.id/public/journals/2/journalThumbnail_id_ID.png',
            ],
            
            // SINTA 3
            'islamica' => [
                'name' => 'Islamica: Jurnal Studi Keislaman',
                'sinta_rank' => 3,
                'issn' => '1978-3183',
                'cover_url' => 'https://ejournal.unida.gontor.ac.id/public/journals/3/journalThumbnail_id_ID.png',
            ],
            'ta_dibuna' => [
                'name' => "Ta'dibuna",
                'sinta_rank' => 3,
                'issn' => '2252-5793',
                'cover_url' => 'https://ejournal.unida.gontor.ac.id/public/journals/5/journalThumbnail_id_ID.jpg',
            ],
            'at_ta_dib' => [
                'name' => "At-Ta'dib",
                'sinta_rank' => 3,
                'issn' => '0216-9142',
                'cover_url' => 'https://ejournal.unida.gontor.ac.id/public/journals/4/journalThumbnail_id_ID.png',
            ],
            
            // SINTA 4
            'ulumuna' => [
                'name' => 'Ulumuna',
                'sinta_rank' => 4,
                'issn' => '2476-9533',
                'cover_url' => 'https://ejournal.unida.gontor.ac.id/public/journals/7/journalThumbnail_id_ID.png',
            ],
            'ettijahat' => [
                'name' => 'Ettijahat',
                'sinta_rank' => 4,
                'issn' => '2722-3868',
                'cover_url' => 'https://ejournal.unida.gontor.ac.id/public/journals/8/journalThumbnail_id_ID.png',
            ],
            'dauliyah' => [
                'name' => 'Dauliyah',
                'sinta_rank' => 4,
                'issn' => '2549-4023',
                'cover_url' => 'https://ejournal.unida.gontor.ac.id/public/journals/9/journalThumbnail_id_ID.png',
            ],
            'kodifikasia' => [
                'name' => 'Kodifikasia',
                'sinta_rank' => 4,
                'issn' => '1907-6371',
                'cover_url' => 'https://ejournal.unida.gontor.ac.id/public/journals/10/journalThumbnail_id_ID.png',
            ],
            
            // SINTA 5
            'hermeneutik' => [
                'name' => 'Hermeneutik',
                'sinta_rank' => 5,
                'issn' => '2549-8088',
                'cover_url' => 'https://ejournal.unida.gontor.ac.id/public/journals/11/journalThumbnail_id_ID.png',
            ],
            'tsamratul_fikri' => [
                'name' => 'Tsamratul Fikri',
                'sinta_rank' => 5,
                'issn' => '2723-5769',
                'cover_url' => 'https://ejournal.unida.gontor.ac.id/public/journals/12/journalThumbnail_id_ID.png',
            ],
        ];

        foreach ($journals as $code => $data) {
            JournalSource::where('code', $code)->update([
                'cover_url' => $data['cover_url'],
                'sinta_rank' => $data['sinta_rank'],
                'issn' => $data['issn'],
            ]);
            
            $this->command->info("Updated journal: {$data['name']}");
        }

        $this->command->info('Journal covers and SINTA ranks updated!');
    }
}
