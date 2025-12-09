<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('thesis_submissions', function (Blueprint $table) {
            // Rename abstract_file to preview_file if exists
            if (Schema::hasColumn('thesis_submissions', 'abstract_file')) {
                $table->renameColumn('abstract_file', 'preview_file');
            } elseif (!Schema::hasColumn('thesis_submissions', 'preview_file')) {
                $table->string('preview_file')->nullable()->after('approval_file');
            }

            // Add visibility columns if not exist
            if (!Schema::hasColumn('thesis_submissions', 'cover_visible')) {
                $table->boolean('cover_visible')->default(true)->after('allow_fulltext_public');
            }
            if (!Schema::hasColumn('thesis_submissions', 'approval_visible')) {
                $table->boolean('approval_visible')->default(false)->after('cover_visible');
            }
            if (!Schema::hasColumn('thesis_submissions', 'preview_visible')) {
                $table->boolean('preview_visible')->default(true)->after('approval_visible');
            }
            if (!Schema::hasColumn('thesis_submissions', 'fulltext_visible')) {
                $table->boolean('fulltext_visible')->default(false)->after('preview_visible');
            }
        });
    }

    public function down(): void
    {
        Schema::table('thesis_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('thesis_submissions', 'preview_file')) {
                $table->renameColumn('preview_file', 'abstract_file');
            }
            
            $table->dropColumn(['cover_visible', 'approval_visible', 'preview_visible', 'fulltext_visible']);
        });
    }
};
