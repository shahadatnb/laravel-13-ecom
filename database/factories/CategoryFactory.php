<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'parent_id' => null,
            'name' => $name,
            'name_bn' => fake()->words(2, true),
            'slug' => fake()->unique()->slug(2),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'sort_order' => fake()->numberBetween(0, 100),
            'featured' => fake()->boolean(),
            'status' => fake()->randomElement(['active', 'inactive']),
            'visibility' => fake()->randomElement(['public', 'hidden', 'menu_only', 'homepage']),
        ];
    }
}
