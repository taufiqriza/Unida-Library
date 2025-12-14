<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('journal_sources', function (Blueprint $table) {
            if (!Schema::hasColumn('journal_sources', 'cover_url')) {
                $table->string('cover_url')->nullable()->after('feed_url');
            }
            if (!Schema::hasColumn('journal_sources', 'sinta_rank')) {
                $table->unsignedTinyInteger('sinta_rank')->nullable()->after('issn');
            }
            if (!Schema::hasColumn('journal_sources', 'description')) {
                $table->text('description')->nullable()->after('sinta_rank');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_sources', function (Blueprint $table) {
            if (Schema::hasColumn('journal_sources', 'cover_url')) {
                $table->dropColumn('cover_url');
            }
            if (Schema::hasColumn('journal_sources', 'sinta_rank')) {
                $table->dropColumn('sinta_rank');
            }
            if (Schema::hasColumn('journal_sources', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
