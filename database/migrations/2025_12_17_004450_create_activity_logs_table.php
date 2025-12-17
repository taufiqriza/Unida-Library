<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            
            // Action details
            $table->string('action'); // create, update, delete, login, logout, etc.
            $table->string('module'); // biblio, member, circulation, attendance, etc.
            $table->string('description');
            
            // Target entity (what was affected)
            $table->string('loggable_type')->nullable(); // Model class
            $table->unsignedBigInteger('loggable_id')->nullable(); // Model ID
            
            // Additional context
            $table->json('properties')->nullable(); // old values, new values, etc.
            $table->json('metadata')->nullable(); // ip, user_agent, etc.
            
            // Status
            $table->enum('level', ['info', 'warning', 'error', 'critical'])->default('info');
            
            $table->timestamps();
            
            // Indexes for fast querying
            $table->index(['user_id', 'created_at']);
            $table->index(['branch_id', 'created_at']);
            $table->index(['module', 'action']);
            $table->index(['loggable_type', 'loggable_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
