<?php

use App\Models\Customer;
use App\Models\Order;

uses()->group('api', 'orders');

test('unauthenticated user cannot list orders', function () {
    $this->getJson('/api/orders')->assertUnauthorized();
});

test('customer only sees their own orders in the index', function () {
    $customer = Customer::factory()->create();
    $other = Customer::factory()->create();

    Order::factory()->count(2)->create(['customer_id' => $customer->id]);
    Order::factory()->count(3)->create(['customer_id' => $other->id]);

    $response = $this->actingAs($customer, 'customer')->getJson('/api/orders');

    $response->assertOk()->assertJson(['success' => true]);
    expect($response->json('data'))->toHaveCount(2);
});

test('customer can view their own order details', function () {
    $customer = Customer::factory()->create();
    $order = Order::factory()->create(['customer_id' => $customer->id]);

    $response = $this->actingAs($customer, 'customer')->getJson("/api/orders/{$order->id}");

    $response->assertOk()->assertJson([
        'success' => true,
        'data' => [
            'id' => $order->id,
            'order_number' => $order->order_number,
        ],
    ]);
});

test('customer cannot view another customers order', function () {
    $customer = Customer::factory()->create();
    $other = Customer::factory()->create();
    $order = Order::factory()->create(['customer_id' => $other->id]);

    $this->actingAs($customer, 'customer')
        ->getJson("/api/orders/{$order->id}")
        ->assertForbidden();
});
