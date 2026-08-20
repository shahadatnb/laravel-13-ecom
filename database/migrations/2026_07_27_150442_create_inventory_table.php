<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('sku', 100)->nullable()->index();
            $table->string('barcode', 100)->nullable()->index();
            $table->integer('current_stock')->default(0);
            $table->integer('reserved_stock')->default(0);
            $table->integer('available_stock')->storedAs('current_stock - reserved_stock');
            $table->integer('minimum_stock')->default(0);
            $table->integer('maximum_stock')->nullable();
            $table->integer('reorder_level')->default(0);
            $table->string('location', 100)->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'warehouse_id', 'product_variant_id'], 'inventory_unique');
            $table->index('warehouse_id');
            $table->index('available_stock');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
