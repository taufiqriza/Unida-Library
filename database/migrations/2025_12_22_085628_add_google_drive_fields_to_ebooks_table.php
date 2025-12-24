<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebooks', function (Blueprint $table) {
            $table->enum('file_source', ['local', 'google_drive'])->default('local')->after('file_path');
            $table->string('google_drive_id', 255)->nullable()->after('file_source');
            $table->text('google_drive_url')->nullable()->after('google_drive_id');
        });
    }

    public function down(): void
    {
        Schema::table('ebooks', function (Blueprint $table) {
            $table->dropColumn(['file_source', 'google_drive_id', 'google_drive_url']);
        });
    }
};
