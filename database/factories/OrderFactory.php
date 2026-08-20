<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 500, 50000);
        $discount = fake()->boolean(30) ? fake()->randomFloat(2, 0, $subtotal * 0.2) : 0;
        $tax = $subtotal * 0.05;
        $shippingCharge = fake()->randomFloat(2, 50, 200);
        $grandTotal = ($subtotal - $discount) + $tax + $shippingCharge;

        $statuses = ['pending', 'confirmed', 'processing', 'packed', 'shipped', 'delivered', 'completed', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'partially_paid', 'failed', 'refunded'];

        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-'.now()->format('Ymd').'-'.str_pad((string) fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'shipping_charge' => $shippingCharge,
            'grand_total' => $grandTotal,
            'paid_amount' => $grandTotal,
            'due_amount' => 0,
            'currency' => 'BDT',
            'status' => fake()->randomElement($statuses),
            'payment_status' => fake()->randomElement($paymentStatuses),
            'payment_method' => fake()->randomElement(['cod', 'online', 'wallet']),
            'shipping_status' => 'pending',
            'shipping_address' => [
                'recipient_name' => fake()->name(),
                'phone' => fake()->phoneNumber(),
                'address_line_1' => fake()->streetAddress(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'postal_code' => fake()->postcode(),
                'country' => 'Bangladesh',
            ],
            'billing_address' => [
                'recipient_name' => fake()->name(),
                'phone' => fake()->phoneNumber(),
                'address_line_1' => fake()->streetAddress(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'postal_code' => fake()->postcode(),
                'country' => 'Bangladesh',
            ],
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'payment_status' => 'paid',
            'shipping_status' => 'delivered',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'payment_status' => 'refunded',
        ]);
    }
}
