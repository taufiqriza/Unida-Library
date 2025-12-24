<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pddikti_data', function (Blueprint $table) {
            $table->id();
            $table->string('pddikti_id')->unique()->comment('Unique ID from PDDikti');
            $table->enum('type', ['mahasiswa', 'dosen'])->index();
            $table->string('name');
            $table->string('nim_nidn')->nullable()->comment('NIM for students, NIDN for lecturers');
            $table->string('prodi')->nullable()->comment('Program Studi');
            $table->string('prodi_code')->nullable()->comment('Kode Prodi');
            $table->string('jenjang')->nullable()->comment('S1, S2, S3, D3, etc');
            $table->string('status')->nullable()->comment('Aktif, Cuti, Lulus, etc');
            $table->year('angkatan')->nullable()->comment('Entry year');
            $table->string('pt_name')->nullable()->comment('Nama Perguruan Tinggi');
            $table->string('pt_id')->nullable()->comment('ID Perguruan Tinggi di PDDikti');
            $table->timestamp('synced_at')->nullable()->comment('Last sync time from PDDikti');
            
            // Linking to member
            $table->foreignId('linked_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->timestamp('linked_at')->nullable()->comment('When this record was linked to a member');
            
            $table->timestamps();
            
            // Indexes for search
            $table->index('name');
            $table->index('nim_nidn');
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pddikti_data');
    }
};
