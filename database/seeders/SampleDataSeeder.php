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
        DB::table('publishers')->upsert([
            ['name' => 'Gramedia Pustaka Utama', 'city' => 'Jakarta', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Erlangga', 'city' => 'Jakarta', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mizan', 'city' => 'Bandung', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kompas', 'city' => 'Jakarta', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bentang Pustaka', 'city' => 'Yogyakarta', 'created_at' => now(), 'updated_at' => now()],
        ], ['name'], ['city', 'updated_at']);

        // Authors
        DB::table('authors')->upsert([
            ['name' => 'Andrea Hirata', 'type' => 'personal', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pramoedya Ananta Toer', 'type' => 'personal', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tere Liye', 'type' => 'personal', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dee Lestari', 'type' => 'personal', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Habiburrahman El Shirazy', 'type' => 'personal', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ahmad Fuadi', 'type' => 'personal', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Leila S. Chudori', 'type' => 'personal', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Eka Kurniawan', 'type' => 'personal', 'created_at' => now(), 'updated_at' => now()],
        ], ['name'], ['type', 'updated_at']);

        // Subjects
        DB::table('subjects')->upsert([
            ['name' => 'Fiksi Indonesia', 'classification' => '899.221', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Novel', 'classification' => '808.3', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pendidikan', 'classification' => '370', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sejarah Indonesia', 'classification' => '959.8', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Agama Islam', 'classification' => '297', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Komputer', 'classification' => '004', 'created_at' => now(), 'updated_at' => now()],
        ], ['name'], ['classification', 'updated_at']);

        // Books
        DB::table('books')->upsert([
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
                'is_opac_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
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
                'is_opac_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
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
                'is_opac_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
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
                'is_opac_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
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
                'is_opac_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['isbn'], ['title', 'sor', 'updated_at']);

        // Book-Author pivot
        DB::table('book_author')->insertOrIgnore([
            ['book_id' => 1, 'author_id' => 1],
            ['book_id' => 2, 'author_id' => 2],
            ['book_id' => 3, 'author_id' => 6],
            ['book_id' => 4, 'author_id' => 5],
            ['book_id' => 5, 'author_id' => 4],
        ]);

        // Book-Subject pivot
        DB::table('book_subject')->insertOrIgnore([
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
        DB::table('items')->upsert([
            ['book_id' => 1, 'barcode' => 'B0001-001', 'call_number' => '899.221 AND l c.1', 'inventory_code' => 'INV-2024-001', 'branch_id' => 1, 'collection_type_id' => 1, 'location_id' => 1, 'item_status_id' => 1, 'received_date' => now()->subMonths(6), 'source' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['book_id' => 1, 'barcode' => 'B0001-002', 'call_number' => '899.221 AND l c.2', 'inventory_code' => 'INV-2024-002', 'branch_id' => 1, 'collection_type_id' => 1, 'location_id' => 1, 'item_status_id' => 1, 'received_date' => now()->subMonths(5), 'source' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['book_id' => 2, 'barcode' => 'B0002-001', 'call_number' => '899.221 PRA b c.1', 'inventory_code' => 'INV-2024-003', 'branch_id' => 1, 'collection_type_id' => 1, 'location_id' => 1, 'item_status_id' => 1, 'received_date' => now()->subMonths(4), 'source' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['book_id' => 2, 'barcode' => 'B0002-002', 'call_number' => '899.221 PRA b c.2', 'inventory_code' => 'INV-2024-004', 'branch_id' => 1, 'collection_type_id' => 1, 'location_id' => 1, 'item_status_id' => 1, 'received_date' => now()->subMonths(3), 'source' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['book_id' => 3, 'barcode' => 'B0003-001', 'call_number' => '899.221 FUA n c.1', 'inventory_code' => 'INV-2024-005', 'branch_id' => 1, 'collection_type_id' => 1, 'location_id' => 1, 'item_status_id' => 1, 'received_date' => now()->subMonths(2), 'source' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['book_id' => 4, 'barcode' => 'B0004-001', 'call_number' => '899.221 HAB a c.1', 'inventory_code' => 'INV-2024-006', 'branch_id' => 1, 'collection_type_id' => 1, 'location_id' => 1, 'item_status_id' => 1, 'received_date' => now()->subMonths(1), 'source' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['book_id' => 5, 'barcode' => 'B0005-001', 'call_number' => '899.221 DEE s c.1', 'inventory_code' => 'INV-2024-007', 'branch_id' => 1, 'collection_type_id' => 1, 'location_id' => 1, 'item_status_id' => 1, 'received_date' => now()->subMonths(1), 'source' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['book_id' => 5, 'barcode' => 'B0005-002', 'call_number' => '899.221 DEE s c.2', 'inventory_code' => 'INV-2024-008', 'branch_id' => 1, 'collection_type_id' => 1, 'location_id' => 1, 'item_status_id' => 1, 'received_date' => now()->subMonths(1), 'source' => 1, 'created_at' => now(), 'updated_at' => now()],
        ], ['barcode'], ['call_number', 'updated_at']);

        // Members
        DB::table('members')->upsert([
            ['member_id' => 'M2024001', 'name' => 'Budi Santoso', 'email' => 'budi@example.com', 'password' => bcrypt('password'), 'gender' => 'M', 'member_type_id' => 1, 'branch_id' => 1, 'register_date' => now()->subMonths(3), 'expire_date' => now()->addYear(), 'created_at' => now(), 'updated_at' => now()],
            ['member_id' => 'M2024002', 'name' => 'Siti Rahayu', 'email' => 'siti@example.com', 'password' => bcrypt('password'), 'gender' => 'F', 'member_type_id' => 1, 'branch_id' => 1, 'register_date' => now()->subMonths(2), 'expire_date' => now()->addYear(), 'created_at' => now(), 'updated_at' => now()],
            ['member_id' => 'M2024003', 'name' => 'Dr. Ahmad Wijaya', 'email' => 'ahmad@example.com', 'password' => bcrypt('password'), 'gender' => 'M', 'member_type_id' => 2, 'branch_id' => 1, 'register_date' => now()->subMonths(1), 'expire_date' => now()->addYear(), 'created_at' => now(), 'updated_at' => now()],
            ['member_id' => 'M2024004', 'name' => 'Dewi Lestari', 'email' => 'dewi@example.com', 'password' => bcrypt('password'), 'gender' => 'F', 'member_type_id' => 3, 'branch_id' => 1, 'register_date' => now()->subMonths(1), 'expire_date' => now()->addYear(), 'created_at' => now(), 'updated_at' => now()],
        ], ['member_id'], ['name', 'email', 'password', 'updated_at']);

        // Users with different roles
        DB::table('users')->upsert([
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
        ], ['email'], ['name', 'role', 'updated_at']);
    }
}
