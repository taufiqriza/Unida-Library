<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->string('device_name')->nullable();
            $table->string('fcm_token', 500)->unique();
            $table->enum('platform', ['android', 'ios'])->default('android');
            $table->string('app_version', 20)->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();

            $table->index('member_id');
        });

        Schema::create('member_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->string('type', 50);
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_notifications');
        Schema::dropIfExists('member_devices');
    }
};
