<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_updates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('type')->default('feature'); // feature, improvement, fix, announcement
            $table->string('icon')->nullable();
            $table->string('color')->default('blue'); // blue, green, purple, amber, red
            $table->json('target_roles')->nullable(); // ['staff', 'admin', 'librarian'] or null for all
            $table->boolean('is_active')->default(true);
            $table->boolean('is_dismissible')->default(true);
            $table->integer('priority')->default(0); // higher = more important
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_updates');
    }
};
