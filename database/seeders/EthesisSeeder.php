<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Ethesis;
use App\Models\Faculty;
use Illuminate\Database\Seeder;

class EthesisSeeder extends Seeder
{
    public function run(): void
    {
        // Fakultas
        $fti = Faculty::create(['name' => 'Fakultas Teknologi Informasi', 'code' => 'FTI']);
        $feb = Faculty::create(['name' => 'Fakultas Ekonomi dan Bisnis', 'code' => 'FEB']);
        $fkip = Faculty::create(['name' => 'Fakultas Keguruan dan Ilmu Pendidikan', 'code' => 'FKIP']);
        $fh = Faculty::create(['name' => 'Fakultas Hukum', 'code' => 'FH']);

        // Program Studi
        $ti = Department::create(['faculty_id' => $fti->id, 'name' => 'Teknik Informatika', 'code' => 'TI', 'degree' => 'S1']);
        $si = Department::create(['faculty_id' => $fti->id, 'name' => 'Sistem Informasi', 'code' => 'SI', 'degree' => 'S1']);
        $mti = Department::create(['faculty_id' => $fti->id, 'name' => 'Magister Teknik Informatika', 'code' => 'MTI', 'degree' => 'S2']);
        
        $akuntansi = Department::create(['faculty_id' => $feb->id, 'name' => 'Akuntansi', 'code' => 'AKT', 'degree' => 'S1']);
        $manajemen = Department::create(['faculty_id' => $feb->id, 'name' => 'Manajemen', 'code' => 'MNJ', 'degree' => 'S1']);
        
        $pbi = Department::create(['faculty_id' => $fkip->id, 'name' => 'Pendidikan Bahasa Inggris', 'code' => 'PBI', 'degree' => 'S1']);
        $pgsd = Department::create(['faculty_id' => $fkip->id, 'name' => 'Pendidikan Guru Sekolah Dasar', 'code' => 'PGSD', 'degree' => 'S1']);
        
        $ilmuhukum = Department::create(['faculty_id' => $fh->id, 'name' => 'Ilmu Hukum', 'code' => 'IH', 'degree' => 'S1']);

        // Sample Thesis
        Ethesis::create([
            'branch_id' => 1,
            'department_id' => $ti->id,
            'title' => 'Implementasi Machine Learning untuk Prediksi Kelulusan Mahasiswa',
            'abstract' => 'Penelitian ini bertujuan untuk mengimplementasikan algoritma machine learning dalam memprediksi kelulusan mahasiswa berdasarkan data akademik. Metode yang digunakan adalah Random Forest dan Support Vector Machine.',
            'author' => 'Ahmad Fauzi',
            'nim' => '2019101001',
            'advisor1' => 'Dr. Budi Santoso, M.Kom.',
            'advisor2' => 'Ir. Dewi Lestari, M.T.',
            'year' => 2024,
            'type' => 'skripsi',
            'keywords' => 'machine learning, prediksi, kelulusan, random forest',
            'is_public' => true,
        ]);

        Ethesis::create([
            'branch_id' => 1,
            'department_id' => $si->id,
            'title' => 'Perancangan Sistem Informasi Perpustakaan Digital Berbasis Web',
            'abstract' => 'Skripsi ini membahas perancangan dan implementasi sistem informasi perpustakaan digital menggunakan framework Laravel. Sistem ini memungkinkan pengelolaan koleksi digital dan akses online.',
            'author' => 'Siti Nurhaliza',
            'nim' => '2019102015',
            'advisor1' => 'Dr. Eko Prasetyo, M.Kom.',
            'year' => 2024,
            'type' => 'skripsi',
            'keywords' => 'sistem informasi, perpustakaan digital, laravel, web',
            'is_public' => true,
        ]);

        Ethesis::create([
            'branch_id' => 1,
            'department_id' => $mti->id,
            'title' => 'Analisis Sentimen Media Sosial Menggunakan Deep Learning',
            'abstract' => 'Tesis ini menganalisis sentimen publik di media sosial menggunakan pendekatan deep learning dengan arsitektur LSTM dan BERT. Hasil penelitian menunjukkan akurasi yang tinggi dalam klasifikasi sentimen.',
            'author' => 'Rudi Hermawan',
            'nim' => '2022201005',
            'advisor1' => 'Prof. Dr. Agus Wijaya, M.Sc.',
            'advisor2' => 'Dr. Ratna Sari, M.Kom.',
            'year' => 2024,
            'type' => 'tesis',
            'keywords' => 'analisis sentimen, deep learning, LSTM, BERT, media sosial',
            'is_public' => true,
        ]);
    }
}
