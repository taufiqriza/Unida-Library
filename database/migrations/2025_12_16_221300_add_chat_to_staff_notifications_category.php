<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'chat' to the category enum
        DB::statement("ALTER TABLE staff_notifications MODIFY COLUMN category ENUM('loan', 'task', 'member', 'system', 'announcement', 'chat') NOT NULL DEFAULT 'system'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE staff_notifications MODIFY COLUMN category ENUM('loan', 'task', 'member', 'system', 'announcement') NOT NULL DEFAULT 'system'");
    }
};
