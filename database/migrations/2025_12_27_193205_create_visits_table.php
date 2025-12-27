<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('member_id')->nullable()->constrained()->onDelete('set null');
            $table->string('guest_name', 100)->nullable();
            $table->string('guest_institution', 100)->nullable();
            $table->enum('visitor_type', ['member', 'guest'])->default('member');
            $table->enum('purpose', ['baca', 'pinjam', 'belajar', 'penelitian', 'lainnya'])->default('baca');
            $table->timestamp('visited_at');
            $table->timestamps();

            $table->index(['branch_id', 'visited_at']);
            $table->index(['member_id', 'visited_at']);
            $table->index('visited_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
