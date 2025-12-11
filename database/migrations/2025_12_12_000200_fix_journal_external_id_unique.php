<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_articles', function (Blueprint $table) {
            // Drop old unique constraint
            $table->dropUnique(['external_id']);
            
            // Add composite unique (source_type + external_id)
            $table->unique(['source_type', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::table('journal_articles', function (Blueprint $table) {
            $table->dropUnique(['source_type', 'external_id']);
            $table->unique('external_id');
        });
    }
};
