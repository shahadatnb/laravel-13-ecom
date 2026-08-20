<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupon_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_excluded')->default(false);
            $table->timestamps();

            $table->unique(['coupon_id', 'product_id', 'is_excluded']);
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_products');
    }
};
