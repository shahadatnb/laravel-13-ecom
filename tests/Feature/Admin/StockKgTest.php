<?php

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Warehouse;

uses()->in('Feature\\Admin');

beforeEach(function () {
    $this->actingAs(User::factory()->create());

    $this->product = Product::factory()->create([
        'product_type' => 'variable',
        'stock_unit' => 'kg',
        'stock' => 120,
    ]);
    $this->variant = ProductVariant::factory()->create([
        'product_id' => $this->product->id,
        'stock' => 0,
        'unit_weight' => 5,
        'attributes' => ['Weight' => '5kg'],
    ]);
    $this->warehouse = Warehouse::factory()->create();
});

test('admin can add kilograms to the pool', function () {
    $response = $this->post(route('admin.stock.stock-in', $this->product), [
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 12.5,
        'reason' => 'Purchase',
    ]);

    $response->assertRedirect(route('admin.stock.index'));
    expect((float) $this->product->fresh()->stock)->toEqual(132.5);
    $this->assertDatabaseHas('inventory_transactions', [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
    ]);
});

test('kilogram pool stock-in ignores variant rows', function () {
    $response = $this->post(route('admin.stock.stock-in', $this->product), [
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 10,
        'variant_stocks' => [
            ['variant_id' => $this->variant->id, 'quantity' => 7],
        ],
    ]);

    $response->assertRedirect(route('admin.stock.index'));
    expect((float) $this->product->fresh()->stock)->toEqual(130.0);
    $this->assertDatabaseHas('product_variants', ['id' => $this->variant->id, 'stock' => 0]);
});

test('kilogram pool stock-in rejects fractional zero and negatives', function () {
    $response = $this->post(route('admin.stock.stock-in', $this->product), [
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 0,
    ]);

    $response->assertSessionHasErrors('quantity');
    expect((float) $this->product->fresh()->stock)->toEqual(120.0);
});
