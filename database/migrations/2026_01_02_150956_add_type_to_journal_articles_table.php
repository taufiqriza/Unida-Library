<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_articles', function (Blueprint $table) {
            $table->string('type', 20)->default('article')->after('source_type');
        });
    }

    public function down(): void
    {
        Schema::table('journal_articles', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
