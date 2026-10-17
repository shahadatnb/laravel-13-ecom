<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SiteSetting;

/**
 * Pricing payload helpers shared only by this file (unique global names
 * because Pest test helpers share one process).
 */
function pricingOrderPayload(array $items, array $overrides = []): array
{
    return array_merge([
        'guest_email' => 'guest@example.com',
        'payment_method' => 'cod',
        'items' => $items,
    ], $overrides);
}

function pricingItemFor(Product $product, int $qty, ?ProductVariant $variant = null): array
{
    $item = [
        'product_id' => $product->id,
        'product_name' => $product->name,
        'product_sku' => $product->sku,
        'unit_price' => 1,
        'quantity' => $qty,
    ];

    if ($variant !== null) {
        $item['product_variant_id'] = $variant->id;
        $item['variant_attributes'] = $variant->attributes;
    }

    return $item;
}

function pricingOrderFromResponse($response): Order
{
    return Order::where('order_number', $response->json('data.order_number'))->firstOrFail();
}

test('unit price is resolved from the database, not the client', function () {
    SiteSetting::where('key', 'tax_rate')->update(['value' => '0']);

    $product = Product::factory()->create([
        'stock' => 10,
        'product_type' => 'simple',
        'stock_unit' => 'unit',
        'sale_price' => 50,
        'regular_price' => 80,
    ]);

    $response = $this->postJson('/api/guest-orders', pricingOrderPayload([
        pricingItemFor($product, 2),
    ]));

    $response->assertCreated();

    $order = pricingOrderFromResponse($response);

    expect($order->items->first()->unit_price)->toEqual(50.0)
        ->and($order->subtotal)->toEqual(100.0)
        ->and($order->grand_total)->toEqual(100.0);
});

test('variant price wins over the product price', function () {
    SiteSetting::where('key', 'tax_rate')->update(['value' => '0']);

    $product = Product::factory()->create([
        'product_type' => 'variable',
        'stock_unit' => 'unit',
        'sale_price' => 50,
    ]);
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'price' => 120,
        'stock' => 5,
    ]);

    $response = $this->postJson('/api/guest-orders', pricingOrderPayload([
        pricingItemFor($product, 1, $variant),
    ]));

    $response->assertCreated();

    $order = pricingOrderFromResponse($response);

    expect($order->items->first()->unit_price)->toEqual(120.0)
        ->and($order->subtotal)->toEqual(120.0);
});

test('a variant without its own price falls back to the product price', function () {
    SiteSetting::where('key', 'tax_rate')->update(['value' => '0']);

    $product = Product::factory()->create([
        'product_type' => 'variable',
        'stock_unit' => 'unit',
        'sale_price' => 75,
    ]);
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'price' => null,
        'stock' => 5,
    ]);

    $response = $this->postJson('/api/guest-orders', pricingOrderPayload([
        pricingItemFor($product, 1, $variant),
    ]));

    $response->assertCreated();

    expect(pricingOrderFromResponse($response)->items->first()->unit_price)->toEqual(75.0);
});

test('a kg variant without its own price is charged the per-kg rate times its weight', function () {
    SiteSetting::where('key', 'tax_rate')->update(['value' => '0']);

    $product = Product::factory()->create([
        'product_type' => 'variable',
        'stock_unit' => 'kg',
        'stock' => 100,
        'sale_price' => 60,
        'regular_price' => 70,
    ]);
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'price' => null,
        'unit_weight' => 5,
        'stock' => 0,
    ]);

    $response = $this->postJson('/api/guest-orders', pricingOrderPayload([
        pricingItemFor($product, 1, $variant),
    ]));

    $response->assertCreated();

    // ৳60/kg × 5 kg pack = ৳300, not the bare per-kg rate.
    expect(pricingOrderFromResponse($response)->items->first()->unit_price)->toEqual(300.0);
});

test('an explicit kg variant price wins over the scaled product rate', function () {
    SiteSetting::where('key', 'tax_rate')->update(['value' => '0']);

    $product = Product::factory()->create([
        'product_type' => 'variable',
        'stock_unit' => 'kg',
        'stock' => 100,
        'sale_price' => 60,
        'regular_price' => 70,
    ]);
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'price' => 290,
        'unit_weight' => 5,
        'stock' => 0,
    ]);

    $response = $this->postJson('/api/guest-orders', pricingOrderPayload([
        pricingItemFor($product, 1, $variant),
    ]));

    $response->assertCreated();

    // The admin-set pack price is charged as-is; the scaled rate is only a fallback.
    expect(pricingOrderFromResponse($response)->items->first()->unit_price)->toEqual(290.0);
});

test('client-sent order discount is ignored', function () {
    SiteSetting::where('key', 'tax_rate')->update(['value' => '0']);

    $product = Product::factory()->create([
        'stock' => 10,
        'product_type' => 'simple',
        'stock_unit' => 'unit',
        'sale_price' => 50,
        'regular_price' => 50,
    ]);

    $response = $this->postJson('/api/guest-orders', pricingOrderPayload([
        pricingItemFor($product, 2),
    ], ['discount' => 90]));

    $response->assertCreated();

    $order = pricingOrderFromResponse($response);

    expect($order->discount)->toEqual(0.0)
        ->and($order->grand_total)->toEqual(100.0);
});

test('an unknown coupon code is rejected', function () {
    $product = Product::factory()->create([
        'stock' => 10,
        'product_type' => 'simple',
        'stock_unit' => 'unit',
        'sale_price' => 50,
    ]);

    $response = $this->postJson('/api/guest-orders', pricingOrderPayload([
        pricingItemFor($product, 1),
    ], ['coupon_code' => 'NOPE']));

    $response->assertStatus(422)->assertJsonValidationErrors(['coupon_code']);
    $this->assertDatabaseCount('orders', 0);
});
