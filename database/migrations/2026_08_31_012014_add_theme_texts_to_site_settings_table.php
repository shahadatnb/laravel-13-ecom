<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $themeTexts = [
            // Common
            'secure_checkout' => 'Secure Checkout',
            'secure_checkout_desc' => '100% secure checkout',
            'easy_returns' => 'Easy Returns',
            'easy_returns_desc' => '7-day return policy',
            'support_247' => '24/7 Support',
            'support_247_desc' => 'Dedicated support',
            'cash_on_delivery' => 'Cash on Delivery',
            'original_product' => 'Original Product',

            // Section titles
            'shop_by_category' => 'Shop by Category',
            'shop_by_category_subtitle' => 'Browse our top categories',
            'featured_products' => 'Featured Products',
            'featured_products_subtitle' => 'Handpicked items just for you',
            'new_arrivals' => 'New Arrivals',
            'new_arrivals_subtitle' => 'Latest products for you',
            'featured_deals' => 'Featured Deals',
            'featured_deals_subtitle' => 'Handpicked bargains for you',
            'shop_the_collection' => 'Shop the Collection',
            'collection_subtitle' => 'Every product is handpicked, tested, and guaranteed.',

            // Hero fallback (when no slides in DB)
            'hero_title' => 'Discover the Best Deals Online',
            'hero_subtitle' => 'Shop the latest trends with amazing prices. Quality products, fast delivery, and exceptional customer service.',
            'hero_cta' => 'Shop Now',

            // Urgency strip
            'urgency_flash_deals' => '⚡ Flash deals dropping daily',
            'urgency_easy_returns' => '🔄 Easy 7-day returns',
        ];

        foreach ($themeTexts as $key => $value) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => "theme_text_{$key}"],
                ['value' => $value, 'type' => 'text', 'group' => 'theme_texts', 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        DB::table('site_settings')->where('group', 'theme_texts')->delete();
    }
};
