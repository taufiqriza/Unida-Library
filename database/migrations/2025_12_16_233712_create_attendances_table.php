<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('attendance_locations')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->enum('type', ['check_in', 'check_out']);
            $table->dateTime('scanned_at');
            $table->time('scheduled_time')->nullable();
            $table->time('actual_time');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->unsignedInteger('distance_meters')->nullable();
            $table->enum('verification_method', ['qr_scan', 'location_select', 'manual'])->default('location_select');
            $table->boolean('is_late')->default(false);
            $table->unsignedInteger('late_minutes')->default(0);
            $table->boolean('is_verified')->default(true);
            $table->text('notes')->nullable();
            $table->json('device_info')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'date', 'type'], 'unique_user_date_type');
            $table->index(['branch_id', 'date']);
            $table->index(['user_id', 'date']);
            $table->index(['location_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
