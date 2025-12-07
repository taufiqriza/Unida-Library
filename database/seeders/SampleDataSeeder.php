<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Publishers
        $publishers = [
            ['name' => 'Gramedia Pustaka Utama', 'city' => 'Jakarta'],
            ['name' => 'Erlangga', 'city' => 'Jakarta'],
            ['name' => 'Mizan', 'city' => 'Bandung'],
            ['name' => 'Kompas', 'city' => 'Jakarta'],
            ['name' => 'Bentang Pustaka', 'city' => 'Yogyakarta'],
        ];
        foreach ($publishers as $p) {
            DB::table('publishers')->insert(array_merge($p, ['created_at' => now(), 'updated_at' => now()]));
        }

        // Places
        $places = ['Jakarta', 'Bandung', 'Yogyakarta', 'Surabaya', 'Semarang'];
        foreach ($places as $p) {
            DB::table('places')->insert(['name' => $p, 'created_at' => now(), 'updated_at' => now()]);
        }

        // Authors
        $authors = [
            ['name' => 'Andrea Hirata', 'type' => 'personal'],
            ['name' => 'Pramoedya Ananta Toer', 'type' => 'personal'],
            ['name' => 'Tere Liye', 'type' => 'personal'],
            ['name' => 'Dee Lestari', 'type' => 'personal'],
            ['name' => 'Habiburrahman El Shirazy', 'type' => 'personal'],
            ['name' => 'Ahmad Fuadi', 'type' => 'personal'],
            ['name' => 'Leila S. Chudori', 'type' => 'personal'],
            ['name' => 'Eka Kurniawan', 'type' => 'personal'],
        ];
        foreach ($authors as $a) {
            DB::table('authors')->insert(array_merge($a, ['created_at' => now(), 'updated_at' => now()]));
        }

        // Subjects
        $subjects = [
            ['name' => 'Fiksi Indonesia', 'classification' => '899.221'],
            ['name' => 'Novel', 'classification' => '808.3'],
            ['name' => 'Pendidikan', 'classification' => '370'],
            ['name' => 'Sejarah Indonesia', 'classification' => '959.8'],
            ['name' => 'Agama Islam', 'classification' => '297'],
            ['name' => 'Komputer', 'classification' => '004'],
        ];
        foreach ($subjects as $s) {
            DB::table('subjects')->insert(array_merge($s, ['created_at' => now(), 'updated_at' => now()]));
        }

        // Books
        $books = [
            [
                'title' => 'Laskar Pelangi',
                'sor' => 'Andrea Hirata',
                'isbn' => '9789792234565',
                'publisher_id' => 1,
                'place_id' => 1,
                'publish_year' => '2005',
                'classification' => '899.221',
                'call_number' => '899.221 AND l',
                'collation' => 'xii, 529 hlm.; 20 cm',
                'abstract' => 'Novel tentang perjuangan anak-anak Belitung dalam mengejar pendidikan.',
                'language' => 'id',
                'media_type_id' => 1,
                'branch_id' => 1,
            ],
            [
                'title' => 'Bumi Manusia',
                'sor' => 'Pramoedya Ananta Toer',
                'isbn' => '9789799731234',
                'publisher_id' => 1,
                'place_id' => 1,
                'publish_year' => '1980',
                'classification' => '899.221',
                'call_number' => '899.221 PRA b',
                'collation' => 'xviii, 535 hlm.; 21 cm',
                'abstract' => 'Novel sejarah tentang perjuangan bangsa Indonesia di era kolonial.',
                'language' => 'id',
                'media_type_id' => 1,
                'branch_id' => 1,
            ],
            [
                'title' => 'Negeri 5 Menara',
                'sor' => 'Ahmad Fuadi',
                'isbn' => '9789792254123',
                'publisher_id' => 1,
                'place_id' => 1,
                'publish_year' => '2009',
                'classification' => '899.221',
                'call_number' => '899.221 FUA n',
                'collation' => 'x, 423 hlm.; 20 cm',
                'abstract' => 'Novel inspiratif tentang kehidupan di pesantren.',
                'language' => 'id',
                'media_type_id' => 1,
                'branch_id' => 1,
            ],
            [
                'title' => 'Ayat-Ayat Cinta',
                'sor' => 'Habiburrahman El Shirazy',
                'isbn' => '9789793062891',
                'publisher_id' => 3,
                'place_id' => 2,
                'publish_year' => '2004',
                'classification' => '899.221',
                'call_number' => '899.221 HAB a',
                'collation' => 'xiv, 419 hlm.; 20 cm',
                'abstract' => 'Novel religius tentang cinta dan kehidupan mahasiswa Indonesia di Mesir.',
                'language' => 'id',
                'media_type_id' => 1,
                'branch_id' => 1,
            ],
            [
                'title' => 'Supernova: Ksatria, Puteri, dan Bintang Jatuh',
                'sor' => 'Dee Lestari',
                'isbn' => '9789791227001',
                'publisher_id' => 5,
                'place_id' => 3,
                'publish_year' => '2001',
                'classification' => '899.221',
                'call_number' => '899.221 DEE s',
                'collation' => 'viii, 312 hlm.; 20 cm',
                'abstract' => 'Novel fiksi ilmiah Indonesia.',
                'language' => 'id',
                'media_type_id' => 1,
                'branch_id' => 1,
            ],
        ];

        foreach ($books as $book) {
            DB::table('books')->insert(array_merge($book, [
                'is_opac_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Book-Author pivot
        DB::table('book_author')->insert([
            ['book_id' => 1, 'author_id' => 1],
            ['book_id' => 2, 'author_id' => 2],
            ['book_id' => 3, 'author_id' => 6],
            ['book_id' => 4, 'author_id' => 5],
            ['book_id' => 5, 'author_id' => 4],
        ]);

        // Book-Subject pivot
        DB::table('book_subject')->insert([
            ['book_id' => 1, 'subject_id' => 1],
            ['book_id' => 1, 'subject_id' => 2],
            ['book_id' => 1, 'subject_id' => 3],
            ['book_id' => 2, 'subject_id' => 1],
            ['book_id' => 2, 'subject_id' => 4],
            ['book_id' => 3, 'subject_id' => 1],
            ['book_id' => 3, 'subject_id' => 5],
            ['book_id' => 4, 'subject_id' => 1],
            ['book_id' => 4, 'subject_id' => 5],
            ['book_id' => 5, 'subject_id' => 1],
            ['book_id' => 5, 'subject_id' => 2],
        ]);

        // Items (Eksemplar)
        $items = [
            ['book_id' => 1, 'barcode' => 'B0001-001', 'call_number' => '899.221 AND l c.1', 'inventory_code' => 'INV-2024-001'],
            ['book_id' => 1, 'barcode' => 'B0001-002', 'call_number' => '899.221 AND l c.2', 'inventory_code' => 'INV-2024-002'],
            ['book_id' => 2, 'barcode' => 'B0002-001', 'call_number' => '899.221 PRA b c.1', 'inventory_code' => 'INV-2024-003'],
            ['book_id' => 2, 'barcode' => 'B0002-002', 'call_number' => '899.221 PRA b c.2', 'inventory_code' => 'INV-2024-004'],
            ['book_id' => 3, 'barcode' => 'B0003-001', 'call_number' => '899.221 FUA n c.1', 'inventory_code' => 'INV-2024-005'],
            ['book_id' => 4, 'barcode' => 'B0004-001', 'call_number' => '899.221 HAB a c.1', 'inventory_code' => 'INV-2024-006'],
            ['book_id' => 5, 'barcode' => 'B0005-001', 'call_number' => '899.221 DEE s c.1', 'inventory_code' => 'INV-2024-007'],
            ['book_id' => 5, 'barcode' => 'B0005-002', 'call_number' => '899.221 DEE s c.2', 'inventory_code' => 'INV-2024-008'],
        ];

        foreach ($items as $item) {
            DB::table('items')->insert(array_merge($item, [
                'branch_id' => 1,
                'collection_type_id' => 1,
                'location_id' => 1,
                'item_status_id' => 1,
                'received_date' => now()->subMonths(rand(1, 12)),
                'source' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Members
        $members = [
            ['member_id' => 'M2024001', 'name' => 'Budi Santoso', 'email' => 'budi@example.com', 'gender' => 'M', 'member_type_id' => 1],
            ['member_id' => 'M2024002', 'name' => 'Siti Rahayu', 'email' => 'siti@example.com', 'gender' => 'F', 'member_type_id' => 1],
            ['member_id' => 'M2024003', 'name' => 'Dr. Ahmad Wijaya', 'email' => 'ahmad@example.com', 'gender' => 'M', 'member_type_id' => 2],
            ['member_id' => 'M2024004', 'name' => 'Dewi Lestari', 'email' => 'dewi@example.com', 'gender' => 'F', 'member_type_id' => 3],
        ];

        foreach ($members as $m) {
            DB::table('members')->insert(array_merge($m, [
                'branch_id' => 1,
                'register_date' => now()->subMonths(rand(1, 6)),
                'expire_date' => now()->addYear(),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Sample Loans (for chart testing)
        $items = DB::table('items')->pluck('id')->toArray();
        $memberIds = DB::table('members')->pluck('id')->toArray();
        
        foreach (range(0, 6) as $daysAgo) {
            $loansCount = rand(1, 3);
            for ($i = 0; $i < $loansCount; $i++) {
                if (empty($items)) break;
                $itemId = $items[array_rand($items)];
                DB::table('loans')->insert([
                    'branch_id' => 1,
                    'member_id' => $memberIds[array_rand($memberIds)],
                    'item_id' => $itemId,
                    'loan_date' => now()->subDays($daysAgo),
                    'due_date' => now()->subDays($daysAgo)->addDays(7),
                    'is_returned' => $daysAgo > 3,
                    'return_date' => $daysAgo > 3 ? now()->subDays($daysAgo - 2) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Users with different roles
        DB::table('users')->insert([
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@perpustakaan.id',
                'password' => Hash::make('password'),
                'branch_id' => null,
                'role' => 'super_admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin Pusat',
                'email' => 'admin@perpustakaan.id',
                'password' => Hash::make('password'),
                'branch_id' => 1,
                'role' => 'admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pustakawan',
                'email' => 'pustakawan@perpustakaan.id',
                'password' => Hash::make('password'),
                'branch_id' => 1,
                'role' => 'librarian',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
