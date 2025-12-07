<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Add missing SLiMS fields
            $table->string('sor', 200)->nullable()->after('title')->comment('Statement of Responsibility');
            $table->string('isbn', 20)->nullable()->change();
            $table->string('classification', 40)->nullable()->after('call_number');
            $table->text('spec_detail_info')->nullable()->after('abstract')->comment('Specific Detail Info for serials');
            $table->boolean('opac_hide')->default(false)->after('is_opac_visible');
            $table->boolean('promoted')->default(false)->after('opac_hide');
            $table->text('labels')->nullable()->after('promoted');
            $table->foreignId('frequency_id')->nullable()->after('media_type_id');
            $table->foreignId('content_type_id')->nullable()->after('frequency_id');
            $table->foreignId('carrier_type_id')->nullable()->after('content_type_id');
            $table->foreignId('place_id')->nullable()->after('publisher_id')->comment('Publish place');
            $table->foreignId('user_id')->nullable()->after('branch_id')->comment('Input by');
            $table->timestamp('input_date')->nullable();
        });

        // Master: Places
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Master: Frequencies (for serials)
        Schema::create('frequencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('time_increment')->default(1);
            $table->string('time_unit', 20)->nullable();
            $table->string('language_prefix', 5)->nullable();
            $table->timestamps();
        });

        // Master: Content Types (RDA)
        Schema::create('content_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->nullable();
            $table->timestamps();
        });

        // Master: Carrier Types (RDA)
        Schema::create('carrier_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->nullable();
            $table->timestamps();
        });

        // Master: Item Status
        Schema::create('item_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10);
            $table->string('name');
            $table->string('rules')->nullable();
            $table->boolean('no_loan')->default(false);
            $table->timestamps();
        });

        // Master: Suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        // Update items table
        Schema::table('items', function (Blueprint $table) {
            $table->string('call_number', 50)->nullable()->after('barcode');
            $table->foreignId('item_status_id')->nullable()->after('status');
            $table->foreignId('supplier_id')->nullable()->after('source');
            $table->string('order_no', 20)->nullable();
            $table->date('order_date')->nullable();
            $table->string('invoice', 20)->nullable();
            $table->date('invoice_date')->nullable();
            $table->string('site', 50)->nullable()->comment('Site/Lokasi fisik');
            $table->foreignId('user_id')->nullable();
            $table->dropColumn('status');
        });

        // Book Attachments
        Schema::create('book_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->enum('access_type', ['public', 'private'])->default('public');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_attachments');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('item_statuses');
        Schema::dropIfExists('carrier_types');
        Schema::dropIfExists('content_types');
        Schema::dropIfExists('frequencies');
        Schema::dropIfExists('places');
    }
};
