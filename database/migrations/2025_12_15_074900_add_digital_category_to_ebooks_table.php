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
        Schema::table('ebooks', function (Blueprint $table) {
            if (!Schema::hasColumn('ebooks', 'collection_type')) {
                $table->string('collection_type')->nullable()->after('digital_category_id')
                      ->comment('regular, universitaria, premium');
            }
            if (!Schema::hasColumn('ebooks', 'is_downloadable')) {
                $table->boolean('is_downloadable')->default(true)->after('opac_hide')
                      ->comment('false = read only, no download');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ebooks', function (Blueprint $table) {
            $table->dropColumn(['collection_type', 'is_downloadable']);
        });
    }
};
