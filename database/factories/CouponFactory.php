<?php

namespace Database\Factories;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        $types = [Coupon::TYPE_PERCENTAGE, Coupon::TYPE_FIXED, Coupon::TYPE_FREE_SHIPPING];
        $type = $this->faker->randomElement($types);

        return [
            'code' => strtoupper($this->faker->unique()->bothify('??????##')),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(),
            'type' => $type,
            'discount_type' => Coupon::DISCOUNT_CART,
            'discount_value' => $type === Coupon::TYPE_FIXED ? $this->faker->randomFloat(2, 5, 100) : $this->faker->randomFloat(0, 5, 50),
            'max_discount' => $type === Coupon::TYPE_PERCENTAGE ? $this->faker->randomFloat(2, 20, 100) : null,
            'min_order_amount' => $this->faker->optional()->randomFloat(2, 20, 200),
            'usage_limit' => $this->faker->optional()->numberBetween(10, 1000),
            'per_user_limit' => 1,
            'total_used' => 0,
            'status' => $this->faker->randomElement([Coupon::STATUS_ACTIVE, Coupon::STATUS_DRAFT, Coupon::STATUS_INACTIVE]),
            'priority' => $this->faker->numberBetween(0, 100),
            'scope' => Coupon::SCOPE_ALL,
            'is_auto_apply' => false,
            'is_first_order_only' => false,
            'is_guest_allowed' => true,
            'valid_from' => $this->faker->optional()->dateTimeBetween('-1 month', '+1 month'),
            'valid_until' => $this->faker->optional()->dateTimeBetween('+1 month', '+6 months'),
            'created_by' => User::factory(),
        ];
    }

    public function percentage(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Coupon::TYPE_PERCENTAGE,
            'discount_value' => $this->faker->randomFloat(0, 5, 50),
            'max_discount' => $this->faker->randomFloat(2, 20, 100),
        ]);
    }

    public function fixed(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Coupon::TYPE_FIXED,
            'discount_value' => $this->faker->randomFloat(2, 5, 100),
            'max_discount' => null,
        ]);
    }

    public function freeShipping(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Coupon::TYPE_FREE_SHIPPING,
            'discount_value' => 0,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Coupon::STATUS_ACTIVE,
            'valid_from' => now()->subDays(1),
            'valid_until' => now()->addMonths(3),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Coupon::STATUS_EXPIRED,
            'valid_from' => now()->subMonths(6),
            'valid_until' => now()->subDays(1),
        ]);
    }

    public function autoApply(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_auto_apply' => true,
        ]);
    }
}
