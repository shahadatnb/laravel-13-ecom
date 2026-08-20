<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Make user_id nullable for guest checkout
            $table->foreignId('user_id')->nullable()->change();

            // Add guest_email for non-registered customers
            $table->string('guest_email', 255)->nullable()->after('user_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('guest_email');

            // Revert user_id to non-nullable (this may fail if there are guest orders)
            // $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
