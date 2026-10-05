<?php

use App\Models\Customer;
use App\Models\Product;

uses()->group('api', 'wishlist');

test('wishlist returns product thumbnail as product image', function () {
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['thumbnail' => 'upload/products/thumb.jpg']);

    $this->actingAs($customer, 'customer')
        ->postJson('/api/wishlist', ['product_id' => $product->id])
        ->assertCreated();

    $this->actingAs($customer, 'customer')
        ->getJson('/api/wishlist')
        ->assertOk()
        ->assertJsonPath('data.0.product_image', 'upload/products/thumb.jpg');
});

test('wishlist falls back to first gallery image when thumbnail is missing', function () {
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['thumbnail' => null]);
    $product->images()->create(['image' => 'upload/products/gallery.jpg']);

    $this->actingAs($customer, 'customer')
        ->postJson('/api/wishlist', ['product_id' => $product->id])
        ->assertCreated();

    $this->actingAs($customer, 'customer')
        ->getJson('/api/wishlist')
        ->assertOk()
        ->assertJsonPath('data.0.product_image', 'upload/products/gallery.jpg');
});

test('wishlist product image is null when product has no image at all', function () {
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['thumbnail' => null]);

    $this->actingAs($customer, 'customer')
        ->postJson('/api/wishlist', ['product_id' => $product->id])
        ->assertCreated();

    $this->actingAs($customer, 'customer')
        ->getJson('/api/wishlist')
        ->assertOk()
        ->assertJsonPath('data.0.product_image', null);
});
