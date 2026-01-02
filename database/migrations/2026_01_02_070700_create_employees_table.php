<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('niy', 20)->nullable();
            $table->string('nidn', 50)->nullable()->index();
            $table->string('nitk', 30)->nullable();
            
            // Basic info
            $table->string('name');
            $table->string('front_title', 50)->nullable();
            $table->string('back_title', 50)->nullable();
            $table->string('full_name')->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->string('birth_place_date')->nullable();
            
            // Employment
            $table->enum('type', ['dosen', 'tendik'])->index();
            $table->string('status', 30)->nullable();          // Aktif, Izin Studi, dll
            $table->string('yayasan', 10)->nullable();         // TY, TTY
            $table->string('category', 30)->nullable();        // DT, DK, PnD, Administrasi, dll
            $table->string('position', 50)->nullable();        // Jabatan fungsional
            $table->year('join_year')->nullable();
            
            // Unit
            $table->string('faculty', 50)->nullable();
            $table->string('prodi', 100)->nullable();
            $table->string('satker', 50)->nullable();          // Untuk tendik
            $table->string('campus', 20)->nullable();
            
            // Education
            $table->string('education_level', 10)->nullable(); // S1, S2, S3
            $table->string('s1_univ')->nullable();
            $table->year('s1_year')->nullable();
            $table->string('s2_univ')->nullable();
            $table->year('s2_year')->nullable();
            $table->string('s3_univ')->nullable();
            $table->year('s3_year')->nullable();
            $table->string('expertise')->nullable();
            
            // Contact (akan di-enrich dari e-kinerja)
            $table->string('email')->nullable()->index();
            
            // Certification
            $table->boolean('serdos')->default(false);
            
            // Additional
            $table->string('domicile', 50)->nullable();
            $table->text('additional_duties')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['niy', 'type'], 'employees_niy_type_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
