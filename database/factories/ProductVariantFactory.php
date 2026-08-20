<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        static $comboIndex = 0;

        // Cycle through a set of realistic attribute combinations
        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $colors = ['Red', 'Blue', 'Green', 'Black', 'White'];
        $materials = ['Cotton', 'Polyester', 'Wool', 'Linen'];

        $size = $sizes[array_rand($sizes)];
        $color = $colors[array_rand($colors)];
        $material = $materials[array_rand($materials)];

        $comboIndex++;

        return [
            'product_id' => Product::factory(),
            'name' => "{$size} / {$color}",
            'sku' => 'VAR-'.fake()->unique()->numerify('#####'),
            'barcode' => fake()->unique()->numerify('##########'),
            'price' => fake()->randomFloat(2, 10, 200),
            'stock' => fake()->numberBetween(0, 50),
            'attributes' => [
                'Size' => $size,
                'Color' => $color,
                'Material' => $material,
            ],
            'sort_order' => $comboIndex,
        ];
    }

    /**
     * State: Only set specific Size, Color, Material attributes.
     */
    public function withAttributes(string $size, string $color, string $material): static
    {
        return $this->state(fn () => [
            'name' => "{$size} / {$color}",
            'attributes' => [
                'Size' => $size,
                'Color' => $color,
                'Material' => $material,
            ],
        ]);
    }

    /**
     * State: Use simple attributes (just Size and Color).
     */
    public function simpleAttributes(): static
    {
        $sizes = ['S', 'M', 'L'];
        $colors = ['Red', 'Blue', 'Green'];
        $size = $sizes[array_rand($sizes)];
        $color = $colors[array_rand($colors)];

        return $this->state(fn () => [
            'name' => "{$size} / {$color}",
            'attributes' => [
                'Size' => $size,
                'Color' => $color,
            ],
        ]);
    }
}
