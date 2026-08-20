<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Samsung', 'slug' => 'samsung', 'country' => 'South Korea', 'website' => 'https://samsung.com', 'featured' => true],
            ['name' => 'Apple', 'slug' => 'apple', 'country' => 'United States', 'website' => 'https://apple.com', 'featured' => true],
            ['name' => 'Sony', 'slug' => 'sony', 'country' => 'Japan', 'website' => 'https://sony.com', 'featured' => true],
            ['name' => 'Nike', 'slug' => 'nike', 'country' => 'United States', 'website' => 'https://nike.com', 'featured' => true],
            ['name' => 'Adidas', 'slug' => 'adidas', 'country' => 'Germany', 'website' => 'https://adidas.com', 'featured' => true],
            ['name' => 'LG', 'slug' => 'lg', 'country' => 'South Korea', 'website' => 'https://lg.com'],
            ['name' => 'HP', 'slug' => 'hp', 'country' => 'United States', 'website' => 'https://hp.com'],
            ['name' => 'Dell', 'slug' => 'dell', 'country' => 'United States', 'website' => 'https://dell.com'],
            ['name' => 'Lenovo', 'slug' => 'lenovo', 'country' => 'China', 'website' => 'https://lenovo.com'],
            ['name' => 'Asus', 'slug' => 'asus', 'country' => 'Taiwan', 'website' => 'https://asus.com'],
            ['name' => 'Microsoft', 'slug' => 'microsoft', 'country' => 'United States', 'website' => 'https://microsoft.com'],
            ['name' => 'Philips', 'slug' => 'philips', 'country' => 'Netherlands', 'website' => 'https://philips.com'],
            ['name' => 'Bose', 'slug' => 'bose', 'country' => 'United States', 'website' => 'https://bose.com'],
            ['name' => 'JBL', 'slug' => 'jbl', 'country' => 'United States', 'website' => 'https://jbl.com'],
            ['name' => 'Canon', 'slug' => 'canon', 'country' => 'Japan', 'website' => 'https://canon.com'],
            ['name' => 'Nikon', 'slug' => 'nikon', 'country' => 'Japan', 'website' => 'https://nikon.com'],
            ['name' => 'Under Armour', 'slug' => 'under-armour', 'country' => 'United States', 'website' => 'https://underarmour.com'],
        ];

        foreach ($brands as $brand) {
            Brand::create([
                'name' => $brand['name'],
                'slug' => $brand['slug'],
                'short_description' => fake()->sentence(10),
                'description' => fake()->paragraph(3),
                'country' => $brand['country'],
                'website' => $brand['website'],
                'logo' => null,
                'banner' => null,
                'featured' => $brand['featured'] ?? false,
                'sort_order' => 0,
                'status' => 'active',
                'visibility' => 'public',
                'meta_title' => $brand['name'].' - Official Store',
                'meta_description' => fake()->sentence(20),
                'meta_keywords' => $brand['name'].', electronics, shop, buy online',
                'canonical_url' => $brand['website'],
            ]);
        }
    }
}
