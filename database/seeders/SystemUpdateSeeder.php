<?php

namespace Database\Seeders;

use App\Models\SystemUpdate;
use Illuminate\Database\Seeder;

class SystemUpdateSeeder extends Seeder
{
    public function run(): void
    {
        $updates = [
            [
                'title' => 'Member Account Linking',
                'description' => 'Staff dapat menghubungkan akun mereka dengan data member untuk mengakses fasilitas perpustakaan. Fitur ini memungkinkan staff yang juga mahasiswa/dosen menggunakan satu akun untuk kedua portal.',
                'type' => 'feature',
                'icon' => 'link',
                'color' => 'emerald',
                'target_roles' => ['staff', 'librarian'],
                'priority' => 10,
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => 'E-Learning Integration',
                'description' => 'Integrasi sistem e-learning dengan perpustakaan digital. Akses materi pembelajaran, assignment, dan resources langsung dari portal perpustakaan.',
                'type' => 'feature', 
                'icon' => 'graduation-cap',
                'color' => 'blue',
                'priority' => 9,
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Enhanced Copy Cataloging',
                'description' => 'Perbaikan sistem copy cataloging dengan fitur auto-complete, batch processing, dan validasi data yang lebih akurat untuk mempercepat proses katalogisasi.',
                'type' => 'improvement',
                'icon' => 'copy',
                'color' => 'purple',
                'target_roles' => ['librarian', 'admin'],
                'priority' => 8,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Quick Cover Generator',
                'description' => 'Tool baru untuk generate cover buku secara otomatis menggunakan AI. Mendukung berbagai template dan customization untuk koleksi perpustakaan.',
                'type' => 'feature',
                'icon' => 'image',
                'color' => 'amber',
                'target_roles' => ['librarian'],
                'priority' => 7,
                'published_at' => now()->subDays(4),
            ],
            [
                'title' => 'Digital Member Card',
                'description' => 'Kartu anggota digital dengan QR code untuk akses cepat. Mendukung contactless check-in dan integrasi dengan sistem keamanan perpustakaan.',
                'type' => 'feature',
                'icon' => 'id-card',
                'color' => 'indigo',
                'priority' => 6,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Excel Import Enhancement',
                'description' => 'Perbaikan fitur import Excel dengan validasi data real-time, error handling yang lebih baik, dan support untuk format file yang lebih beragam.',
                'type' => 'improvement',
                'icon' => 'file-excel',
                'color' => 'green',
                'target_roles' => ['admin', 'librarian'],
                'priority' => 5,
                'published_at' => now()->subDays(6),
            ],
            [
                'title' => 'Advanced Analytics Dashboard',
                'description' => 'Dashboard analytics baru dengan visualisasi data yang lebih komprehensif, real-time reporting, dan export ke berbagai format.',
                'type' => 'feature',
                'icon' => 'chart-line',
                'color' => 'blue',
                'target_roles' => ['admin'],
                'priority' => 4,
                'published_at' => now()->subDays(7),
            ],
            [
                'title' => 'Enhanced Security System',
                'description' => 'Peningkatan sistem keamanan dengan 2FA, audit logging yang lebih detail, dan monitoring aktivitas suspicious secara real-time.',
                'type' => 'improvement',
                'icon' => 'shield-alt',
                'color' => 'red',
                'priority' => 8,
                'published_at' => now()->subDays(8),
            ],
            [
                'title' => 'Member Management Upgrade',
                'description' => 'Fitur baru untuk manajemen member termasuk bulk operations, advanced filtering, dan integrasi dengan sistem akademik.',
                'type' => 'improvement',
                'icon' => 'users',
                'color' => 'teal',
                'target_roles' => ['staff', 'admin'],
                'priority' => 6,
                'published_at' => now()->subDays(9),
            ],
            [
                'title' => 'Performance Optimization',
                'description' => 'Optimisasi performa sistem secara menyeluruh. Loading time lebih cepat 40%, response time API diperbaiki, dan penggunaan memory lebih efisien.',
                'type' => 'improvement',
                'icon' => 'tachometer-alt',
                'color' => 'orange',
                'priority' => 7,
                'published_at' => now()->subDays(10),
            ],
        ];

        foreach ($updates as $update) {
            SystemUpdate::create($update);
        }
    }
}
