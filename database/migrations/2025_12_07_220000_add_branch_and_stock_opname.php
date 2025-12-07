<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add branch_id to fines
        Schema::table('fines', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('member_id')->constrained();
        });

        // Stock Opname (Inventory Check)
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('code')->unique();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['draft', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->integer('total_items')->default(0);
            $table->integer('found_items')->default(0);
            $table->integer('missing_items')->default(0);
            $table->integer('damaged_items')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Stock Opname Items
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained();
            $table->enum('status', ['pending', 'found', 'missing', 'damaged'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('checked_by')->nullable()->constrained('users');
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
        Schema::dropIfExists('stock_opnames');
        Schema::table('fines', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
