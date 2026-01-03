<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->integer('renewal_count')->default(0)->after('is_returned');
            $table->integer('max_renewals')->default(2)->after('renewal_count');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['renewal_count', 'max_renewals']);
        });
    }
};
