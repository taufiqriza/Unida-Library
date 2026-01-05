<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add pin and forward columns to chat_messages
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->boolean('is_pinned')->default(false)->after('is_deleted');
            $table->foreignId('pinned_by')->nullable()->after('is_pinned')->constrained('users')->nullOnDelete();
            $table->timestamp('pinned_at')->nullable()->after('pinned_by');
            $table->foreignId('forwarded_from_id')->nullable()->after('pinned_at')->constrained('chat_messages')->nullOnDelete();
        });
        
        // Create reactions table
        Schema::create('chat_message_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('chat_messages')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('emoji', 10);
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['message_id', 'user_id', 'emoji']);
        });
        
        // Create mentions table
        Schema::create('chat_mentions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('chat_messages')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['message_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_mentions');
        Schema::dropIfExists('chat_message_reactions');
        
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign(['pinned_by']);
            $table->dropForeign(['forwarded_from_id']);
            $table->dropColumn(['is_pinned', 'pinned_by', 'pinned_at', 'forwarded_from_id']);
        });
    }
};
