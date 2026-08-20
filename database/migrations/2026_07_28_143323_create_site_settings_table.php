<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->string('label')->nullable();
            $table->string('type')->default('text'); // text, textarea, image, json
            $table->timestamps();
        });

        // Insert default settings
        $now = now();
        $defaults = [
            ['key' => 'site_name', 'value' => 'E-Commerce', 'group' => 'general', 'label' => 'Site Name', 'type' => 'text', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'site_description', 'value' => 'Your trusted online shopping destination for quality products at great prices.', 'group' => 'general', 'label' => 'Site Description', 'type' => 'textarea', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact_phone', 'value' => '+880 1234 567890', 'group' => 'contact', 'label' => 'Contact Phone', 'type' => 'text', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact_email', 'value' => 'info@ecommerce.com', 'group' => 'contact', 'label' => 'Contact Email', 'type' => 'text', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact_address', 'value' => 'Dhaka, Bangladesh', 'group' => 'contact', 'label' => 'Contact Address', 'type' => 'text', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'footer_quick_links', 'value' => json_encode([['label' => 'About Us', 'url' => '/about'], ['label' => 'Contact', 'url' => '/contact'], ['label' => 'Products', 'url' => '/products']]), 'group' => 'footer', 'label' => 'Footer Quick Links', 'type' => 'json', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'footer_customer_service_links', 'value' => json_encode([['label' => 'FAQ', 'url' => '#'], ['label' => 'Shipping Info', 'url' => '#'], ['label' => 'Returns', 'url' => '#']]), 'group' => 'footer', 'label' => 'Footer Customer Service Links', 'type' => 'json', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'trust_features', 'value' => json_encode([
                ['icon' => '🚚', 'title' => 'Free Shipping', 'description' => 'Free shipping on all orders over $50', 'color' => 'blue'],
                ['icon' => '🔒', 'title' => 'Secure Payment', 'description' => '100% secure payment processing', 'color' => 'green'],
                ['icon' => '↩️', 'title' => 'Easy Returns', 'description' => '30-day hassle-free return policy', 'color' => 'orange'],
                ['icon' => '💬', 'title' => '24/7 Support', 'description' => 'Round-the-clock customer support', 'color' => 'purple'],
            ]), 'group' => 'content', 'label' => 'Trust Features', 'type' => 'json', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'trusted_brands', 'value' => json_encode(['Nike', 'Apple', 'Samsung', 'Sony', 'LG', 'Philips']), 'group' => 'content', 'label' => 'Trusted Brands', 'type' => 'json', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('site_settings')->insert($defaults);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
