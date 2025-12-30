<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE chat_messages MODIFY COLUMN type ENUM('text', 'system', 'voice', 'bot') DEFAULT 'text'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE chat_messages MODIFY COLUMN type ENUM('text', 'system', 'voice') DEFAULT 'text'");
    }
};
