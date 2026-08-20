<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'icon' => 'fa-microchip', 'featured' => true],
            ['name' => 'Clothing', 'slug' => 'clothing', 'icon' => 'fa-shirt', 'featured' => true],
            ['name' => 'Home & Garden', 'slug' => 'home-garden', 'icon' => 'fa-house', 'featured' => true],
            ['name' => 'Sports', 'slug' => 'sports', 'icon' => 'fa-futbol', 'featured' => true],
            ['name' => 'Photography', 'slug' => 'photography', 'icon' => 'fa-camera', 'featured' => true],
            ['name' => 'Computers & Laptops', 'slug' => 'computers-laptops', 'parent_id' => 1, 'icon' => 'fa-laptop', 'featured' => false],
            ['name' => 'Smartphones & Tablets', 'slug' => 'smartphones-tablets', 'parent_id' => 1, 'icon' => 'fa-mobile-alt', 'featured' => false],
            ['name' => 'Audio & Headphones', 'slug' => 'audio-headphones', 'parent_id' => 1, 'icon' => 'fa-headphones', 'featured' => false],
            ['name' => 'Men\'s Clothing', 'slug' => 'mens-clothing', 'parent_id' => 2, 'icon' => 'fa-male', 'featured' => false],
            ['name' => 'Women\'s Clothing', 'slug' => 'womens-clothing', 'parent_id' => 2, 'icon' => 'fa-female', 'featured' => false],
            ['name' => 'Kids\' Clothing', 'slug' => 'kids-clothing', 'parent_id' => 2, 'icon' => 'fa-child', 'featured' => false],
            ['name' => 'Furniture', 'slug' => 'furniture', 'parent_id' => 3, 'icon' => 'fa-couch', 'featured' => false],
            ['name' => 'Kitchen Appliances', 'slug' => 'kitchen-appliances', 'parent_id' => 3, 'icon' => 'fa-blender', 'featured' => false],
            ['name' => 'Football', 'slug' => 'football', 'parent_id' => 4, 'icon' => 'fa-football-ball', 'featured' => false],
            ['name' => 'Basketball', 'slug' => 'basketball', 'parent_id' => 4, 'icon' => 'fa-basketball-ball', 'featured' => false],
            ['name' => 'Cameras', 'slug' => 'cameras', 'parent_id' => 5, 'icon' => 'fa-camera', 'featured' => false],
            ['name' => 'Lenses', 'slug' => 'lenses', 'parent_id' => 5, 'icon' => 'fa-camera-retro', 'featured' => false],
        ];

        foreach ($categories as $category) {
            Category::create([
                'parent_id' => $category['parent_id'] ?? null,
                'name' => $category['name'],
                'slug' => $category['slug'],
                'short_description' => fake()->sentence(10),
                'description' => fake()->paragraph(3),
                'icon' => $category['icon'],
                'thumbnail' => null,
                'banner' => null,
                'sort_order' => 0,
                'featured' => $category['featured'] ?? false,
                'status' => 'active',
                'visibility' => 'public',
                'meta_title' => $category['name'].' - Shop Online',
                'meta_description' => fake()->sentence(20),
                'meta_keywords' => $category['name'].', shop, buy, online, store',
                'canonical_url' => null,
            ]);
        }
    }
}
