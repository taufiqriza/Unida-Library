<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plagiarism_checks', function (Blueprint $table) {
            $table->enum('check_type', ['system', 'external'])->default('system')->after('status');
            $table->string('external_platform')->nullable()->after('check_type'); // turnitin, ithenticate, copyscape
            $table->string('external_report_file')->nullable()->after('external_platform');
            $table->text('review_notes')->nullable()->after('error_message');
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('review_notes');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            
            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('plagiarism_checks', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['check_type', 'external_platform', 'external_report_file', 'review_notes', 'reviewed_by', 'reviewed_at']);
        });
    }
};
