<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $this->upsert('site_name', 'MyVoucher');
        $this->upsert('site_description', 'Your trusted online shopping destination for quality products at great prices in Bangladesh.');
        $this->upsert('contact_phone', '+880 1700-123456');
        $this->upsert('contact_email', 'support@myvoucher.com');
        $this->upsert('contact_address', 'Gulshan-2, Dhaka 1212, Bangladesh');
        $this->upsert('active_theme', 'modern');
        $this->upsert('logo', '');
        $this->upsert('favicon', '');
        $this->upsert('og_image', '');
        $this->upsert('currency_symbol', '৳');
        $this->upsert('currency_code', 'BDT');
        $this->upsert('currency_position', 'before');
        $this->upsert('currency_decimals', '0');
        $this->upsert('currency_thousand_separator', ',');
        $this->upsert('currency_decimal_separator', '.');
        $this->upsert('tax_rate', '0');
        $this->upsert('free_shipping_threshold', '5000');
        $this->upsert('shipping_rate', '100');

        $this->upsert('footer_quick_links', json_encode([
            ['label' => 'About Us', 'url' => '/page/about-us'],
            ['label' => 'Contact', 'url' => '/contact'],
            ['label' => 'Products', 'url' => '/products'],
            ['label' => 'Categories', 'url' => '/categories'],
        ]));

        $this->upsert('footer_customer_service_links', json_encode([
            ['label' => 'FAQ', 'url' => '/page/faq'],
            ['label' => 'Shipping Policy', 'url' => '/page/shipping-policy'],
            ['label' => 'Return Policy', 'url' => '/page/return-policy'],
            ['label' => 'Privacy Policy', 'url' => '/page/privacy-policy'],
            ['label' => 'Terms & Conditions', 'url' => '/page/terms'],
        ]));

        $this->upsert('trust_features', json_encode([
            ['icon' => '🚚', 'title' => 'Free Shipping', 'description' => 'Free delivery on orders over ৳5,000', 'color' => 'emerald'],
            ['icon' => '🔒', 'title' => 'Secure Payment', 'description' => '100% secure checkout with SSL encryption', 'color' => 'blue'],
            ['icon' => '↩️', 'title' => 'Easy Returns', 'description' => '7-day hassle-free return policy', 'color' => 'amber'],
            ['icon' => '💬', 'title' => '24/7 Support', 'description' => 'Round-the-clock customer support via phone & chat', 'color' => 'violet'],
        ]));

        $this->upsert('trusted_brands', json_encode([
            'Walton', 'Samsung', 'Apple', 'Xiaomi', 'Sony', 'Philips',
        ]));

        $this->seedHeroSlides();

        $this->command->info('✅ Site settings seeded successfully.');
    }

    private function upsert(string $key, string $value): void
    {
        SiteSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    private function seedHeroSlides(): void
    {
        if (HeroSlide::count() > 0) {
            return;
        }

        $slides = [
            [
                'title' => 'New Season Collection',
                'subtitle' => 'Discover the latest trends with amazing prices',
                'cta_text' => 'Shop Now',
                'cta_link' => '/products',
                'bg_gradient' => 'from-primary-700 via-primary-800 to-primary-900',
                'image_emoji' => '🛍️',
                'badge_text' => 'New Arrivals',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Mega Sale — Up to 40% Off',
                'subtitle' => 'Limited time offer on selected electronics & accessories',
                'cta_text' => 'View Deals',
                'cta_link' => '/products',
                'bg_gradient' => 'from-accent-600 via-accent-700 to-red-800',
                'image_emoji' => '🔥',
                'badge_text' => 'Hot Deal',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Premium Quality, Best Prices',
                'subtitle' => 'Shop from top brands with guaranteed authenticity',
                'cta_text' => 'Explore',
                'cta_link' => '/categories',
                'bg_gradient' => 'from-emerald-600 via-teal-700 to-cyan-800',
                'image_emoji' => '✨',
                'badge_text' => 'Top Brands',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($slides as $slide) {
            HeroSlide::create($slide);
        }

        $this->command->info('✅ Hero slides seeded successfully.');
    }
}
