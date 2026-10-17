<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;

uses()->in('Feature\\Admin');

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

function stockUnitPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Rice Sack',
        'slug' => 'rice-sack-'.uniqid(),
        'status' => 'published',
        'visibility' => 'public',
        'brand_id' => Brand::factory()->create()->id,
        'category_id' => Category::factory()->create()->id,
        'stock' => 500,
        'stock_unit' => 'kg',
    ], $overrides);
}

test('admin can store a kilogram pool product with derived variant weights', function () {
    $slug = 'rice-packs-'.uniqid();

    $response = $this->post(route('admin.product.store'), stockUnitPayload([
        'slug' => $slug,
        'variants' => [
            ['name' => '5kg Pack', 'attributes' => json_encode(['Weight' => '5kg']), 'price' => 500, 'stock' => 0],
            ['name' => '10kg Pack', 'attributes' => json_encode(['Weight' => '10kg']), 'price' => 950, 'stock' => 0],
        ],
    ]));

    $response->assertRedirect(route('admin.product.index'));
    $response->assertSessionHas('success');

    $product = Product::where('slug', $slug)->firstOrFail();
    expect($product->stock_unit)->toBe('kg')
        ->and((float) $product->stock)->toEqual(500.0);

    $fiveKg = $product->variants()->where('name', '5kg Pack')->firstOrFail();
    expect($fiveKg->resolveUnitWeight())->toEqual(5.0);

    $tenKg = $product->variants()->where('name', '10kg Pack')->firstOrFail();
    expect($tenKg->resolveUnitWeight())->toEqual(10.0);
});

test('kilogram pool product without variants requires a unit weight', function () {
    $response = $this->post(route('admin.product.store'), stockUnitPayload([
        'weight' => null,
    ]));

    $response->assertSessionHasErrors('stock_unit');
});

test('kilogram pool simple product stores with the entered unit weight', function () {
    $slug = 'loose-rice-'.uniqid();

    $response = $this->post(route('admin.product.store'), stockUnitPayload([
        'slug' => $slug,
        'weight' => 1.5,
    ]));

    $response->assertSessionHas('success');

    $product = Product::where('slug', $slug)->firstOrFail();
    expect($product->stock_unit)->toBe('kg')
        ->and((float) $product->weight)->toEqual(1.5);
});

test('count mode rejects decimal stock', function () {
    $response = $this->post(route('admin.product.store'), stockUnitPayload([
        'stock_unit' => 'unit',
        'stock' => 1.5,
        'weight' => 10,
    ]));

    $response->assertSessionHasErrors('stock_unit');
});

test('switching a variable product to kilogram pool requires variant weights', function () {
    $product = Product::factory()->create([
        'product_type' => 'variable',
        'stock_unit' => 'unit',
    ]);
    ProductVariant::factory()->create([
        'product_id' => $product->id,
        'name' => 'Size S',
        'attributes' => ['Size' => 'S'],
        'unit_weight' => null,
    ]);

    $response = $this->put(route('admin.product.update', $product->id), [
        'name' => $product->name,
        'slug' => $product->slug,
        'status' => 'published',
        'visibility' => 'public',
        'stock_unit' => 'kg',
    ]);

    $response->assertSessionHasErrors('stock_unit');
    expect($product->fresh()->stock_unit)->toBe('unit');
});

test('product api exposes stock unit and variant unit weight', function () {
    $product = Product::factory()->create([
        'status' => 'published',
        'stock_unit' => 'kg',
        'stock' => 120.5,
    ]);
    ProductVariant::factory()->create([
        'product_id' => $product->id,
        'unit_weight' => 2.5,
        'attributes' => ['Weight' => '2.5kg'],
    ]);

    $response = $this->getJson('/api/products/'.$product->slug);

    $response->assertOk()
        ->assertJsonPath('data.stock_unit', 'kg');
    expect((float) $response->json('data.stock'))->toEqual(120.5)
        ->and((float) $response->json('data.variants.0.unit_weight'))->toEqual(2.5);
});
