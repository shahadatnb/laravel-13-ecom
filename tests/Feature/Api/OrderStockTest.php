<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\OrderService;

/**
 * Stock payload helpers shared only by this file (unique global names
 * because Pest test helpers share one process).
 */
function stockOrderPayload(array $items): array
{
    return [
        'guest_email' => 'guest@example.com',
        'payment_method' => 'cod',
        'items' => $items,
    ];
}

function stockItemFor(Product $product, int $qty, ?ProductVariant $variant = null): array
{
    $item = [
        'product_id' => $product->id,
        'product_name' => $product->name,
        'product_sku' => $product->sku,
        'unit_price' => 100,
        'quantity' => $qty,
    ];

    if ($variant !== null) {
        $item['product_variant_id'] = $variant->id;
        $item['variant_attributes'] = $variant->attributes;
    }

    return $item;
}

test('unit mode decrements simple product stock on order', function () {
    $product = Product::factory()->create([
        'stock' => 10,
        'product_type' => 'simple',
        'stock_unit' => 'unit',
    ]);

    $response = $this->postJson('/api/guest-orders', stockOrderPayload([
        stockItemFor($product, 2),
    ]));

    $response->assertCreated();
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 8]);

    $order = Order::where('order_number', $response->json('data.order_number'))->firstOrFail();
    expect($order->stock_deducted_at)->not->toBeNull();
});

test('unit mode decrements variant stock on order', function () {
    $product = Product::factory()->create([
        'product_type' => 'variable',
        'stock_unit' => 'unit',
        'stock' => 0,
    ]);
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'stock' => 5,
        'unit_weight' => null,
    ]);

    $response = $this->postJson('/api/guest-orders', stockOrderPayload([
        stockItemFor($product, 2, $variant),
    ]));

    $response->assertCreated();
    $this->assertDatabaseHas('product_variants', ['id' => $variant->id, 'stock' => 3]);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 0]);
});

test('unit mode rejects orders beyond variant stock', function () {
    $product = Product::factory()->create(['product_type' => 'variable', 'stock_unit' => 'unit']);
    $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'stock' => 1]);

    $response = $this->postJson('/api/guest-orders', stockOrderPayload([
        stockItemFor($product, 2, $variant),
    ]));

    $response->assertStatus(422)->assertJsonValidationErrors(['items.0.quantity']);
    $this->assertDatabaseHas('product_variants', ['id' => $variant->id, 'stock' => 1]);
    $this->assertDatabaseCount('orders', 0);
});

test('kg pool deducts qty times unit weight across variants', function () {
    $product = Product::factory()->create([
        'stock_unit' => 'kg',
        'stock' => 100,
        'product_type' => 'variable',
    ]);
    $fiveKg = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'stock' => 0,
        'unit_weight' => 5,
        'attributes' => ['Weight' => '5kg'],
    ]);
    $tenKg = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'stock' => 0,
        'unit_weight' => 10,
        'attributes' => ['Weight' => '10kg'],
    ]);

    $response = $this->postJson('/api/guest-orders', stockOrderPayload([
        stockItemFor($product, 2, $fiveKg),
        stockItemFor($product, 1, $tenKg),
    ]));

    $response->assertCreated();
    // 100kg pool - (2 × 5kg) - (1 × 10kg) = 80kg
    expect((float) $product->fresh()->stock)->toEqual(80.0);
    // Variant-level counters are not used in kg mode
    $this->assertDatabaseHas('product_variants', ['id' => $fiveKg->id, 'stock' => 0]);
    $this->assertDatabaseHas('product_variants', ['id' => $tenKg->id, 'stock' => 0]);
});

test('kg pool rejects demand beyond available kilograms', function () {
    $product = Product::factory()->create([
        'stock_unit' => 'kg',
        'stock' => 20,
        'product_type' => 'variable',
    ]);
    $tenKg = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'stock' => 999,
        'unit_weight' => 10,
    ]);

    $response = $this->postJson('/api/guest-orders', stockOrderPayload([
        stockItemFor($product, 3, $tenKg),
    ]));

    $response->assertStatus(422)->assertJsonValidationErrors(['items.0.quantity']);
    expect((float) $product->fresh()->stock)->toEqual(20.0);
    $this->assertDatabaseCount('orders', 0);
});

test('kg product without a configured unit weight is rejected', function () {
    $product = Product::factory()->create([
        'stock_unit' => 'kg',
        'stock' => 500,
        'product_type' => 'simple',
        'weight' => null,
    ]);

    $response = $this->postJson('/api/guest-orders', stockOrderPayload([
        stockItemFor($product, 1),
    ]));

    $response->assertStatus(422)->assertJsonValidationErrors(['items.0.quantity']);
    expect((float) $product->fresh()->stock)->toEqual(500.0);
});

test('cancelled orders restock the kg pool exactly once', function () {
    $product = Product::factory()->create([
        'stock_unit' => 'kg',
        'stock' => 100,
        'product_type' => 'variable',
    ]);
    $fiveKg = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'stock' => 0,
        'unit_weight' => 5,
    ]);

    $response = $this->postJson('/api/guest-orders', stockOrderPayload([
        stockItemFor($product, 2, $fiveKg),
    ]));
    $response->assertCreated();
    expect((float) $product->fresh()->stock)->toEqual(90.0);

    $service = app(OrderService::class);
    $order = Order::where('order_number', $response->json('data.order_number'))->firstOrFail();

    $service->changeStatus($order, Order::STATUS_CANCELLED);
    expect((float) $product->fresh()->stock)->toEqual(100.0);

    // Re-open then cancel again: the guard must prevent a second restock.
    $service->changeStatus($order->fresh(), Order::STATUS_PENDING);
    $service->changeStatus($order->fresh(), Order::STATUS_CANCELLED);
    expect((float) $product->fresh()->stock)->toEqual(100.0);
});

test('orders that never deducted stock do not restock on cancel', function () {
    $product = Product::factory()->create([
        'stock_unit' => 'kg',
        'stock' => 100,
        'product_type' => 'variable',
    ]);
    $fiveKg = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'stock' => 0,
        'unit_weight' => 5,
    ]);

    $response = $this->postJson('/api/guest-orders', stockOrderPayload([
        stockItemFor($product, 2, $fiveKg),
    ]));
    $response->assertCreated();

    // Simulate a legacy order created before stock tracking existed.
    $order = Order::where('order_number', $response->json('data.order_number'))->firstOrFail();
    $order->update(['stock_deducted_at' => null]);

    app(OrderService::class)->changeStatus($order, Order::STATUS_CANCELLED);

    expect((float) $product->fresh()->stock)->toEqual(90.0);
});
