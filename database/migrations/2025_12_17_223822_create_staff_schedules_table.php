<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Schedule info
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', [
                'piket',           // Piket harian
                'shift',           // Shift kerja (pagi/siang/malam)
                'penempatan',      // Penempatan area
                'tugas_rutin',     // Tugas rutin berulang
                'rapat',           // Jadwal rapat
                'pelatihan',       // Jadwal pelatihan
                'cuti',            // Cuti/izin
                'lainnya'
            ])->default('piket');
            
            // Area/location for penempatan
            $table->enum('location', [
                'sirkulasi',
                'referensi',
                'rak_koleksi',
                'ruang_baca',
                'multimedia',
                'administrasi',
                'gudang',
                'all'
            ])->nullable();
            
            // Time settings
            $table->date('schedule_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('shift', ['pagi', 'siang', 'malam', 'full'])->nullable();
            
            // Recurrence for recurring schedules
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurrence_pattern', [
                'daily',
                'weekly',     
                'biweekly',
                'monthly'
            ])->nullable();
            $table->json('recurrence_days')->nullable(); // [1,2,3] for Mon,Tue,Wed
            $table->date('recurrence_end_date')->nullable();
            
            // Status
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled', 'swapped'])->default('scheduled');
            $table->text('notes')->nullable();
            
            // Swap request
            $table->foreignId('swap_requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('swap_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('swap_approved_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['schedule_date', 'user_id']);
            $table->index(['branch_id', 'schedule_date']);
            $table->index(['type', 'schedule_date']);
        });
        
        // Pivot table for schedule swap requests
        Schema::create('schedule_swap_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('staff_schedules')->cascadeOnDelete();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('target_user_id')->constrained('users')->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_swap_requests');
        Schema::dropIfExists('staff_schedules');
    }
};
