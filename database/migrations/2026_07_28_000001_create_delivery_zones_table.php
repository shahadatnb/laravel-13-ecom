<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['inside_dhaka', 'outside_dhaka'])->unique();
            $table->decimal('charge', 10, 2)->default(0);
            $table->decimal('minimum_order_amount', 10, 2)->nullable()->comment('Free delivery threshold');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_zones');
    }
};
