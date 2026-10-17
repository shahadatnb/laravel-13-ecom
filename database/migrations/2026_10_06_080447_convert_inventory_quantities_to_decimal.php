<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->decimal('current_stock', 10, 3)->default(0)->change();
            $table->decimal('reserved_stock', 10, 3)->default(0)->change();
            $table->decimal('minimum_stock', 10, 3)->default(0)->change();
            $table->decimal('maximum_stock', 10, 3)->nullable()->change();
            $table->decimal('reorder_level', 10, 3)->default(0)->change();
            // storedAs columns cannot change type in place
            $table->dropColumn('available_stock');
        });

        Schema::table('inventory', function (Blueprint $table) {
            $table->decimal('available_stock', 10, 3)->storedAs('current_stock - reserved_stock');
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->decimal('quantity_before', 10, 3)->default(0)->change();
            $table->decimal('quantity_change', 10, 3)->change();
            $table->decimal('quantity_after', 10, 3)->default(0)->change();
        });

        Schema::table('inventory_adjustments', function (Blueprint $table) {
            $table->decimal('quantity_before', 10, 3)->change();
            $table->decimal('quantity_change', 10, 3)->change();
            $table->decimal('quantity_after', 10, 3)->change();
        });
    }

    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->integer('current_stock')->default(0)->change();
            $table->integer('reserved_stock')->default(0)->change();
            $table->integer('minimum_stock')->default(0)->change();
            $table->integer('maximum_stock')->nullable()->change();
            $table->integer('reorder_level')->default(0)->change();
            $table->dropColumn('available_stock');
        });

        Schema::table('inventory', function (Blueprint $table) {
            $table->integer('available_stock')->storedAs('current_stock - reserved_stock');
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->integer('quantity_before')->default(0)->change();
            $table->integer('quantity_change')->change();
            $table->integer('quantity_after')->default(0)->change();
        });

        Schema::table('inventory_adjustments', function (Blueprint $table) {
            $table->integer('quantity_before')->change();
            $table->integer('quantity_change')->change();
            $table->integer('quantity_after')->change();
        });
    }
};
