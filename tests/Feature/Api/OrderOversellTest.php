<?php

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SiteSetting;
use App\Models\User;

/**
 * Payload helpers for this file only (unique global names because Pest
 * test helpers share one process across files).
 */
function oversellOrderPayload(array $items): array
{
    return [
        'guest_email' => 'guest@example.com',
        'payment_method' => 'cod',
        'items' => $items,
    ];
}

function oversellItemFor(Product $product, int $qty, ?ProductVariant $variant = null): array
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

function enableOversell(): void
{
    SiteSetting::updateOrCreate(
        ['key' => 'allow_oversell'],
        ['value' => '1', 'type' => 'boolean', 'group' => 'checkout', 'label' => 'Allow Overselling'],
    );
}

test('orders beyond stock are rejected while overselling is disabled', function () {
    $product = Product::factory()->create([
        'stock' => 1,
        'product_type' => 'simple',
        'stock_unit' => 'unit',
    ]);

    $response = $this->postJson('/api/guest-orders', oversellOrderPayload([
        oversellItemFor($product, 2),
    ]));

    $response->assertStatus(422)->assertJsonValidationErrors(['items.0.quantity']);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
    $this->assertDatabaseCount('orders', 0);
});

test('overselling allows a simple product order beyond stock and drives stock negative', function () {
    enableOversell();

    $product = Product::factory()->create([
        'stock' => 1,
        'product_type' => 'simple',
        'stock_unit' => 'unit',
    ]);

    $response = $this->postJson('/api/guest-orders', oversellOrderPayload([
        oversellItemFor($product, 3),
    ]));

    $response->assertCreated();
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => -2]);
});

test('overselling allows a variant order beyond stock and drives variant stock negative', function () {
    enableOversell();

    $product = Product::factory()->create(['product_type' => 'variable', 'stock_unit' => 'unit']);
    $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'stock' => 1]);

    $response = $this->postJson('/api/guest-orders', oversellOrderPayload([
        oversellItemFor($product, 5, $variant),
    ]));

    $response->assertCreated();
    $this->assertDatabaseHas('product_variants', ['id' => $variant->id, 'stock' => -4]);
});

test('overselling allows a kg pool order beyond available kilograms', function () {
    enableOversell();

    $product = Product::factory()->create([
        'stock_unit' => 'kg',
        'stock' => 4,
        'product_type' => 'variable',
    ]);
    $fiveKg = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'stock' => 0,
        'unit_weight' => 5,
    ]);

    $response = $this->postJson('/api/guest-orders', oversellOrderPayload([
        oversellItemFor($product, 2, $fiveKg),
    ]));

    $response->assertCreated();
    // 4kg pool - (2 × 5kg) = -6kg
    expect((float) $product->fresh()->stock)->toEqual(-6.0);
});

test('overselling does not bypass missing product data validation', function () {
    enableOversell();

    $product = Product::factory()->create([
        'stock_unit' => 'kg',
        'stock' => 500,
        'product_type' => 'simple',
        'weight' => null,
    ]);

    $response = $this->postJson('/api/guest-orders', oversellOrderPayload([
        oversellItemFor($product, 1),
    ]));

    $response->assertStatus(422)->assertJsonValidationErrors(['items.0.quantity']);
    expect((float) $product->fresh()->stock)->toEqual(500.0);
});

test('admin site settings can toggle the allow oversell flag', function () {
    $this->actingAs(User::factory()->create());

    $setting = SiteSetting::updateOrCreate(
        ['key' => 'allow_oversell'],
        ['value' => '0', 'type' => 'boolean', 'group' => 'checkout', 'label' => 'Allow Overselling'],
    );

    $this->post(route('admin.settings.site-settings'), ['allow_oversell' => '1'])
        ->assertRedirect();

    expect($setting->fresh()->value)->toBe('1');

    // Unchecking the checkbox omits the key from the payload → flag turns off.
    $this->post(route('admin.settings.site-settings'), [])
        ->assertRedirect();

    expect($setting->fresh()->value)->toBe('0');
});
