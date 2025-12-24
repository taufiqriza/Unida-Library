<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support ENUM modification, skip for SQLite
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE staff_notifications MODIFY COLUMN category ENUM('loan', 'task', 'member', 'system', 'announcement', 'chat') NOT NULL DEFAULT 'system'");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE staff_notifications MODIFY COLUMN category ENUM('loan', 'task', 'member', 'system', 'announcement') NOT NULL DEFAULT 'system'");
        }
    }
};
