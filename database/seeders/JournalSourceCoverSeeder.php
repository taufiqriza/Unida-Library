<?php

namespace Database\Seeders;

use App\Models\JournalSource;
use Illuminate\Database\Seeder;

class JournalSourceCoverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Source: https://ejournal.unida.gontor.ac.id
     */
    public function run(): void
    {
        // Journal cover URLs from ejournal.unida.gontor.ac.id/home_page/
        $covers = [
            // 1. Tsaqafah
            'tsaqafah' => 'https://ejournal.unida.gontor.ac.id/home_page/1.jpg',
            
            // 2. Gontor Agrotech Science Journal
            'agrotech' => 'https://ejournal.unida.gontor.ac.id/home_page/10.jpg',
            
            // 3. Lisanudhad
            'lisanu' => 'https://ejournal.unida.gontor.ac.id/home_page/2.jpg',
            
            // 4. Kalimah
            'kalimah' => 'https://ejournal.unida.gontor.ac.id/home_page/12.png',
            
            // 5. At-Ta'dib : Journal of Pesantren Education
            'tadib' => 'https://ejournal.unida.gontor.ac.id/home_page/21.png',
            
            // 6. Fountain of Informatics Journal
            'fij' => 'https://ejournal.unida.gontor.ac.id/home_page/4.jpg',
            
            // 7. ETTISAL : Journal of Communication
            'ettisal' => 'https://ejournal.unida.gontor.ac.id/home_page/20.jpg',
            
            // 8. Dauliyah : Journal of Islamic and International Affairs
            'dauliyah' => 'https://ejournal.unida.gontor.ac.id/home_page/11.jpg',
            
            // 9. Al-Tijarah
            'altijarah' => 'https://ejournal.unida.gontor.ac.id/home_page/5.jpg',
            
            // 10. Islamic Economics Journal (JIEP)
            'jiep' => 'https://ejournal.unida.gontor.ac.id/home_page/14.png',
            
            // 11. IJTIHAD : Journal of Law and Islamic Economics
            'ijtihad' => 'https://ejournal.unida.gontor.ac.id/home_page/6.jpg',
            
            // 12. Studia Quranika
            'quranika' => 'https://ejournal.unida.gontor.ac.id/home_page/21.jpg',
            
            // 13. Journal of Industrial Hygiene and Occupational Health
            'jihoh' => 'https://ejournal.unida.gontor.ac.id/home_page/13.jpg',
            
            // 14. Tasfiyah : Jurnal Pemikiran Islam
            'tasfiyah' => 'https://ejournal.unida.gontor.ac.id/home_page/7.jpg',
            
            // 15. Darussalam Nutrition Journal
            'nutrition' => 'https://ejournal.unida.gontor.ac.id/home_page/24.jpg',
            
            // 16. Pharmaceutical Journal of Islamic Pharmacy
            'pharmasipha' => 'https://ejournal.unida.gontor.ac.id/home_page/8.jpg',
            
            // 17. Al-Iktisab
            'aliktisab' => 'https://ejournal.unida.gontor.ac.id/home_page/9.jpg',
            
            // 18. Agroindustrial Technology Journal
            'atj' => 'https://ejournal.unida.gontor.ac.id/home_page/16.png',
            
            // 19. Educan : Jurnal Pendidikan Islam
            'educan' => 'https://ejournal.unida.gontor.ac.id/home_page/15.jpg',
            
            // 20. Khadimul Ummah
            'khadimulummah' => 'https://ejournal.unida.gontor.ac.id/home_page/17.jpg',
            
            // 21. Journal of Religious Comparative Studies (JRCS)
            'jrcs' => 'https://ejournal.unida.gontor.ac.id/home_page/3.png',
            
            // 22. Islamic Business and Management Journal (IBMJ)
            'ibmj' => 'https://ejournal.unida.gontor.ac.id/home_page/19.jpg',
            
            // 23. IJELAL (International Journal of English Learning and Applied Linguistics)
            'ijelal' => 'https://ejournal.unida.gontor.ac.id/home_page/ijelal.png',
            
            // 24. Journal of Comparative Study of Religions (JCSR)
            'jcsr' => 'https://ejournal.unida.gontor.ac.id/home_page/jcsr.jpg',
            
            // 25. Journal of Critical Realism in Socio-Economic (JOCRISE)
            'jocrise' => 'https://ejournal.unida.gontor.ac.id/home_page/25.png',
        ];

        $updated = 0;
        foreach ($covers as $code => $url) {
            $affected = JournalSource::where('code', $code)->update(['cover_url' => $url]);
            if ($affected) {
                $this->command->info("✓ Updated: {$code}");
                $updated += $affected;
            }
        }

        $this->command->info("Total updated: {$updated} journal sources");
    }
}
