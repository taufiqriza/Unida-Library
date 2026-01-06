<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_room_members', function (Blueprint $table) {
            $table->timestamp('cleared_at')->nullable()->after('last_read_at');
        });
    }

    public function down(): void
    {
        Schema::table('chat_room_members', function (Blueprint $table) {
            $table->dropColumn('cleared_at');
        });
    }
};
