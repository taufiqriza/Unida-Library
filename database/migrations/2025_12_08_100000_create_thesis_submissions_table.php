<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thesis_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ethesis_id')->nullable()->constrained()->nullOnDelete();
            
            // Informasi Tugas Akhir
            $table->string('type')->default('skripsi'); // skripsi, tesis, disertasi
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->text('abstract');
            $table->text('abstract_en')->nullable();
            $table->string('keywords')->nullable();
            
            // Penulis
            $table->string('author');
            $table->string('nim');
            
            // Pembimbing & Penguji
            $table->string('advisor1');
            $table->string('advisor2')->nullable();
            $table->string('examiner1')->nullable();
            $table->string('examiner2')->nullable();
            $table->string('examiner3')->nullable();
            
            // Tanggal
            $table->year('year');
            $table->date('defense_date')->nullable();
            
            // Files - Stored in private disk for security
            $table->string('cover_file')->nullable();        // Cover (image) - public after published
            $table->string('approval_file')->nullable();     // Lembar pengesahan (PDF) - restricted
            $table->string('preview_file')->nullable();      // BAB 1-3 (PDF) - public after published
            $table->string('fulltext_file')->nullable();     // Full text (PDF) - configurable access
            
            // Status
            $table->string('status')->default('draft');
            
            // Review
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Visibility Settings (controlled by admin)
            $table->boolean('cover_visible')->default(true);
            $table->boolean('approval_visible')->default(false);  // Default: only owner & admin
            $table->boolean('preview_visible')->default(true);    // BAB 1-3 public
            $table->boolean('fulltext_visible')->default(false);  // Full text restricted by default
            $table->boolean('allow_fulltext_public')->default(false); // User request for public access
            
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('type');
            $table->index(['member_id', 'status']);
        });

        Schema::create('thesis_submission_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('thesis_submissions')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thesis_submission_logs');
        Schema::dropIfExists('thesis_submissions');
    }
};
