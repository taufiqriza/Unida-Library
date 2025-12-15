<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Main notifications table
        Schema::create('staff_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type'); // Notification class
            $table->morphs('notifiable'); // user_id, member_id, etc
            
            // Content
            $table->enum('category', ['loan', 'task', 'member', 'system', 'announcement'])->default('system');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->string('title');
            $table->text('body');
            $table->string('action_url', 500)->nullable();
            $table->string('action_label', 100)->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('color', 20)->nullable();
            $table->string('image_url', 500)->nullable();
            
            // Metadata
            $table->json('data')->nullable();
            
            // Status tracking
            $table->timestamp('read_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('dismissed_at')->nullable();
            
            // Delivery tracking
            $table->json('channels_sent')->nullable();
            $table->json('channels_delivered')->nullable();
            $table->json('channels_failed')->nullable();
            
            $table->timestamps();
            
            // morphs already creates index for notifiable_type and notifiable_id
            $table->index('category');
            $table->index('read_at');
            $table->index('created_at');
        });
        
        // User preferences
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained()->cascadeOnDelete();
            
            // Channel preferences
            $table->boolean('channel_database')->default(true);
            $table->boolean('channel_email')->default(true);
            $table->boolean('channel_whatsapp')->default(false);
            $table->boolean('channel_push')->default(false);
            
            // Category preferences
            $table->json('categories')->nullable();
            
            // Quiet hours
            $table->boolean('quiet_hours_enabled')->default(false);
            $table->time('quiet_hours_start')->nullable();
            $table->time('quiet_hours_end')->nullable();
            
            // Digest mode
            $table->enum('digest_mode', ['instant', 'hourly', 'daily', 'weekly'])->default('instant');
            $table->time('digest_time')->default('08:00:00');
            
            $table->timestamps();
            
            $table->unique('user_id');
            $table->unique('member_id');
        });
        
        // Templates
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique();
            $table->string('name');
            $table->enum('category', ['loan', 'task', 'member', 'system', 'announcement']);
            
            // Templates
            $table->string('title_template', 500);
            $table->text('body_template');
            $table->string('email_subject')->nullable();
            $table->text('email_template')->nullable();
            $table->text('whatsapp_template')->nullable();
            
            // Placeholders
            $table->json('available_placeholders')->nullable();
            
            // Settings
            $table->boolean('is_active')->default(true);
            $table->enum('default_priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->json('default_channels')->nullable();
            
            $table->timestamps();
        });
        
        // Scheduled notifications
        Schema::create('notification_schedules', function (Blueprint $table) {
            $table->id();
            
            // Target
            $table->morphs('notifiable');
            
            // Template & Data
            $table->string('template_code', 100);
            $table->json('data')->nullable();
            
            // Schedule
            $table->timestamp('scheduled_at');
            $table->string('timezone', 50)->default('Asia/Jakarta');
            
            // Recurrence
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_rule')->nullable();
            $table->timestamp('recurrence_end_at')->nullable();
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'sent', 'failed', 'cancelled'])->default('pending');
            $table->integer('attempts')->default(0);
            $table->text('last_error')->nullable();
            
            // Reference
            $table->nullableMorphs('reference');
            
            $table->timestamps();
            
            $table->index('scheduled_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_schedules');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('staff_notifications');
    }
};
