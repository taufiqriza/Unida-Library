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
        Schema::table('etheses', function (Blueprint $table) {
            $table->longText('searchable_content')->nullable()->after('keywords');
            $table->timestamp('content_indexed_at')->nullable()->after('searchable_content');
            $table->index(['is_public', 'content_indexed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etheses', function (Blueprint $table) {
            $table->dropIndex(['is_public', 'content_indexed_at']);
            $table->dropColumn(['searchable_content', 'content_indexed_at']);
        });
    }
};
