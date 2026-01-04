<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plagiarism_checks', function (Blueprint $table) {
            $table->text('external_report_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('plagiarism_checks', function (Blueprint $table) {
            $table->string('external_report_url', 500)->nullable()->change();
        });
    }
};
