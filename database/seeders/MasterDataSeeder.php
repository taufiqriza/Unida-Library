<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Branches
        DB::table('branches')->upsert([
            ['code' => 'PUSAT', 'name' => 'Perpustakaan Pusat', 'is_main' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ], ['code'], ['name', 'is_main', 'is_active', 'updated_at']);

        // Media Types (GMD)
        DB::table('media_types')->upsert([
            ['name' => 'Text', 'code' => 'TA', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Computer File', 'code' => 'CF', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cartographic Material', 'code' => 'CM', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Projected Medium', 'code' => 'PM', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sound Recording', 'code' => 'SR', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Video Recording', 'code' => 'VR', 'created_at' => now(), 'updated_at' => now()],
        ], ['code'], ['name', 'updated_at']);

        // Content Types (RDA)
        DB::table('content_types')->upsert([
            ['name' => 'text', 'code' => 'txt', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'performed music', 'code' => 'prm', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'computer program', 'code' => 'cop', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'cartographic image', 'code' => 'cri', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'still image', 'code' => 'sti', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'two-dimensional moving image', 'code' => 'tdi', 'created_at' => now(), 'updated_at' => now()],
        ], ['code'], ['name', 'updated_at']);

        // Carrier Types (RDA)
        DB::table('carrier_types')->upsert([
            ['name' => 'volume', 'code' => 'nc', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'computer disc', 'code' => 'cd', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'online resource', 'code' => 'cr', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'audio disc', 'code' => 'sd', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'video disc', 'code' => 'vd', 'created_at' => now(), 'updated_at' => now()],
        ], ['code'], ['name', 'updated_at']);

        // Collection Types
        DB::table('collection_types')->upsert([
            ['name' => 'Buku Teks', 'code' => 'BT', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Referensi', 'code' => 'REF', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Majalah', 'code' => 'MAJ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jurnal', 'code' => 'JUR', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Skripsi/Tesis', 'code' => 'SKR', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'CD/DVD', 'code' => 'AV', 'created_at' => now(), 'updated_at' => now()],
        ], ['code'], ['name', 'updated_at']);

        // Member Types
        DB::table('member_types')->upsert([
            ['name' => 'Mahasiswa', 'loan_limit' => 3, 'loan_period' => 7, 'fine_per_day' => 500, 'membership_period' => 365, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dosen', 'loan_limit' => 5, 'loan_period' => 14, 'fine_per_day' => 1000, 'membership_period' => 365, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Karyawan', 'loan_limit' => 3, 'loan_period' => 7, 'fine_per_day' => 500, 'membership_period' => 365, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Umum', 'loan_limit' => 2, 'loan_period' => 7, 'fine_per_day' => 1000, 'membership_period' => 180, 'created_at' => now(), 'updated_at' => now()],
        ], ['name'], ['loan_limit', 'loan_period', 'fine_per_day', 'membership_period', 'updated_at']);

        // Item Statuses
        DB::table('item_statuses')->upsert([
            ['code' => 'AVL', 'name' => 'Tersedia', 'no_loan' => false, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'REP', 'name' => 'Sedang Diperbaiki', 'no_loan' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'MIS', 'name' => 'Hilang', 'no_loan' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'REF', 'name' => 'Referensi', 'no_loan' => true, 'created_at' => now(), 'updated_at' => now()],
        ], ['code'], ['name', 'no_loan', 'updated_at']);

        // Locations
        DB::table('locations')->upsert([
            ['code' => 'RAK-A', 'name' => 'Rak A - Umum', 'branch_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'RAK-B', 'name' => 'Rak B - Referensi', 'branch_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'RAK-C', 'name' => 'Rak C - Jurnal', 'branch_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        ], ['code'], ['name', 'branch_id', 'updated_at']);

        // Places (Tempat Terbit)
        DB::table('places')->upsert([
            ['name' => 'Jakarta', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bandung', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Yogyakarta', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Surabaya', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Semarang', 'created_at' => now(), 'updated_at' => now()],
        ], ['name'], ['updated_at']);

        // Frequencies (untuk serial/majalah)
        DB::table('frequencies')->upsert([
            ['name' => 'Harian', 'time_increment' => 1, 'time_unit' => 'day', 'language_prefix' => 'id', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mingguan', 'time_increment' => 1, 'time_unit' => 'week', 'language_prefix' => 'id', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bulanan', 'time_increment' => 1, 'time_unit' => 'month', 'language_prefix' => 'id', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Triwulan', 'time_increment' => 3, 'time_unit' => 'month', 'language_prefix' => 'id', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tahunan', 'time_increment' => 1, 'time_unit' => 'year', 'language_prefix' => 'id', 'created_at' => now(), 'updated_at' => now()],
        ], ['name'], ['time_increment', 'time_unit', 'language_prefix', 'updated_at']);

        // Suppliers
        DB::table('suppliers')->upsert([
            ['name' => 'Toko Buku Gramedia', 'contact' => 'Sales', 'phone' => '021-12345678', 'email' => 'sales@gramedia.com', 'address' => 'Jakarta', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'CV Buku Murah', 'contact' => 'Admin', 'phone' => '022-87654321', 'email' => 'admin@bukumurah.com', 'address' => 'Bandung', 'created_at' => now(), 'updated_at' => now()],
        ], ['name'], ['contact', 'phone', 'email', 'address', 'updated_at']);
    }
}
