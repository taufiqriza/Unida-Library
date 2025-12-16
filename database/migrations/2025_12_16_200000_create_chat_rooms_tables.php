<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Chat Rooms (Conversations)
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            
            // Room Type: direct (1-to-1), branch (per cabang), global (semua staff)
            $table->enum('type', ['direct', 'branch', 'global'])->default('direct');
            
            // Room Info (for groups)
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('color', 20)->nullable();
            
            // References
            $table->foreignId('branch_id')->nullable()->constrained()->cascadeOnDelete();
            
            // Settings
            $table->boolean('is_archived')->default(false);
            
            $table->timestamps();
            
            // Ensure only 1 branch room per branch, only 1 global room
            $table->unique(['branch_id', 'type']);
            $table->index('type');
        });

        // Chat Room Members
        Schema::create('chat_room_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Member Role in Room
            $table->enum('role', ['admin', 'member'])->default('member');
            
            // Notifications
            $table->boolean('is_muted')->default(false);
            
            // Tracking
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('last_read_at')->nullable();
            
            $table->timestamps();
            
            $table->unique(['chat_room_id', 'user_id']);
            $table->index('user_id');
        });

        // Chat Messages (replaces staff_messages for groups)
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            
            // Message Content
            $table->text('message')->nullable();
            
            // Attachments
            $table->string('attachment')->nullable();
            $table->string('attachment_type', 20)->nullable(); // image, file
            $table->string('attachment_name')->nullable();
            
            // Message Type
            $table->enum('type', ['text', 'system'])->default('text');
            
            // Status
            $table->boolean('is_deleted')->default(false);
            
            $table->timestamps();
            
            $table->index(['chat_room_id', 'created_at']);
            $table->index('sender_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_room_members');
        Schema::dropIfExists('chat_rooms');
    }
};
