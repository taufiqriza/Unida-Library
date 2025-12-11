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
        Schema::table('members', function (Blueprint $table) {
            if (!Schema::hasColumn('members', 'faculty_id')) {
                $table->foreignId('faculty_id')->nullable()->after('branch_id')->constrained('faculties')->nullOnDelete();
            }
            if (!Schema::hasColumn('members', 'department_id')) {
                $table->foreignId('department_id')->nullable()->after('faculty_id')->constrained('departments')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            }
            if (Schema::hasColumn('members', 'faculty_id')) {
                $table->dropForeign(['faculty_id']);
                $table->dropColumn('faculty_id');
            }
        });
    }
};
