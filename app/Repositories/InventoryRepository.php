<?php

namespace App\Repositories;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InventoryRepository implements InventoryRepositoryInterface
{
    // === Warehouses ===

    public function findWarehouse(int $id): ?Warehouse
    {
        return Warehouse::with(['createdBy'])->find($id);
    }

    public function createWarehouse(array $data): Warehouse
    {
        return Warehouse::create($data);
    }

    public function updateWarehouse(Warehouse $warehouse, array $data): Warehouse
    {
        $warehouse->update($data);

        return $warehouse->fresh();
    }

    public function deleteWarehouse(Warehouse $warehouse): void
    {
        $warehouse->delete();
    }

    public function listWarehouses(): Collection
    {
        return Warehouse::active()->orderBy('name')->get();
    }

    public function paginateWarehouses(int $perPage = 15): LengthAwarePaginator
    {
        return Warehouse::with(['createdBy'])->orderByDesc('id')->paginate($perPage);
    }

    public function getDefaultWarehouse(): ?Warehouse
    {
        return Warehouse::where('is_default', true)->first()
            ?? Warehouse::active()->first();
    }

    // === Inventory ===

    public function findInventory(int $id): ?Inventory
    {
        return Inventory::with(['product', 'warehouse', 'variant', 'transactions' => function ($q) {
            $q->latest()->limit(10);
        }])->find($id);
    }

    public function findInventoryByProduct(int $productId, int $warehouseId, ?int $variantId = null): ?Inventory
    {
        $query = Inventory::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId);

        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        } else {
            $query->whereNull('product_variant_id');
        }

        return $query->first();
    }

    public function getOrCreateInventory(int $productId, int $warehouseId, ?int $variantId = null): Inventory
    {
        $inventory = $this->findInventoryByProduct($productId, $warehouseId, $variantId);

        if (! $inventory) {
            $inventory = Inventory::create([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'product_variant_id' => $variantId,
                'current_stock' => 0,
                'reserved_stock' => 0,
                'minimum_stock' => 0,
                'reorder_level' => 0,
            ]);
        }

        return $inventory;
    }

    public function updateInventory(Inventory $inventory, array $data): Inventory
    {
        $inventory->update($data);

        return $inventory->fresh(['product', 'warehouse']);
    }

    public function paginateInventory(int $perPage = 15): LengthAwarePaginator
    {
        return Inventory::with(['product', 'warehouse', 'variant'])
            ->orderByDesc('current_stock')
            ->paginate($perPage);
    }

    public function searchInventory(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Inventory::with(['product', 'warehouse', 'variant']);

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                })->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if (isset($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        if (isset($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (isset($filters['low_stock']) && $filters['low_stock']) {
            $query->whereColumn('current_stock', '<=', 'minimum_stock')
                ->where('minimum_stock', '>', 0);
        }

        if (isset($filters['out_of_stock']) && $filters['out_of_stock']) {
            $query->where('current_stock', '<=', 0);
        }

        if (isset($filters['stock_status'])) {
            match ($filters['stock_status']) {
                'low' => $query->whereColumn('current_stock', '<=', 'minimum_stock')->where('minimum_stock', '>', 0),
                'out' => $query->where('current_stock', '<=', 0),
                'available' => $query->where('current_stock', '>', 0),
                default => null,
            };
        }

        // Variant attribute filtering (JSON_EXTRACT)
        if (!empty($filters['attr_name']) && !empty($filters['attr_value'])) {
            $attrName = $filters['attr_name'];
            $attrValue = $filters['attr_value'];
            $query->whereHas('variant', function ($q) use ($attrName, $attrValue) {
                $q->whereRaw(
                    "JSON_EXTRACT(attributes, ?) = ?",
                    ["$.\"{$attrName}\"", json_encode($attrValue)]
                );
            });
        }

        $sortField = $filters['sort'] ?? 'current_stock';
        $sortDir = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDir);

        return $query->paginate($perPage);
    }

    public function getLowStock(int $perPage = 15): LengthAwarePaginator
    {
        return Inventory::with(['product', 'warehouse'])
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('minimum_stock', '>', 0)
            ->orderBy('current_stock')
            ->paginate($perPage);
    }

    public function getOutOfStock(): Collection
    {
        return Inventory::with(['product', 'warehouse'])
            ->where('current_stock', '<=', 0)
            ->get();
    }

    public function getWarehouseStock(int $warehouseId): Collection
    {
        return Inventory::with(['product'])
            ->where('warehouse_id', $warehouseId)
            ->where('current_stock', '>', 0)
            ->orderByDesc('current_stock')
            ->get();
    }

    // === Transactions ===

    public function createTransaction(array $data): InventoryTransaction
    {
        return InventoryTransaction::create($data);
    }

    public function paginateTransactions(int $perPage = 15): LengthAwarePaginator
    {
        return InventoryTransaction::with(['product', 'warehouse', 'user'])
            ->latest()
            ->paginate($perPage);
    }

    public function searchTransactions(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = InventoryTransaction::with(['product', 'warehouse', 'user']);

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        if (isset($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function getProductTransactions(int $productId, int $perPage = 15): LengthAwarePaginator
    {
        return InventoryTransaction::with(['warehouse', 'user'])
            ->where('product_id', $productId)
            ->latest()
            ->paginate($perPage);
    }

    // === Transfers ===

    public function createTransfer(StockTransfer $transfer): StockTransfer
    {
        return $transfer->save();
    }

    public function updateTransfer(StockTransfer $transfer, array $data): StockTransfer
    {
        $transfer->update($data);

        return $transfer->fresh(['fromWarehouse', 'toWarehouse', 'product', 'variant']);
    }

    public function findTransfer(int $id): ?StockTransfer
    {
        return StockTransfer::with(['fromWarehouse', 'toWarehouse', 'product', 'variant', 'requester', 'approver', 'receiver'])->find($id);
    }

    public function paginateTransfers(int $perPage = 15): LengthAwarePaginator
    {
        return StockTransfer::with(['fromWarehouse', 'toWarehouse', 'product'])
            ->latest()
            ->paginate($perPage);
    }

    // === Statistics ===

    public function getWarehouseCount(): int
    {
        return Warehouse::count();
    }

    public function getTotalProductsInStock(): int
    {
        return Inventory::where('current_stock', '>', 0)->count();
    }

    public function getLowStockCount(): int
    {
        return Inventory::whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('minimum_stock', '>', 0)
            ->count();
    }

    public function getOutOfStockCount(): int
    {
        return Inventory::where('current_stock', '<=', 0)->count();
    }

    public function getTotalStockValue(): float
    {
        return (float) Inventory::join('products', 'inventory.product_id', '=', 'products.id')
            ->select(DB::raw('SUM(inventory.current_stock * COALESCE(products.cost_price, 0)) as total_value'))
            ->value('total_value');
    }
}
