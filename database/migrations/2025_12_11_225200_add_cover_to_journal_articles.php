<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_articles', function (Blueprint $table) {
            $table->string('cover_url', 500)->nullable()->after('pdf_url');
            $table->text('abstract_en')->nullable()->after('abstract');
            $table->string('issue_title')->nullable()->after('issue');
            $table->integer('views')->default(0)->after('rights');
        });

        Schema::table('journal_sources', function (Blueprint $table) {
            $table->string('sinta_rank', 10)->nullable()->after('name');
            $table->string('issn', 20)->nullable()->after('sinta_rank');
        });
    }

    public function down(): void
    {
        Schema::table('journal_articles', function (Blueprint $table) {
            $table->dropColumn(['cover_url', 'abstract_en', 'issue_title', 'views']);
        });

        Schema::table('journal_sources', function (Blueprint $table) {
            $table->dropColumn(['sinta_rank', 'issn']);
        });
    }
};
