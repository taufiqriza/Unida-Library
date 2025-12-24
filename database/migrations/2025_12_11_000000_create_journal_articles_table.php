<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_articles', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique(); // OJS article ID from URL
            $table->string('journal_code', 50)->index(); // e.g., 'tsaqafah'
            $table->string('journal_name');
            $table->string('title', 1000);
            $table->text('abstract')->nullable();
            $table->json('authors')->nullable(); // [{name, email}]
            $table->string('doi')->nullable()->index();
            $table->string('volume', 20)->nullable();
            $table->string('issue', 20)->nullable();
            $table->string('pages', 50)->nullable();
            $table->year('publish_year')->nullable()->index();
            $table->date('published_at')->nullable();
            $table->string('url', 500);
            $table->string('pdf_url', 500)->nullable();
            $table->json('keywords')->nullable();
            $table->string('language', 10)->default('id');
            $table->string('rights')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            
            // Fulltext index only for MySQL
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->fullText(['title', 'abstract']);
            }
        });

        Schema::create('journal_sources', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // e.g., 'tsaqafah'
            $table->string('name');
            $table->string('base_url');
            $table->string('feed_type')->default('atom'); // atom, rss, oai
            $table->string('feed_url');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->integer('article_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_articles');
        Schema::dropIfExists('journal_sources');
    }
};
