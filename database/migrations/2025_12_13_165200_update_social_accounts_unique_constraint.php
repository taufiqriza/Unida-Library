<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('social_accounts', function (Blueprint $table) {
            // Drop old unique constraint
            $table->dropUnique('social_accounts_provider_provider_id_unique');
            
            // Add new composite unique: provider + provider_id + user_id (nullable)
            // This allows same google account for different user types
            $table->unique(['provider', 'provider_id', 'user_id'], 'social_accounts_provider_user_unique');
            $table->unique(['provider', 'provider_id', 'member_id'], 'social_accounts_provider_member_unique');
        });
    }

    public function down(): void
    {
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->dropUnique('social_accounts_provider_user_unique');
            $table->dropUnique('social_accounts_provider_member_unique');
            $table->unique(['provider', 'provider_id']);
        });
    }
};
