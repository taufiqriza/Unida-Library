<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('pddikti_id')->nullable()->after('id')->comment('Linked PDDikti record ID');
            $table->string('nim_nidn')->nullable()->after('member_id')->comment('NIM or NIDN from PDDikti');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['pddikti_id', 'nim_nidn']);
        });
    }
};
