<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        // Categories
        $pengumuman = NewsCategory::create(['name' => 'Pengumuman', 'slug' => 'pengumuman', 'sort_order' => 1]);
        $kegiatan = NewsCategory::create(['name' => 'Kegiatan', 'slug' => 'kegiatan', 'sort_order' => 2]);
        $koleksi = NewsCategory::create(['name' => 'Koleksi Baru', 'slug' => 'koleksi-baru', 'sort_order' => 3]);
        $tips = NewsCategory::create(['name' => 'Tips & Tutorial', 'slug' => 'tips-tutorial', 'sort_order' => 4]);

        // Sample News
        News::create([
            'branch_id' => 1,
            'news_category_id' => $pengumuman->id,
            'user_id' => 1,
            'title' => 'Perpustakaan Buka 24 Jam Selama Masa Ujian',
            'slug' => 'perpustakaan-buka-24-jam-selama-masa-ujian',
            'excerpt' => 'Dalam rangka mendukung kegiatan belajar mahasiswa, perpustakaan akan beroperasi 24 jam selama periode ujian akhir semester.',
            'content' => '<p>Dalam rangka mendukung kegiatan belajar mahasiswa selama periode Ujian Akhir Semester (UAS), Perpustakaan Universitas akan beroperasi selama 24 jam penuh.</p><p><strong>Periode:</strong> 15 Desember - 30 Desember 2024</p><p><strong>Fasilitas yang tersedia:</strong></p><ul><li>Ruang baca dengan AC</li><li>WiFi gratis</li><li>Stop kontak untuk charging</li><li>Kantin mini (buka hingga pukul 22.00)</li></ul><p>Mahasiswa diharapkan membawa kartu anggota perpustakaan yang masih berlaku.</p>',
            'status' => 'published',
            'is_featured' => true,
            'is_pinned' => true,
            'published_at' => now(),
        ]);

        News::create([
            'branch_id' => 1,
            'news_category_id' => $kegiatan->id,
            'user_id' => 1,
            'title' => 'Workshop Literasi Digital untuk Mahasiswa Baru',
            'slug' => 'workshop-literasi-digital-mahasiswa-baru',
            'excerpt' => 'Perpustakaan mengadakan workshop literasi digital untuk membantu mahasiswa baru dalam mencari dan mengevaluasi sumber informasi.',
            'content' => '<p>Perpustakaan akan mengadakan Workshop Literasi Digital yang ditujukan khusus untuk mahasiswa baru angkatan 2024.</p><p><strong>Materi yang akan dibahas:</strong></p><ul><li>Cara mencari jurnal ilmiah</li><li>Penggunaan database akademik</li><li>Menghindari plagiarisme</li><li>Manajemen referensi dengan Mendeley</li></ul><p><strong>Waktu:</strong> Sabtu, 20 Desember 2024, pukul 09.00-12.00 WIB</p><p><strong>Tempat:</strong> Ruang Seminar Perpustakaan Lt. 3</p><p>Pendaftaran gratis melalui link: perpustakaan.univ.ac.id/workshop</p>',
            'status' => 'published',
            'is_featured' => true,
            'published_at' => now()->subDays(2),
        ]);

        News::create([
            'branch_id' => 1,
            'news_category_id' => $koleksi->id,
            'user_id' => 1,
            'title' => 'Koleksi E-Book Baru: 500 Judul dari Springer Nature',
            'slug' => 'koleksi-ebook-baru-springer-nature',
            'excerpt' => 'Perpustakaan menambah koleksi e-book dari penerbit Springer Nature dengan 500 judul baru di berbagai bidang ilmu.',
            'content' => '<p>Kabar gembira untuk sivitas akademika! Perpustakaan telah berlangganan 500 judul e-book baru dari penerbit Springer Nature.</p><p><strong>Bidang ilmu yang tersedia:</strong></p><ul><li>Computer Science</li><li>Engineering</li><li>Business & Economics</li><li>Medicine & Health Sciences</li></ul><p>E-book dapat diakses melalui portal perpustakaan digital dengan login menggunakan akun SSO universitas.</p>',
            'status' => 'published',
            'published_at' => now()->subDays(5),
        ]);

        News::create([
            'branch_id' => 1,
            'news_category_id' => $tips->id,
            'user_id' => 1,
            'title' => 'Cara Mengakses E-Journal dari Rumah',
            'slug' => 'cara-mengakses-ejournal-dari-rumah',
            'excerpt' => 'Tutorial lengkap cara mengakses database jurnal ilmiah dari luar kampus menggunakan VPN universitas.',
            'content' => '<p>Banyak mahasiswa yang bertanya bagaimana cara mengakses e-journal dari rumah. Berikut panduan lengkapnya:</p><p><strong>Langkah-langkah:</strong></p><ol><li>Download aplikasi VPN universitas</li><li>Login dengan akun SSO</li><li>Pilih server "Library Access"</li><li>Buka browser dan akses portal perpustakaan</li><li>Pilih database yang diinginkan</li></ol><p>Jika mengalami kendala, silakan hubungi helpdesk perpustakaan di ext. 1234.</p>',
            'status' => 'draft',
            'published_at' => null,
        ]);
    }
}
