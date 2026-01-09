<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // full, partial, branch
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            
            // Backup details
            $table->json('tables_included'); // which tables were backed up
            $table->json('data_counts'); // record counts per table
            $table->bigInteger('total_records');
            $table->string('file_size');
            $table->string('compression_type')->default('json');
            
            // File paths
            $table->json('file_paths'); // array of backup file paths
            $table->string('checksum'); // for integrity verification
            
            // Status and metadata
            $table->enum('status', ['pending', 'completed', 'failed', 'restored'])->default('pending');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // additional info
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['branch_id', 'created_at']);
            $table->index(['type', 'status']);
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
