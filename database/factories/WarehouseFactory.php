<?php

namespace Database\Factories;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company().' Warehouse',
            'code' => strtoupper($this->faker->unique()->bothify('WH-??##')),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'country' => $this->faker->country(),
            'zip_code' => $this->faker->postcode(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'manager_name' => $this->faker->name(),
            'is_default' => false,
            'status' => Warehouse::STATUS_ACTIVE,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => ['status' => Warehouse::STATUS_ACTIVE]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['status' => Warehouse::STATUS_INACTIVE]);
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => ['is_default' => true, 'status' => Warehouse::STATUS_ACTIVE]);
    }
}
