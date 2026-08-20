<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventory')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 30); // purchase, sale, return, refund, adjustment, transfer_in, transfer_out, damage, expired, opening_balance
            $table->string('reference_type', 50)->nullable(); // order, purchase_order, transfer, adjustment
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->integer('quantity_before')->default(0);
            $table->integer('quantity_change');
            $table->integer('quantity_after')->default(0);
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->string('status', 20)->default('completed'); // pending, completed, cancelled, reversed
            $table->text('reason')->nullable();
            $table->string('batch_number', 100)->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('created_by_type', 20)->nullable(); // admin, system, api
            $table->timestamps();

            $table->index('type');
            $table->index('reference_type');
            $table->index('reference_id');
            $table->index('created_at');
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
