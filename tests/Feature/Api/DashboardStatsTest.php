<?php

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\UserAddress;
use App\Models\WishlistItem;

uses()->group('api', 'dashboard');

test('unauthenticated user cannot access dashboard stats', function () {
    $this->getJson('/api/dashboard/stats')->assertUnauthorized();
});

test('dashboard stats returns correct counts for the customer', function () {
    $customer = Customer::factory()->create();
    $customer->wallet()->create([
        'balance' => 150,
        'locked_balance' => 20,
        'status' => 'active',
    ]);

    Order::factory()->count(2)->create(['customer_id' => $customer->id]);

    UserAddress::factory()->count(2)->create([
        'user_id' => null,
        'customer_id' => $customer->id,
    ]);

    WishlistItem::create([
        'customer_id' => $customer->id,
        'product_id' => Product::factory()->create()->id,
    ]);

    $response = $this->actingAs($customer, 'customer')->getJson('/api/dashboard/stats');

    $response->assertOk()->assertJson(['success' => true]);

    expect($response->json('data.total_orders'))->toBe(2);
    expect((float) $response->json('data.wallet_balance'))->toBe(150.0);
    expect($response->json('data.total_addresses'))->toBe(2);
    expect($response->json('data.total_wishlist'))->toBe(1);
});

test('dashboard stats ignores other customers data', function () {
    $customer = Customer::factory()->create();
    $other = Customer::factory()->create();

    Order::factory()->count(3)->create(['customer_id' => $other->id]);

    UserAddress::factory()->create([
        'user_id' => null,
        'customer_id' => $other->id,
    ]);

    WishlistItem::create([
        'customer_id' => $other->id,
        'product_id' => Product::factory()->create()->id,
    ]);

    $other->wallet()->create([
        'balance' => 999,
        'locked_balance' => 0,
        'status' => 'active',
    ]);

    $response = $this->actingAs($customer, 'customer')->getJson('/api/dashboard/stats');

    expect($response->json('data.total_orders'))->toBe(0);
    expect((float) $response->json('data.wallet_balance'))->toBe(0.0);
    expect($response->json('data.total_addresses'))->toBe(0);
    expect($response->json('data.total_wishlist'))->toBe(0);
});

test('dashboard stats returns zero balance when customer has no wallet', function () {
    $customer = Customer::factory()->create();

    $response = $this->actingAs($customer, 'customer')->getJson('/api/dashboard/stats');

    $response->assertOk();
    expect((float) $response->json('data.wallet_balance'))->toBe(0.0);
});
