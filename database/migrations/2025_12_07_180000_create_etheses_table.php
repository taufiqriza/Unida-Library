<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fakultas/Faculty
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->timestamps();
        });

        // Program Studi/Department
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('degree')->default('S1'); // S1, S2, S3
            $table->timestamps();
        });

        // E-Thesis
        Schema::create('etheses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('title_en')->nullable(); // English title
            $table->text('abstract');
            $table->text('abstract_en')->nullable();
            $table->string('author'); // Nama penulis
            $table->string('nim')->nullable(); // NIM
            $table->string('advisor1'); // Pembimbing 1
            $table->string('advisor2')->nullable(); // Pembimbing 2
            $table->string('examiner1')->nullable(); // Penguji 1
            $table->string('examiner2')->nullable(); // Penguji 2
            $table->string('examiner3')->nullable(); // Penguji 3
            $table->year('year');
            $table->date('defense_date')->nullable(); // Tanggal sidang
            $table->string('type')->default('skripsi'); // skripsi, tesis, disertasi
            $table->string('keywords')->nullable();
            $table->string('file_path')->nullable(); // PDF full-text
            $table->string('cover_path')->nullable(); // Cover image
            $table->string('url')->nullable(); // External URL
            $table->boolean('is_public')->default(true); // Akses publik
            $table->boolean('is_fulltext_public')->default(false); // Full-text publik
            $table->integer('views')->default(0);
            $table->integer('downloads')->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        // Thesis subjects (many-to-many)
        Schema::create('ethesis_subject', function (Blueprint $table) {
            $table->foreignId('ethesis_id')->constrained('etheses')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->primary(['ethesis_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ethesis_subject');
        Schema::dropIfExists('etheses');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('faculties');
    }
};
