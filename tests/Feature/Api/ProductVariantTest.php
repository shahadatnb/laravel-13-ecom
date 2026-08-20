<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;

uses()->group('api', 'product', 'variant');

beforeEach(function () {
    $this->brand = Brand::factory()->create();
    $this->category = Category::factory()->create();
});

test('product show endpoint returns variants with attributes as an array', function () {
    $product = Product::factory()->create([
        'brand_id' => $this->brand->id,
        'category_id' => $this->category->id,
        'slug' => 'test-variable-product',
        'product_type' => 'variable',
        'status' => 'published',
    ]);

    // Create 3 variants with known attribute combinations
    ProductVariant::factory()
        ->withAttributes('S', 'Red', 'Cotton')
        ->create(['product_id' => $product->id, 'sort_order' => 1]);

    ProductVariant::factory()
        ->withAttributes('M', 'Blue', 'Polyester')
        ->create(['product_id' => $product->id, 'sort_order' => 2]);

    ProductVariant::factory()
        ->withAttributes('L', 'Green', 'Wool')
        ->create(['product_id' => $product->id, 'sort_order' => 3]);

    $response = $this->getJson('/api/products/test-variable-product');

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $responseData = $response->json('data');
    expect($responseData)->toHaveKey('variants');
    expect($responseData['variants'])->toHaveCount(3);

    // Verify each variant's attributes is an array with correct keys
    // Build a lookup map from the response (order-independent assertion)
    $responseAttrs = collect($responseData['variants'])->map(fn ($v) => $v['attributes']);

    expect($responseAttrs)->toHaveCount(3);

    // Each variant MUST have attributes as an array (not string)
    $responseAttrs->each(function ($attrs) {
        expect($attrs)->toBeArray();
        expect($attrs)->toHaveKeys(['Size', 'Color', 'Material']);
    });

    // Find variant with Size=S and verify all its attributes
    $sVariant = $responseAttrs->firstWhere('Size', 'S');
    expect($sVariant)->not->toBeNull();
    expect($sVariant['Color'])->toBe('Red');
    expect($sVariant['Material'])->toBe('Cotton');

    // Find variant with Color=Blue
    $blueVariant = $responseAttrs->firstWhere('Color', 'Blue');
    expect($blueVariant)->not->toBeNull();
    expect($blueVariant['Size'])->toBe('M');
    expect($blueVariant['Material'])->toBe('Polyester');

    // Find variant with Material=Wool
    $woolVariant = $responseAttrs->firstWhere('Material', 'Wool');
    expect($woolVariant)->not->toBeNull();
    expect($woolVariant['Size'])->toBe('L');
    expect($woolVariant['Color'])->toBe('Green');
});

test('product with simple attributes returns correct keys', function () {
    $product = Product::factory()->create([
        'brand_id' => $this->brand->id,
        'category_id' => $this->category->id,
        'slug' => 'simple-attr-product',
        'product_type' => 'variable',
        'status' => 'published',
    ]);

    ProductVariant::factory()
        ->simpleAttributes()
        ->create(['product_id' => $product->id, 'sort_order' => 1]);

    ProductVariant::factory()
        ->simpleAttributes()
        ->create(['product_id' => $product->id, 'sort_order' => 2]);

    $response = $this->getJson('/api/products/simple-attr-product');

    $response->assertStatus(200);

    $variants = $response->json('data.variants');
    expect($variants)->toHaveCount(2);

    foreach ($variants as $variant) {
        expect($variant['attributes'])->toBeArray();
        // Simple attributes only have Size and Color (no Material)
        expect($variant['attributes'])->toHaveKeys(['Size', 'Color']);
        expect($variant['attributes'])->not->toHaveKey('Material');
    }
});

test('product without variants returns empty variants array', function () {
    $product = Product::factory()->create([
        'brand_id' => $this->brand->id,
        'category_id' => $this->category->id,
        'slug' => 'simple-product',
        'product_type' => 'simple',
        'status' => 'published',
    ]);

    $response = $this->getJson('/api/products/simple-product');

    $response->assertStatus(200);

    $variants = $response->json('data.variants');
    expect($variants)->toBeEmpty();
});

test('variant attributes are not returned as a string', function () {
    $product = Product::factory()->create([
        'brand_id' => $this->brand->id,
        'category_id' => $this->category->id,
        'slug' => 'string-check-product',
        'product_type' => 'variable',
        'status' => 'published',
    ]);

    ProductVariant::factory()
        ->withAttributes('XL', 'Black', 'Linen')
        ->create(['product_id' => $product->id, 'sort_order' => 1]);

    $response = $this->getJson('/api/products/string-check-product');

    $response->assertStatus(200);

    $variant = $response->json('data.variants.0');
    expect($variant['attributes'])->toBeArray();
    expect($variant['attributes'])->not->toBeString();
    // PHPUnit also can't iterate a string, but Pest's toBeArray() already confirms
});

test('variant attributes contain expected keys regardless of order', function () {
    $product = Product::factory()->create([
        'brand_id' => $this->brand->id,
        'category_id' => $this->category->id,
        'slug' => 'key-order-product',
        'product_type' => 'variable',
        'status' => 'published',
    ]);

    // Create variant where attributes may be in any order
    ProductVariant::factory()->create([
        'product_id' => $product->id,
        'attributes' => ['Color' => 'Red', 'Size' => 'M', 'Material' => 'Cotton'],
        'sort_order' => 1,
    ]);

    $response = $this->getJson('/api/products/key-order-product');

    $response->assertStatus(200);

    $variant = $response->json('data.variants.0');
    expect($variant['attributes'])->toHaveKeys(['Size', 'Color', 'Material']);
    expect($variant['attributes']['Size'])->toBe('M');
    expect($variant['attributes']['Color'])->toBe('Red');
});
