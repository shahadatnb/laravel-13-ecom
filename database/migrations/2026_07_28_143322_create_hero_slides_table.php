<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('cta_text')->default('Shop Now');
            $table->string('cta_link')->default('/products');
            $table->string('bg_gradient')->default('from-blue-600 via-blue-700 to-indigo-900');
            $table->string('image_emoji')->default('🎉');
            $table->string('badge_text')->nullable()->default('Limited Time Offer');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
