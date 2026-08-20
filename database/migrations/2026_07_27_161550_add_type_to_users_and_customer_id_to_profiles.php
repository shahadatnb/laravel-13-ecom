<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('type', ['admin', 'staff'])->default('staff')->after('remember_token');
            $table->index('type');
        });

        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('id')
                ->constrained('customers')->cascadeOnUpdate()->restrictOnDelete();
            $table->index('customer_id');
        });

        Schema::table('wallets', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('user_id')
                ->constrained('customers')->cascadeOnUpdate()->restrictOnDelete();
            $table->index('customer_id');
        });

        Schema::table('user_addresses', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('user_id')
                ->constrained('customers')->cascadeOnUpdate()->restrictOnDelete();
            $table->index('customer_id');
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('user_id')
                ->constrained('customers')->cascadeOnUpdate()->restrictOnDelete();
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex(['customer_id']);
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });

        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropIndex(['customer_id']);
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });

        Schema::table('wallets', function (Blueprint $table) {
            $table->dropIndex(['customer_id']);
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });

        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->dropIndex(['customer_id']);
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn('type');
        });
    }
};
