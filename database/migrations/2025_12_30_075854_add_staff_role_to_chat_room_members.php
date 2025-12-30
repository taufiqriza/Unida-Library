<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE chat_room_members MODIFY COLUMN role ENUM('admin', 'member', 'staff') DEFAULT 'member'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE chat_room_members MODIFY COLUMN role ENUM('admin', 'member') DEFAULT 'member'");
    }
};
