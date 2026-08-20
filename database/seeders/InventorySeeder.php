<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        // Create default warehouse
        $mainWarehouse = Warehouse::create([
            'name' => 'Main Warehouse',
            'code' => 'WH-MAIN',
            'address' => '123 Commerce Street',
            'city' => 'New York',
            'state' => 'NY',
            'country' => 'USA',
            'zip_code' => '10001',
            'phone' => '+1-555-0100',
            'email' => 'warehouse@example.com',
            'manager_name' => 'John Stockman',
            'is_default' => true,
            'status' => 'active',
            'created_by' => $admin?->id,
        ]);

        // Secondary warehouse
        $secondaryWarehouse = Warehouse::create([
            'name' => 'Secondary Warehouse',
            'code' => 'WH-SEC',
            'address' => '456 Industrial Blvd',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'country' => 'USA',
            'zip_code' => '90001',
            'phone' => '+1-555-0200',
            'manager_name' => 'Jane Warehouse',
            'is_default' => false,
            'status' => 'active',
            'created_by' => $admin?->id,
        ]);

        // Create additional warehouses if none exist
        if (Warehouse::count() <= 2) {
            Warehouse::factory(3)->active()->create();
        }

        // Create inventory records for existing products
        $products = Product::limit(20)->get();

        if ($products->count() > 0) {
            $warehouses = Warehouse::active()->get();

            foreach ($products as $product) {
                foreach ($warehouses->take(2) as $warehouse) {
                    $currentStock = fake()->numberBetween(0, 200);
                    $reservedStock = fake()->numberBetween(0, min(20, $currentStock));

                    $inventory = Inventory::create([
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouse->id,
                        'product_variant_id' => null,
                        'current_stock' => $currentStock,
                        'reserved_stock' => $reservedStock,
                        'minimum_stock' => fake()->numberBetween(5, 30),
                        'maximum_stock' => fake()->optional()->numberBetween(100, 500),
                        'reorder_level' => fake()->numberBetween(10, 50),
                        'location' => fake()->optional()->regexify('[A-Z][0-9]{2}-[A-Z][0-9]{2}'),
                    ]);

                    // Create opening balance transaction
                    InventoryTransaction::create([
                        'inventory_id' => $inventory->id,
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouse->id,
                        'user_id' => $admin?->id,
                        'type' => InventoryTransaction::TYPE_OPENING_BALANCE,
                        'quantity_before' => 0,
                        'quantity_change' => $currentStock,
                        'quantity_after' => $currentStock,
                        'status' => 'completed',
                        'reason' => 'Opening balance',
                    ]);
                }
            }
        } else {
            // No products exist yet, just create a few inventory entries with product factories
            Inventory::factory(10)->inStock()->create();
            Inventory::factory(3)->lowStock()->create();
            Inventory::factory(2)->outOfStock()->create();
        }
    }
}
