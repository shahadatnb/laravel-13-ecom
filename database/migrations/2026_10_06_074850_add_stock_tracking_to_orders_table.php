<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Set when stock was deducted for this order; null marks legacy
            // orders created before stock tracking, which must never restock.
            $table->timestamp('stock_deducted_at')->nullable()->after('shipping_status');
            $table->timestamp('stock_restocked_at')->nullable()->after('stock_deducted_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['stock_deducted_at', 'stock_restocked_at']);
        });
    }
};
