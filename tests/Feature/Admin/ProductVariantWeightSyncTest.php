<?php

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\ProductVariantService;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('an explicit unit weight overrides the name-derived weight', function () {
    $product = Product::factory()->create(['product_type' => 'variable', 'stock_unit' => 'kg']);
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'name' => '5kg',
        'unit_weight' => 5,
        'attributes' => ['Weight' => '5kg'],
    ]);

    app(ProductVariantService::class)->sync($product, [
        [
            'id' => $variant->id,
            'name' => 'বস্তা (50kg)',
            'attributes' => ['Weight' => 'বস্তা (50kg)'],
            'unit_weight' => '75',
        ],
    ]);

    expect((float) $variant->fresh()->unit_weight)->toEqual(75.0);
});

test('sync derives the weight when no explicit value is given', function () {
    $product = Product::factory()->create(['product_type' => 'variable', 'stock_unit' => 'kg']);

    app(ProductVariantService::class)->sync($product, [
        ['name' => '5kg', 'attributes' => ['Weight' => '5kg']],
        ['name' => 'চিপস 500g', 'attributes' => ['Weight' => '500g']],
    ]);

    $weights = $product->variants()->orderBy('id')->pluck('unit_weight')->map(fn ($w) => (float) $w);

    expect($weights->toArray())->toEqual([5.0, 0.5]);
});

test('a zero or empty explicit weight falls back to derivation', function () {
    $product = Product::factory()->create(['product_type' => 'variable', 'stock_unit' => 'kg']);

    app(ProductVariantService::class)->sync($product, [
        ['name' => '10kg', 'attributes' => ['Weight' => '10kg'], 'unit_weight' => '0'],
    ]);

    expect((float) $product->variants()->first()->unit_weight)->toEqual(10.0);
});

test('admin can update a variant weight via the AJAX endpoint', function () {
    $product = Product::factory()->create(['product_type' => 'variable', 'stock_unit' => 'kg']);
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'name' => '5kg',
        'unit_weight' => 5,
    ]);

    $this->post(route('admin.product.variant-update', $product), [
        'variant_id' => $variant->id,
        'field' => 'unit_weight',
        'value' => '50',
    ])->assertOk();

    expect((float) $variant->fresh()->unit_weight)->toEqual(50.0);
});

test('the AJAX endpoint rejects a negative weight', function () {
    $product = Product::factory()->create(['product_type' => 'variable', 'stock_unit' => 'kg']);
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'name' => '5kg',
        'unit_weight' => 5,
    ]);

    $this->post(route('admin.product.variant-update', $product), [
        'variant_id' => $variant->id,
        'field' => 'unit_weight',
        'value' => '-3',
    ])->assertSessionHasErrors();

    expect((float) $variant->fresh()->unit_weight)->toEqual(5.0);
});

test('a kg product validates against the submitted explicit weights', function () {
    $slug = 'bagged-rice-'.uniqid();

    $payload = [
        'name' => 'Bagged Rice',
        'slug' => $slug,
        'status' => 'published',
        'visibility' => 'public',
        'stock_unit' => 'kg',
        'stock' => 500,
        'product_type' => 'variable',
        'variants' => [
            [
                'name' => 'বস্তা',
                'attributes' => json_encode(['Weight' => 'বস্তা']),
                'unit_weight' => '50',
                'price' => 2500,
            ],
        ],
    ];

    $response = $this->post(route('admin.product.store'), $payload);

    $response->assertRedirect();

    $product = Product::where('slug', $slug)->firstOrFail();

    $this->assertDatabaseHas('product_variants', [
        'product_id' => $product->id,
        'unit_weight' => 50,
    ]);
});
