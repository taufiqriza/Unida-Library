<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Branches (Multi-cabang)
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->boolean('is_main')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Master: Publishers
        Schema::create('publishers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city')->nullable();
            $table->timestamps();
        });

        // Master: Authors
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['personal', 'organizational', 'conference'])->default('personal');
            $table->timestamps();
        });

        // Master: Subjects/Topics
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('classification')->nullable();
            $table->timestamps();
        });

        // Master: Locations (Rak)
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20);
            $table->string('name');
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Master: Collection Types
        Schema::create('collection_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->nullable();
            $table->string('name');
            $table->timestamps();
        });

        // Master: Media Types (GMD)
        Schema::create('media_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->nullable();
            $table->timestamps();
        });

        // Master: Member Types
        Schema::create('member_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('loan_limit')->default(3);
            $table->integer('loan_period')->default(7);
            $table->decimal('fine_per_day', 10, 2)->default(0);
            $table->integer('membership_period')->default(365);
            $table->timestamps();
        });

        // Books (Biblio)
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained();
            $table->string('title');
            $table->string('isbn', 20)->nullable()->index();
            $table->foreignId('publisher_id')->nullable()->constrained()->nullOnDelete();
            $table->string('publish_year', 4)->nullable();
            $table->string('publish_place')->nullable();
            $table->string('edition')->nullable();
            $table->string('collation')->nullable();
            $table->string('series_title')->nullable();
            $table->string('call_number', 50)->nullable()->index();
            $table->text('notes')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('media_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('language', 10)->default('id');
            $table->text('abstract')->nullable();
            $table->boolean('is_opac_visible')->default(true);
            $table->timestamps();
        });

        // Book Authors (pivot)
        Schema::create('book_author', function (Blueprint $table) {
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained()->cascadeOnDelete();
            $table->integer('level')->default(1);
            $table->primary(['book_id', 'author_id']);
        });

        // Book Subjects (pivot)
        Schema::create('book_subject', function (Blueprint $table) {
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->primary(['book_id', 'subject_id']);
        });

        // Items (Eksemplar)
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained();
            $table->string('barcode', 30)->unique();
            $table->foreignId('collection_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['available', 'on_loan', 'reserved', 'damaged', 'lost', 'repair'])->default('available');
            $table->string('inventory_code', 50)->nullable();
            $table->date('received_date')->nullable();
            $table->string('source')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->timestamps();
        });

        // Members
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained();
            $table->string('member_id', 30)->unique();
            $table->string('name');
            $table->enum('gender', ['M', 'F'])->nullable();
            $table->date('birth_date')->nullable();
            $table->string('identity_number', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->foreignId('member_type_id')->constrained();
            $table->date('register_date');
            $table->date('expire_date');
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Loans (Peminjaman)
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('member_id')->constrained();
            $table->foreignId('item_id')->constrained();
            $table->date('loan_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->boolean('is_returned')->default(false);
            $table->integer('extend_count')->default(0);
            $table->timestamps();
        });

        // Fines (Denda)
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained();
            $table->foreignId('member_id')->constrained();
            $table->decimal('amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
        });

        // News/Articles (CMS)
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('image')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // Pages (Static pages)
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('fines');
        Schema::dropIfExists('loans');
        Schema::dropIfExists('members');
        Schema::dropIfExists('items');
        Schema::dropIfExists('book_subject');
        Schema::dropIfExists('book_author');
        Schema::dropIfExists('books');
        Schema::dropIfExists('member_types');
        Schema::dropIfExists('media_types');
        Schema::dropIfExists('collection_types');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('publishers');
        Schema::dropIfExists('branches');
    }
};
