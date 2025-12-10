<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plagiarism_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('thesis_submission_id')->nullable()->constrained()->onDelete('set null');
            
            // Document Info
            $table->string('document_title')->nullable();
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('file_type', 10); // pdf, docx
            $table->unsignedInteger('file_size'); // bytes
            $table->unsignedInteger('word_count')->nullable();
            $table->unsignedInteger('page_count')->nullable();
            
            // Check Result
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->decimal('similarity_score', 5, 2)->nullable(); // 0.00 - 100.00
            $table->json('similarity_sources')->nullable(); // Array of matched sources
            $table->json('detailed_report')->nullable(); // Full report data
            
            // Provider Info
            $table->string('provider', 50)->default('internal'); // turnitin, ithenticate, copyleaks, internal
            $table->string('external_id')->nullable(); // ID from external service
            $table->string('external_report_url')->nullable(); // URL to external report
            
            // Certificate
            $table->string('certificate_number')->nullable()->unique();
            $table->string('certificate_path')->nullable();
            $table->timestamp('certificate_generated_at')->nullable();
            
            // Processing timestamps
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['member_id', 'status']);
            $table->index('certificate_number');
            $table->index('status');
        });

        // Table for storing extracted text from documents (for internal comparison)
        Schema::create('document_fingerprints', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable'); // Can be ethesis, plagiarism_check, etc.
            $table->longText('content_text'); // Extracted text
            $table->json('content_chunks')->nullable(); // Text split into chunks for comparison
            $table->string('content_hash', 64)->nullable(); // SHA256 hash of content
            $table->unsignedInteger('word_count')->nullable();
            $table->timestamps();
            
            $table->index('content_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_fingerprints');
        Schema::dropIfExists('plagiarism_checks');
    }
};
