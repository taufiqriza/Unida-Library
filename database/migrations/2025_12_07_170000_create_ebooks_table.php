<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ebooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('title');
            $table->string('sor')->nullable()->comment('Statement of Responsibility');
            $table->foreignId('publisher_id')->nullable()->constrained()->nullOnDelete();
            $table->string('publish_year', 4)->nullable();
            $table->string('isbn', 20)->nullable();
            $table->string('edition')->nullable();
            $table->string('pages')->nullable();
            $table->string('file_size')->nullable();
            $table->string('file_format', 20)->nullable()->comment('PDF, EPUB, etc');
            $table->string('file_path')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('language', 10)->default('id');
            $table->text('abstract')->nullable();
            $table->string('classification', 40)->nullable();
            $table->string('call_number', 50)->nullable();
            $table->foreignId('media_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('content_type_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('access_type', ['public', 'member', 'restricted'])->default('member');
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('opac_hide')->default(false);
            $table->timestamps();
        });

        // Pivot: ebook_author
        Schema::create('ebook_author', function (Blueprint $table) {
            $table->foreignId('ebook_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained()->cascadeOnDelete();
            $table->integer('level')->default(1);
            $table->primary(['ebook_id', 'author_id']);
        });

        // Pivot: ebook_subject
        Schema::create('ebook_subject', function (Blueprint $table) {
            $table->foreignId('ebook_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->primary(['ebook_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebook_subject');
        Schema::dropIfExists('ebook_author');
        Schema::dropIfExists('ebooks');
    }
};
