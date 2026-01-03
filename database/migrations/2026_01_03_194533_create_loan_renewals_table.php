<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->date('old_due_date');
            $table->date('new_due_date');
            $table->integer('renewal_number');
            $table->enum('source', ['online', 'staff'])->default('online');
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['loan_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_renewals');
    }
};
