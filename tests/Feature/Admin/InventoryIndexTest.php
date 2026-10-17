<?php

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;

uses()->in('Feature\\Admin');

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('admin inventory index loads with variant attribute filters', function () {
    $product = Product::factory()->create(['product_type' => 'variable']);
    ProductVariant::factory()->create([
        'product_id' => $product->id,
        'attributes' => ['Weight' => '5kg', 'Color' => 'Red'],
    ]);
    ProductVariant::factory()->create([
        'product_id' => $product->id,
        'attributes' => ['Weight' => '10kg'],
    ]);

    $response = $this->get(route('admin.inventory.index'));

    $response->assertOk();
    $response->assertViewIs('admin.inventory.index');

    $attributes = $response->viewData('variantAttributes');
    expect($attributes['Weight'])->toEqual(['5kg', '10kg'])
        ->and($attributes['Color'])->toEqual(['Red']);
});

test('admin inventory index skips variants with empty or null attributes', function () {
    $product = Product::factory()->create(['product_type' => 'variable']);
    ProductVariant::factory()->create([
        'product_id' => $product->id,
        'attributes' => null,
    ]);
    ProductVariant::factory()->create([
        'product_id' => $product->id,
        'attributes' => ['Size' => 'L'],
    ]);
    // JSON null text (valid JSON, but not an attribute map) must not crash.
    DB::table('product_variants')->insert([
        'product_id' => $product->id,
        'name' => 'JSON null variant',
        'sku' => 'json-null-'.uniqid(),
        'attributes' => 'null',
        'price' => 100,
        'stock' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->get(route('admin.inventory.index'));

    $response->assertOk();
    expect($response->viewData('variantAttributes')['Size'])->toEqual(['L']);
});
