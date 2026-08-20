<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::take(20)->get();

        if ($products->isEmpty()) {
            $this->command->info('No products found. Run ProductSeeder first.');

            return;
        }

        Order::factory()
            ->count(25)
            ->sequence(fn ($sequence) => [
                'status' => collect(['pending', 'confirmed', 'processing', 'delivered', 'completed'])->random(),
                'payment_status' => collect(['pending', 'paid'])->random(),
            ])
            ->create()
            ->each(function (Order $order) use ($products) {
                $itemCount = fake()->numberBetween(1, 5);
                $orderSubtotal = 0;
                $chosenProducts = $products->random(min($itemCount, $products->count()));

                foreach ($chosenProducts as $product) {
                    $qty = fake()->numberBetween(1, 3);
                    $price = $product->sale_price ?? $product->regular_price ?? fake()->randomFloat(2, 100, 5000);
                    $itemSubtotal = $price * $qty;

                    $order->items()->create([
                        'product_id' => $product->id,
                        'product_variant_id' => null,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'unit_price' => $price,
                        'quantity' => $qty,
                        'subtotal' => $itemSubtotal,
                        'discount' => 0,
                        'tax' => $itemSubtotal * 0.05,
                        'total' => $itemSubtotal * 1.05,
                    ]);

                    $orderSubtotal += $itemSubtotal;
                }

                $discount = fake()->boolean(20) ? $orderSubtotal * 0.1 : 0;
                $tax = $orderSubtotal * 0.05;
                $shippingCharge = 100;
                $grandTotal = ($orderSubtotal - $discount) + $tax + $shippingCharge;

                $order->update([
                    'subtotal' => $orderSubtotal,
                    'discount' => $discount,
                    'tax' => $tax,
                    'shipping_charge' => $shippingCharge,
                    'grand_total' => $grandTotal,
                    'paid_amount' => $order->payment_status === 'paid' ? $grandTotal : 0,
                    'due_amount' => $order->payment_status === 'paid' ? 0 : $grandTotal,
                ]);

                // Log status history
                $order->statusHistories()->create([
                    'from_status' => null,
                    'to_status' => $order->status,
                    'changed_by_type' => 'system',
                    'notes' => 'Order created via seeder',
                ]);

                if ($order->status === 'completed') {
                    $order->statusHistories()->create([
                        'from_status' => 'processing',
                        'to_status' => 'completed',
                        'changed_by_type' => 'system',
                        'notes' => 'Order completed',
                    ]);
                }
            });
    }
}
