<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'brand_id' => Brand::factory(),
            'category_id' => Category::factory(),
            'name' => fake()->word().' '.fake()->word(),
            'name_bn' => fake()->word(),
            'slug' => fake()->slug(),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'sku' => fake()->unique()->numerify('SKU-#####'),
            'barcode' => fake()->unique()->numerify('##########'),
            'thumbnail' => null,
            'regular_price' => fake()->randomFloat(2, 10, 500),
            'sale_price' => fake()->randomFloat(2, 5, 400),
            'wholesale_price' => fake()->randomFloat(2, 5, 300),
            'cost_price' => fake()->randomFloat(2, 5, 200),
            'stock' => fake()->numberBetween(0, 100),
            'minimum_stock' => fake()->numberBetween(0, 10),
            'maximum_order' => fake()->numberBetween(1, 50),
            'weight' => fake()->randomFloat(2, 0.1, 10),
            'length' => fake()->randomFloat(2, 1, 50),
            'width' => fake()->randomFloat(2, 1, 50),
            'height' => fake()->randomFloat(2, 1, 50),
            'tax_class' => fake()->word(),
            'shipping_class' => fake()->word(),
            'status' => fake()->randomElement(['draft', 'pending', 'published', 'hidden', 'archived']),
            'product_type' => fake()->randomElement(['simple', 'variable', 'digital', 'service', 'bundle']),
            'featured' => fake()->boolean(),
            'visibility' => fake()->randomElement(['public', 'private', 'hidden']),
            'meta_title' => fake()->sentence(),
            'meta_description' => fake()->sentence(),
            'meta_keywords' => fake()->words(3, true),
            'canonical_url' => fake()->url(),
            'published_at' => fake()->optional()->dateTime(),
        ];
    }
}
