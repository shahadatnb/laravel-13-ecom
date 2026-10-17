<?php

use App\Models\DeliveryZone;
use App\Models\District;
use App\Models\Order;
use App\Models\Product;
use App\Models\SiteSetting;

beforeEach(function () {
    $this->zone = DeliveryZone::create([
        'name' => 'Inside Dhaka',
        'type' => 'inside_dhaka',
        'charge' => 60,
        'minimum_order_amount' => 5000,
        'status' => 'active',
    ]);

    $this->district = District::create([
        'name' => 'Dhaka',
        'division' => 'Dhaka Division',
        'delivery_zone_id' => $this->zone->id,
        'status' => 'active',
    ]);

    $this->product = Product::factory()->create([
        'stock' => 10,
        'status' => 'published',
        'product_type' => 'simple',
        'sale_price' => 500,
        'regular_price' => 500,
    ]);
});

function shippingOrderPayload(Product $product, array $overrides = []): array
{
    return array_replace_recursive([
        'guest_email' => 'guest@example.com',
        'payment_method' => 'cod',
        'items' => [[
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'unit_price' => 500,
            'quantity' => 2,
        ]],
        'shipping_address' => [
            'recipient_name' => 'Guest Customer',
            'phone' => '01700000000',
            'address_line_1' => 'House 1, Road 1',
            'district' => 'Dhaka',
        ],
    ], $overrides);
}

test('shipping charge is recalculated server-side and client values are ignored', function () {
    SiteSetting::where('key', 'tax_rate')->update(['value' => '10']);

    $response = $this->postJson('/api/guest-orders', shippingOrderPayload($this->product, [
        'shipping_charge' => 0,
        'tax' => 9999,
    ]));

    $response->assertCreated();

    $order = Order::where('order_number', $response->json('data.order_number'))->firstOrFail();

    expect($order->subtotal)->toEqual(1000.0)
        ->and($order->shipping_charge)->toEqual(60.0)
        ->and($order->tax)->toEqual(100.0)
        ->and($order->grand_total)->toEqual(1160.0);
});

test('unknown district is rejected and no order is created', function () {
    $response = $this->postJson('/api/guest-orders', shippingOrderPayload($this->product, [
        'shipping_address' => [
            'recipient_name' => 'Guest Customer',
            'phone' => '01700000000',
            'address_line_1' => 'House 1, Road 1',
            'district' => 'Atlantis',
        ],
    ]));

    $response->assertStatus(422)->assertJsonValidationErrors(['shipping_address.district']);
    $this->assertDatabaseCount('orders', 0);
});

test('inactive district is rejected at order placement', function () {
    $this->district->update(['status' => 'inactive']);

    $response = $this->postJson('/api/guest-orders', shippingOrderPayload($this->product));

    $response->assertStatus(422)->assertJsonValidationErrors(['shipping_address.district']);
    $this->assertDatabaseCount('orders', 0);
});

test('shipping address without a district is rejected', function () {
    $response = $this->postJson('/api/guest-orders', shippingOrderPayload($this->product, [
        'shipping_address' => [
            'recipient_name' => 'Guest Customer',
            'phone' => '01700000000',
            'address_line_1' => 'House 1, Road 1',
            'district' => null,
        ],
    ]));

    $response->assertStatus(422)->assertJsonValidationErrors(['shipping_address.district']);
    $this->assertDatabaseCount('orders', 0);
});

test('free delivery threshold is applied server-side', function () {
    $response = $this->postJson('/api/guest-orders', shippingOrderPayload($this->product, [
        'items' => [[
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'unit_price' => 500,
            'quantity' => 10,
        ]],
    ]));

    $response->assertCreated();

    $order = Order::where('order_number', $response->json('data.order_number'))->firstOrFail();

    expect($order->subtotal)->toEqual(5000.0)
        ->and($order->shipping_charge)->toEqual(0.0);
});

test('district charge override is used instead of the zone charge', function () {
    $this->district->update(['charge' => 45]);
    SiteSetting::where('key', 'tax_rate')->update(['value' => '0']);

    $response = $this->postJson('/api/guest-orders', shippingOrderPayload($this->product));

    $response->assertCreated();

    $order = Order::where('order_number', $response->json('data.order_number'))->firstOrFail();

    expect($order->shipping_charge)->toEqual(45.0)
        ->and($order->grand_total)->toEqual(1045.0);
});
