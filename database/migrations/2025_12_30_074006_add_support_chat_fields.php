<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update type enum to include 'support'
        DB::statement("ALTER TABLE chat_rooms MODIFY COLUMN type ENUM('direct', 'branch', 'global', 'support') DEFAULT 'direct'");
        
        Schema::table('chat_rooms', function (Blueprint $table) {
            $table->foreignId('member_id')->nullable()->after('type')->constrained('users')->nullOnDelete();
            $table->string('topic')->nullable()->after('member_id');
            $table->enum('status', ['open', 'resolved'])->default('open')->after('topic');
            $table->foreignId('last_staff_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('chat_rooms', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropForeign(['last_staff_id']);
            $table->dropColumn(['member_id', 'topic', 'status', 'last_staff_id']);
        });
        
        DB::statement("ALTER TABLE chat_rooms MODIFY COLUMN type ENUM('direct', 'branch', 'global') DEFAULT 'direct'");
    }
};
