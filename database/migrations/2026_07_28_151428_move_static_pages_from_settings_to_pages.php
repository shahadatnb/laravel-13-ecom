<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Read existing content from site_settings
        $settings = DB::table('site_settings')
            ->whereIn('key', ['about_us', 'faq', 'shipping_info', 'returns'])
            ->get(['key', 'value', 'label']);

        $now = now();
        $pageMap = [
            'about_us' => ['slug' => 'about-us', 'title' => 'About Us'],
            'faq' => ['slug' => 'faq', 'title' => 'FAQ'],
            'shipping_info' => ['slug' => 'shipping-info', 'title' => 'Shipping Info'],
            'returns' => ['slug' => 'returns', 'title' => 'Returns Policy'],
        ];

        foreach ($settings as $setting) {
            if (! isset($pageMap[$setting->key])) {
                continue;
            }

            $page = $pageMap[$setting->key];
            $slug = $page['slug'];

            // Check if a page with this slug already exists
            $existing = DB::table('pages')->where('slug', $slug)->first();

            if (! $existing) {
                DB::table('pages')->insert([
                    'title' => $page['title'],
                    'slug' => $slug,
                    'content' => $setting->value,
                    'meta_title' => $page['title'].' - E-Commerce',
                    'meta_description' => null,
                    'status' => 'published',
                    'sort_order' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // Delete old settings from site_settings
        DB::table('site_settings')
            ->whereIn('key', ['about_us', 'faq', 'shipping_info', 'returns'])
            ->delete();
    }

    public function down(): void
    {
        // Delete the pages we created
        DB::table('pages')->whereIn('slug', ['about-us', 'faq', 'shipping-info', 'returns'])->delete();

        // Re-insert defaults (we don't have the original values, so insert generic ones)
        $now = now();
        $defaults = [
            ['key' => 'about_us', 'value' => '<h2>About Us</h2><p>Your trusted online store.</p>', 'group' => 'pages', 'label' => 'About Us', 'type' => 'richtext', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'faq', 'value' => '<h2>FAQ</h2><p>Frequently asked questions.</p>', 'group' => 'pages', 'label' => 'FAQ', 'type' => 'richtext', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'shipping_info', 'value' => '<h2>Shipping Info</h2><p>Shipping information.</p>', 'group' => 'pages', 'label' => 'Shipping Info', 'type' => 'richtext', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'returns', 'value' => '<h2>Returns Policy</h2><p>Return policy details.</p>', 'group' => 'pages', 'label' => 'Returns Policy', 'type' => 'richtext', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('site_settings')->insert($defaults);
    }
};
