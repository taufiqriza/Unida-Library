<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WordPressNewsImportSeeder extends Seeder
{
    /**
     * Import news from WordPress library.digilib-unida.id
     * Data extracted from: https://library.digilib-unida.id/wp-json/wp/v2/posts
     */
    public function run(): void
    {
        // First, create/get categories
        $news = NewsCategory::firstOrCreate(
            ['slug' => 'news'],
            ['name' => 'News', 'sort_order' => 1]
        );
        
        $artikel = NewsCategory::firstOrCreate(
            ['slug' => 'artikel'],
            ['name' => 'Artikel', 'sort_order' => 2]
        );
        
        $seminar = NewsCategory::firstOrCreate(
            ['slug' => 'seminar'],
            ['name' => 'Seminar', 'sort_order' => 3]
        );

        // WordPress Posts Data - Extracted from API
        $posts = [
            [
                'title' => 'Audit Mutu Internal 2025: Perpustakaan UNIDA Gontor Perkuat Standar Kualitas Terbaik',
                'slug' => 'ami-2025',
                'category' => $news->id,
                'published_at' => '2025-01-14 19:59:02',
                'featured_image_url' => 'https://library.digilib-unida.id/wp-content/uploads/2025/01/AMI2025-1-MAIN.png',
                'excerpt' => 'Pada hari Selasa, 14 Januari 2025, telah dilaksanakan Audit Mutu Internal di Perpustakaan Universitas Darussalam Gontor. Audit ini bertujuan untuk menilai kinerja perpustakaan sesuai dengan standar yang telah ditetapkan.',
                'content' => '<p>Pada hari Selasa, 14 Januari 2025, telah dilaksanakan Audit Mutu Internal di Perpustakaan Universitas Darussalam Gontor. Audit ini bertujuan untuk menilai kinerja perpustakaan sesuai dengan standar yang telah ditetapkan, serta mempersiapkan perpustakaan untuk proses akreditasi perpustakaan perguruan tinggi. Audit ini dilaksanakan di Ruang Rapat Perpustakaan Universitas Darussalam Gontor dengan kehadiran tim auditor yang kompeten.</p>
<p>Adapun yang bertindak sebagai Auditee dalam audit ini adalah Kepala Perpustakaan Syamsul Hadi Untung, M.A., M.LS. Sementara itu, tim auditor dipimpin oleh Dr. Yuangga Kurnia Yahya, S.Th.I., M.A., dengan anggota Rindang Diannita, S.K.M., M.Kes., dan Dhika Amalia Kurniawan, S.E., M.M. Seluruh proses audit berlangsung dengan lancar, berkat kerjasama yang baik antara tim auditor dan pihak perpustakaan.</p>
<p>Audit Mutu Internal ini meliputi berbagai standar, termasuk PPEPP (Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan), yang merupakan siklus penting dalam Sistem Penjaminan Mutu Internal (SPMI). Proses ini bertujuan untuk memastikan bahwa perpustakaan telah melaksanakan setiap tahapan dengan baik, serta mampu melakukan perbaikan berkelanjutan untuk mencapai standar mutu yang diharapkan.</p>
<p>Selain PPEPP, audit ini juga menyoroti berbagai aspek yang berkaitan dengan Tagihan Akreditasi Perpustakaan Perguruan Tinggi. Hal ini mencakup penilaian terhadap fasilitas, koleksi, layanan, serta manajemen perpustakaan. Evaluasi yang dilakukan memberikan gambaran komprehensif mengenai sejauh mana perpustakaan telah memenuhi persyaratan akreditasi dan area yang memerlukan peningkatan.</p>
<p>Hasil audit menunjukkan bahwa meskipun banyak aspek yang telah berjalan dengan baik, masih terdapat beberapa area yang perlu dibenahi dan ditingkatkan oleh Perpustakaan Universitas Darussalam Gontor. Rekomendasi yang diberikan oleh tim auditor diharapkan dapat menjadi panduan untuk langkah-langkah perbaikan di masa mendatang, demi tercapainya standar mutu yang lebih tinggi dan kesiapan menghadapi akreditasi.</p>
<p><em>Creator & Editor: Muhamad Taufiq Riza</em></p>',
            ],
            [
                'title' => 'Jejak Sanad Gontor-Mekah: Menelusuri Tradisi Ilmu dan Peradaban dari Jalur Josari',
                'slug' => 'jejak-sanad-gontor-mekah',
                'category' => $artikel->id,
                'published_at' => '2024-12-21 17:33:43',
                'featured_image_url' => 'https://library.digilib-unida.id/wp-content/uploads/2024/12/SANAD-GONTOR.png',
                'excerpt' => 'Pesantren Josari Jetis Ponorogo, tidak bisa dipisahkan dari perjalanan keilmuan para trimurti pendiri Gontor. Semua trimurti Gontor, mulai dari Kyai Ahmad Sahal, Kyai Zainudi Fanani, Kyai Imam Zarkasyi pernah mukim nyantri di Josari.',
                'content' => '<p>Pesantren Josari Jetis Ponorogo, tidak bisa dipisahkan dari perjalanan keilmuan para trimurti pendiri Gontor. Semua trimurti Gontor, mulai dari Kyai Ahmad Sahal, Kyai Zainudi Fanani, Kyai Imam Zarkasyi pernah mukim nyantri di Josari. Selama di Josari, para trimurti menempuh dua model pendidikan sekaligus, formal dan pesantren.</p>
<p>Usaha ini digawangi oleh ibunda Trimurti Nyai Santoso Anom, sebagai bentuk tanggung jawab dan langkah taktis menyiapkan ketiga putranya melanjutkan estafet pesantren Gontor yang "mati suri" sepeninggal sang suami Kyai R. Santoso Anom Besari.</p>
<p>Di Josari, trimurti dibekali pengetahuan agama level dasar dan menengah, dari tauhid, fiqih Syafi\'iyyah, hadist, gramatika arab, akhlak, siroh al-barzanji dan tasawuf dasar. Namun, dari sekian fan ilmu, nampaknya ilmu tauhid dan ilmu fiqih yang paling kuat pengaruhnya dalam diri trimurti.</p>
<p>Rantai sanad keilmuan Josari bisa terhubung dengan pesantren-pesantren besar di Jawa bahkan di Mekah melalui perantara al-Alim al-Allamah al-Arif Billah Kyai Muhammad Mansur (1831-1943). Kyai Mansur merupakan generasi ke-7, yang memimpin pesantren dan masjid jami\' Josari tahun 1896-1943.</p>
<p><em>Creator & Editor: Muhamad Taufiq Riza</em></p>',
            ],
            [
                'title' => 'Peningkatan Layanan Digital Library UNIDA Gontor Berkat Kerjasama dengan PT. PNM Madiun',
                'slug' => 'digital-library-pnm',
                'category' => $news->id,
                'published_at' => '2024-12-21 09:55:55',
                'featured_image_url' => 'https://library.digilib-unida.id/wp-content/uploads/2024/12/FOTO-UTAMA.png',
                'excerpt' => 'Pada Kamis, 19 Desember 2024, Universitas Darussalam Gontor (UNIDA Gontor) menyelenggarakan acara yang menandai dimulainya peningkatan layanan perpustakaan digital melalui kerjasama dengan PT. Permodalan Nasional Madani (PNM) Cabang Madiun.',
                'content' => '<p>Pada Kamis, 19 Desember 2024, Universitas Darussalam Gontor (UNIDA Gontor) menyelenggarakan acara yang menandai dimulainya peningkatan layanan perpustakaan digital melalui kerjasama dengan PT. Permodalan Nasional Madani (PNM) Cabang Madiun. Acara ini dihadiri oleh Kepala Perpustakaan UNIDA Gontor Ustadz Syamsul Hadi Untung, M.A., MLS, dan Kepala PT. PNM Madiun, serta staf Perpustakaan UNIDA Gontor.</p>
<p>Dalam rangka mendukung Program Tanggung Jawab Sosial dan Lingkungan (TJSL), PT. PNM Madiun memberikan bantuan berupa perangkat komputer dan printer untuk memperkuat layanan perpustakaan digital UNIDA Gontor. Barang yang diberikan meliputi 1 Unit PC AIO (All-In-One) Asus yang akan digunakan untuk mengakses berbagai sumber daya digital, serta 1 Unit Printer Epson yang mendukung kebutuhan cetak dokumen.</p>
<p>Ustadz Syamsul Hadi Untung, M.A., MLS, dalam sambutannya mengungkapkan pentingnya pemanfaatan teknologi dalam dunia pendidikan. "Digitalisasi perpustakaan adalah langkah yang sangat penting dalam mendukung transformasi pendidikan yang lebih modern dan efektif," ujarnya.</p>
<p><em>Creator & Editor: Muhamad Taufiq Riza</em></p>',
            ],
            [
                'title' => 'Meetup SLiMS & Seminar Literasi: Membangun Critical Minds Melalui Literatur Perpustakaan',
                'slug' => 'seminar-critical-minds',
                'category' => $seminar->id,
                'published_at' => '2024-12-06 12:38:00',
                'featured_image_url' => 'https://library.digilib-unida.id/wp-content/uploads/2025/01/MEETUP-UTAMA.png',
                'excerpt' => 'Pada Jumat, 6 Desember 2024, Perpustakaan UNIDA Gontor dengan bangga mempersembahkan acara "Meetup SLiMS & Seminar Literasi: Membangun Critical Minds Melalui Literatur Perpustakaan."',
                'content' => '<p>Pada Jumat, 6 Desember 2024, Perpustakaan UNIDA Gontor dengan bangga mempersembahkan acara "Meetup SLiMS & Seminar Literasi: Membangun Critical Minds Melalui Literatur Perpustakaan." Acara ini dirancang untuk memperkuat kemampuan berpikir kritis dan analitis peserta dengan memanfaatkan literatur yang tersedia di perpustakaan.</p>
<p>Seminar literasi ini akan dibawakan oleh Ibu Ayu Wulansari, A.MD, S.Kom, M.A., dosen Universitas Muhammadiyah Ponorogo sekaligus Kepala Perpustakaan di institusi tersebut. Dalam sesi ini, Ibu Ayu akan membagikan wawasan tentang pentingnya literasi dalam membangun kemampuan berpikir kritis serta cara-cara efektif memanfaatkan berbagai jenis literatur yang tersedia di perpustakaan.</p>
<p>Acara ini akan dibuka oleh Kepala Perpustakaan UNIDA Gontor, Ustadz Syamsul Hadi Untung, M.A, MLS. Dalam sambutannya, Ustadz Syamsul akan menyampaikan pentingnya perpustakaan sebagai pusat literasi dan inovasi.</p>
<p>Setelah sesi seminar, acara akan dilanjutkan dengan Meetup SLiMS (Senayan Library Management System), sebuah sistem manajemen perpustakaan yang telah diimplementasikan di UNIDA Gontor.</p>
<p><em>Creator & Editor: Muhamad Taufiq Riza</em></p>',
            ],
            [
                'title' => 'Sosialisasi Literasi Perpustakaan: Orientasi Mahasiswa Baru UNIDA Gontor 2024',
                'slug' => 'sosialisasi-literasi-2024',
                'category' => $news->id,
                'published_at' => '2024-09-15 10:00:00',
                'featured_image_url' => null,
                'excerpt' => 'Perpustakaan UNIDA Gontor menyelenggarakan sosialisasi literasi perpustakaan kepada seluruh mahasiswa baru tahun akademik 2024/2025. Kegiatan ini merupakan bagian dari program orientasi untuk memperkenalkan fasilitas dan layanan perpustakaan.',
                'content' => '<p>Perpustakaan UNIDA Gontor menyelenggarakan sosialisasi literasi perpustakaan kepada seluruh mahasiswa baru tahun akademik 2024/2025. Kegiatan ini merupakan bagian dari program orientasi untuk memperkenalkan fasilitas dan layanan perpustakaan.</p>
<p>Dalam sosialisasi ini, mahasiswa baru diperkenalkan dengan berbagai layanan perpustakaan meliputi:</p>
<ul>
<li>Cara mendaftar dan mengaktifkan kartu anggota perpustakaan</li>
<li>Penggunaan OPAC (Online Public Access Catalog)</li>
<li>Akses e-resources dan database jurnal</li>
<li>Layanan peminjaman dan pengembalian buku</li>
<li>Fasilitas ruang baca dan diskusi</li>
</ul>
<p>Kepala Perpustakaan, Ustadz Syamsul Hadi Untung, M.A., MLS, menekankan pentingnya literasi informasi bagi mahasiswa dalam menunjang keberhasilan studi mereka.</p>
<p><em>Creator & Editor: Muhamad Taufiq Riza</em></p>',
            ],
            [
                'title' => 'Perpustakaan UNIDA Gontor Raih Akreditasi A dari Perpustakaan Nasional',
                'slug' => 'akreditasi-a-perpusnas',
                'category' => $news->id,
                'published_at' => '2024-08-20 14:30:00',
                'featured_image_url' => null,
                'excerpt' => 'Perpustakaan Universitas Darussalam Gontor berhasil meraih akreditasi A dari Perpustakaan Nasional Republik Indonesia. Pencapaian ini merupakan hasil kerja keras seluruh tim perpustakaan dalam meningkatkan kualitas layanan.',
                'content' => '<p>Perpustakaan Universitas Darussalam Gontor berhasil meraih akreditasi A dari Perpustakaan Nasional Republik Indonesia. Pencapaian ini merupakan hasil kerja keras seluruh tim perpustakaan dalam meningkatkan kualitas layanan dan pengembangan koleksi.</p>
<p>Proses akreditasi meliputi penilaian terhadap berbagai aspek:</p>
<ul>
<li>Koleksi perpustakaan</li>
<li>Sarana dan prasarana</li>
<li>Layanan perpustakaan</li>
<li>Tenaga perpustakaan</li>
<li>Pengelolaan perpustakaan</li>
<li>Penguatan perpustakaan</li>
</ul>
<p>Rektor UNIDA Gontor mengapresiasi pencapaian ini dan mendorong perpustakaan untuk terus berinovasi dalam memberikan layanan terbaik bagi sivitas akademika.</p>
<p><em>Creator & Editor: Tim Perpustakaan UNIDA Gontor</em></p>',
            ],
            [
                'title' => 'Workshop Penulisan Karya Ilmiah dan Manajemen Referensi',
                'slug' => 'workshop-manajemen-referensi',
                'category' => $seminar->id,
                'published_at' => '2024-07-10 09:00:00',
                'featured_image_url' => null,
                'excerpt' => 'Perpustakaan UNIDA Gontor mengadakan workshop penulisan karya ilmiah dan manajemen referensi menggunakan aplikasi Mendeley. Workshop ini ditujukan bagi mahasiswa tingkat akhir dan dosen.',
                'content' => '<p>Perpustakaan UNIDA Gontor mengadakan workshop penulisan karya ilmiah dan manajemen referensi menggunakan aplikasi Mendeley. Workshop ini ditujukan bagi mahasiswa tingkat akhir dan dosen yang sedang menyusun karya ilmiah.</p>
<p>Materi workshop meliputi:</p>
<ul>
<li>Teknik pencarian literatur yang efektif</li>
<li>Penggunaan database jurnal internasional</li>
<li>Instalasi dan konfigurasi Mendeley</li>
<li>Import dan organisasi referensi</li>
<li>Integrasi Mendeley dengan Microsoft Word</li>
<li>Pembuatan daftar pustaka otomatis</li>
</ul>
<p>Peserta workshop sangat antusias mengikuti setiap sesi dan berharap workshop serupa dapat diadakan secara berkala.</p>
<p><em>Creator & Editor: Tim Perpustakaan UNIDA Gontor</em></p>',
            ],
            [
                'title' => 'Koleksi Baru: Buku-Buku Referensi Studi Islam Kontemporer',
                'slug' => 'koleksi-baru-studi-islam',
                'category' => $artikel->id,
                'published_at' => '2024-06-15 11:00:00',
                'featured_image_url' => null,
                'excerpt' => 'Perpustakaan UNIDA Gontor menambah koleksi buku-buku referensi terbaru di bidang Studi Islam Kontemporer. Buku-buku ini diperoleh dari berbagai penerbit ternama.',
                'content' => '<p>Perpustakaan UNIDA Gontor menambah koleksi buku-buku referensi terbaru di bidang Studi Islam Kontemporer. Buku-buku ini diperoleh dari berbagai penerbit ternama baik nasional maupun internasional.</p>
<p>Beberapa judul baru yang tersedia:</p>
<ul>
<li>Islamic Finance: Principles and Practice</li>
<li>Contemporary Islamic Thought</li>
<li>Maqasid al-Shariah in Modern Context</li>
<li>Islamic Education in the 21st Century</li>
<li>Comparative Religion Studies</li>
</ul>
<p>Koleksi baru ini diharapkan dapat mendukung kegiatan pembelajaran dan penelitian di lingkungan UNIDA Gontor, khususnya untuk program studi yang berkaitan dengan kajian Islam.</p>
<p><em>Creator & Editor: Tim Perpustakaan UNIDA Gontor</em></p>',
            ],
        ];

        foreach ($posts as $post) {
            // Check if already exists
            if (News::where('slug', $post['slug'])->exists()) {
                $this->command->info("Skipping: {$post['title']} (already exists)");
                continue;
            }

            News::create([
                'branch_id' => 1,
                'news_category_id' => $post['category'],
                'user_id' => 1,
                'title' => $post['title'],
                'slug' => $post['slug'],
                'excerpt' => $post['excerpt'],
                'content' => $post['content'],
                'featured_image' => null, // Images will need to be downloaded separately
                'status' => 'published',
                'is_featured' => false,
                'is_pinned' => false,
                'published_at' => Carbon::parse($post['published_at']),
                'views' => rand(10, 200),
            ]);

            $this->command->info("Imported: {$post['title']}");
        }

        $this->command->info('WordPress news import completed!');
    }
}
