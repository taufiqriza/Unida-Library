<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('books'); // books, members, etc
            $table->string('filename');
            $table->string('covers_file')->nullable();
            $table->integer('total_rows')->default(0);
            $table->integer('success_count')->default(0);
            $table->integer('warning_count')->default(0);
            $table->integer('error_count')->default(0);
            $table->enum('status', ['pending', 'validating', 'ready', 'processing', 'completed', 'failed'])->default('pending');
            $table->json('preview_data')->nullable();
            $table->json('error_log')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};
