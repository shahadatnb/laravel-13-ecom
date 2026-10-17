<?php

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Warehouse;
use App\Services\OrderService;

/**
 * Stock payload helpers shared only by this file (unique global names
 * because Pest test helpers share one process).
 */
function ledgerOrderPayload(array $items): array
{
    return [
        'guest_email' => 'guest@example.com',
        'payment_method' => 'cod',
        'items' => $items,
    ];
}

function ledgerItemFor(Product $product, int $qty, ?ProductVariant $variant = null): array
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

function ledgerOrderFromResponse($response): Order
{
    return Order::where('order_number', $response->json('data.order_number'))->firstOrFail();
}

beforeEach(function () {
    $this->warehouse = Warehouse::factory()->default()->create();
});

test('kg pool sales mirror into the warehouse ledger', function () {
    $product = Product::factory()->create([
        'stock_unit' => 'kg',
        'stock' => 100,
        'product_type' => 'variable',
    ]);
    $fiveKg = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'stock' => 0,
        'unit_weight' => 5,
    ]);
    Inventory::factory()->create([
        'product_id' => $product->id,
        'warehouse_id' => $this->warehouse->id,
        'product_variant_id' => null,
        'current_stock' => 100,
    ]);

    $response = $this->postJson('/api/guest-orders', ledgerOrderPayload([
        ledgerItemFor($product, 2, $fiveKg),
    ]));

    $response->assertCreated();
    expect((float) $product->fresh()->stock)->toEqual(90.0);

    $inventory = Inventory::where('product_id', $product->id)
        ->where('warehouse_id', $this->warehouse->id)
        ->whereNull('product_variant_id')
        ->firstOrFail();

    expect((float) $inventory->current_stock)->toEqual(90.0);

    $transaction = InventoryTransaction::where('inventory_id', $inventory->id)
        ->where('type', InventoryTransaction::TYPE_SALE)
        ->firstOrFail();

    expect($transaction->reference_type)->toEqual('order')
        ->and($transaction->reference_number)->toEqual(ledgerOrderFromResponse($response)->order_number)
        ->and((float) $transaction->quantity_before)->toEqual(100.0)
        ->and((float) $transaction->quantity_change)->toEqual(-10.0)
        ->and((float) $transaction->quantity_after)->toEqual(90.0);
});

test('a missing inventory row is seeded from the storefront pool', function () {
    $product = Product::factory()->create([
        'stock_unit' => 'kg',
        'stock' => 100,
        'product_type' => 'simple',
        'weight' => 5,
    ]);

    $response = $this->postJson('/api/guest-orders', ledgerOrderPayload([
        ledgerItemFor($product, 2),
    ]));

    $response->assertCreated();
    expect((float) $product->fresh()->stock)->toEqual(90.0);

    $inventory = Inventory::where('product_id', $product->id)
        ->where('warehouse_id', $this->warehouse->id)
        ->whereNull('product_variant_id')
        ->firstOrFail();

    expect((float) $inventory->current_stock)->toEqual(90.0);

    $transaction = InventoryTransaction::where('inventory_id', $inventory->id)->firstOrFail();
    expect((float) $transaction->quantity_before)->toEqual(100.0)
        ->and((float) $transaction->quantity_after)->toEqual(90.0);
});

test('a drifted single-warehouse ledger is aligned to the storefront pool', function () {
    $product = Product::factory()->create([
        'stock_unit' => 'kg',
        'stock' => 100,
        'product_type' => 'simple',
        'weight' => 5,
    ]);
    // Ledger drifted to 30 while the storefront pool says 100.
    Inventory::factory()->create([
        'product_id' => $product->id,
        'warehouse_id' => $this->warehouse->id,
        'product_variant_id' => null,
        'current_stock' => 30,
    ]);

    $response = $this->postJson('/api/guest-orders', ledgerOrderPayload([
        ledgerItemFor($product, 1),
    ]));

    $response->assertCreated();

    $inventory = Inventory::where('product_id', $product->id)->firstOrFail();
    expect((float) $inventory->current_stock)->toEqual(95.0);

    $transaction = InventoryTransaction::where('inventory_id', $inventory->id)->firstOrFail();
    expect((float) $transaction->quantity_before)->toEqual(100.0)
        ->and((float) $transaction->quantity_after)->toEqual(95.0);
});

test('variant sales mirror per variant row', function () {
    $product = Product::factory()->create([
        'product_type' => 'variable',
        'stock_unit' => 'unit',
        'stock' => 0,
    ]);
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'stock' => 5,
    ]);
    Inventory::factory()->create([
        'product_id' => $product->id,
        'warehouse_id' => $this->warehouse->id,
        'product_variant_id' => $variant->id,
        'current_stock' => 5,
    ]);

    $response = $this->postJson('/api/guest-orders', ledgerOrderPayload([
        ledgerItemFor($product, 2, $variant),
    ]));

    $response->assertCreated();

    $inventory = Inventory::where('product_variant_id', $variant->id)->firstOrFail();
    expect((float) $inventory->current_stock)->toEqual(3.0);
});

test('cancelling an order mirrors the restock into the ledger', function () {
    $product = Product::factory()->create([
        'stock_unit' => 'kg',
        'stock' => 100,
        'product_type' => 'simple',
        'weight' => 5,
    ]);

    $response = $this->postJson('/api/guest-orders', ledgerOrderPayload([
        ledgerItemFor($product, 2),
    ]));
    $response->assertCreated();

    $order = ledgerOrderFromResponse($response);
    app(OrderService::class)->changeStatus($order, Order::STATUS_CANCELLED);

    expect((float) $product->fresh()->stock)->toEqual(100.0);

    $inventory = Inventory::where('product_id', $product->id)->firstOrFail();
    expect((float) $inventory->current_stock)->toEqual(100.0);

    $return = InventoryTransaction::where('inventory_id', $inventory->id)
        ->where('type', InventoryTransaction::TYPE_RETURN)
        ->firstOrFail();
    expect((float) $return->quantity_change)->toEqual(10.0)
        ->and((float) $return->quantity_after)->toEqual(100.0);
});

test('orders succeed without any warehouse configured', function () {
    $this->warehouse->delete();

    $product = Product::factory()->create([
        'stock_unit' => 'kg',
        'stock' => 100,
        'product_type' => 'simple',
        'weight' => 5,
    ]);

    $response = $this->postJson('/api/guest-orders', ledgerOrderPayload([
        ledgerItemFor($product, 2),
    ]));

    $response->assertCreated();
    expect((float) $product->fresh()->stock)->toEqual(90.0);
    $this->assertDatabaseCount('inventory', 0);
});
