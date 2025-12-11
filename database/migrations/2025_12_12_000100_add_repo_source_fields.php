<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add source fields to etheses
        Schema::table('etheses', function (Blueprint $table) {
            $table->string('source_type', 20)->default('local')->after('id'); // local, repo
            $table->string('external_id')->nullable()->after('source_type');
            $table->string('external_url', 500)->nullable()->after('external_id');
            
            $table->index('source_type');
            $table->index('external_id');
        });

        // Add source fields to journal_articles
        Schema::table('journal_articles', function (Blueprint $table) {
            $table->string('source_type', 20)->default('ojs')->after('id'); // ojs, repo
            
            $table->index('source_type');
        });
    }

    public function down(): void
    {
        Schema::table('etheses', function (Blueprint $table) {
            $table->dropIndex(['source_type']);
            $table->dropIndex(['external_id']);
            $table->dropColumn(['source_type', 'external_id', 'external_url']);
        });

        Schema::table('journal_articles', function (Blueprint $table) {
            $table->dropIndex(['source_type']);
            $table->dropColumn('source_type');
        });
    }
};
