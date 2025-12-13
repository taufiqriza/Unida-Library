<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->enum('registration_type', ['internal', 'external', 'public'])->default('public')->after('is_active');
            $table->string('institution')->nullable()->after('registration_type');
            $table->string('institution_city')->nullable()->after('institution');
            $table->enum('email_verified', ['pending', 'verified'])->default('verified')->after('institution_city');
            $table->timestamp('email_verified_at')->nullable()->after('email_verified');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['registration_type', 'institution', 'institution_city', 'email_verified', 'email_verified_at']);
        });
    }
};
