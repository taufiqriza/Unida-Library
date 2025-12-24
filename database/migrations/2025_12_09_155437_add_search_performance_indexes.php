<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Books indexes
        $this->addIndexIfNotExists('books', 'title');
        $this->addIndexIfNotExists('books', 'isbn');
        $this->addIndexIfNotExists('books', 'call_number');
        $this->addIndexIfNotExists('books', 'publish_year');

        // Etheses indexes
        $this->addIndexIfNotExists('etheses', 'author');
        $this->addIndexIfNotExists('etheses', 'nim');
        $this->addIndexIfNotExists('etheses', 'year');
        $this->addIndexIfNotExists('etheses', 'is_public');

        // Members indexes
        $this->addIndexIfNotExists('members', 'member_id');
        $this->addIndexIfNotExists('members', 'email');
        $this->addIndexIfNotExists('members', 'is_active');

        // Items indexes
        $this->addIndexIfNotExists('items', 'barcode');
        $this->addIndexIfNotExists('items', 'item_status_id');

        // Loans indexes
        $this->addIndexIfNotExists('loans', 'is_returned');
        $this->addIndexIfNotExists('loans', 'due_date');
    }

    public function down(): void
    {
        // Indexes will remain - safe to keep
    }

    protected function addIndexIfNotExists(string $table, string $column): void
    {
        if (!Schema::hasColumn($table, $column)) {
            return;
        }

        $indexName = "{$table}_{$column}_index";
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'sqlite') {
            $exists = DB::select("SELECT name FROM sqlite_master WHERE type='index' AND name=?", [$indexName]);
        } else {
            $exists = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        }
        
        if (empty($exists)) {
            Schema::table($table, fn(Blueprint $t) => $t->index($column));
        }
    }
};
