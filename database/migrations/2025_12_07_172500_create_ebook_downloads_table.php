<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ebook Categories
        Schema::create('ebook_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add category to ebooks
        Schema::table('ebooks', function (Blueprint $table) {
            $table->foreignId('ebook_category_id')->nullable()->after('content_type_id')->constrained()->nullOnDelete();
        });

        // Download history
        Schema::create('ebook_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ebook_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebook_downloads');
        Schema::table('ebooks', function (Blueprint $table) {
            $table->dropForeign(['ebook_category_id']);
            $table->dropColumn('ebook_category_id');
        });
        Schema::dropIfExists('ebook_categories');
    }
};
