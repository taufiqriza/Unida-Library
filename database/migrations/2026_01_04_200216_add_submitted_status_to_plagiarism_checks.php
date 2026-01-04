<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE plagiarism_checks MODIFY status ENUM('pending', 'processing', 'submitted', 'completed', 'failed') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE plagiarism_checks MODIFY status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending'");
    }
};
