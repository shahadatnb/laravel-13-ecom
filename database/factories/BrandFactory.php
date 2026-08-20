<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name' => $name,
            'slug' => fake()->unique()->slug(2),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'website' => fake()->url(),
            'country' => fake()->country(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'sort_order' => fake()->numberBetween(0, 100),
            'featured' => fake()->boolean(),
            'status' => fake()->randomElement(['active', 'inactive']),
            'visibility' => fake()->randomElement(['public', 'hidden', 'featured']),
        ];
    }
}
