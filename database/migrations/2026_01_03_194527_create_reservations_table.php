<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['pending', 'ready', 'fulfilled', 'cancelled', 'expired'])->default('pending');
            $table->integer('queue_position')->default(1);
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('pickup_deadline')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamps();
            
            $table->index(['book_id', 'status']);
            $table->index(['member_id', 'status']);
            $table->unique(['member_id', 'book_id', 'status'], 'unique_active_reservation');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
