<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    public function definition(): array
    {
        $currentStock = $this->faker->numberBetween(0, 500);
        $reservedStock = $this->faker->numberBetween(0, min(50, $currentStock));

        return [
            'product_id' => Product::factory(),
            'warehouse_id' => Warehouse::factory(),
            'product_variant_id' => null,
            'current_stock' => $currentStock,
            'reserved_stock' => $reservedStock,
            'minimum_stock' => $this->faker->numberBetween(5, 50),
            'maximum_stock' => $this->faker->optional()->numberBetween(200, 1000),
            'reorder_level' => $this->faker->numberBetween(10, 100),
            'location' => $this->faker->optional()->regexify('[A-Z]{1}[0-9]{2}-[A-Z]{1}[0-9]{2}'),
        ];
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_stock' => $this->faker->numberBetween(0, 10),
            'minimum_stock' => 20,
            'reserved_stock' => 0,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_stock' => 0,
            'reserved_stock' => 0,
            'minimum_stock' => $this->faker->numberBetween(5, 20),
        ]);
    }

    public function inStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_stock' => $this->faker->numberBetween(50, 500),
            'reserved_stock' => $this->faker->numberBetween(0, 20),
            'minimum_stock' => $this->faker->numberBetween(5, 30),
        ]);
    }
}
