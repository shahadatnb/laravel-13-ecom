<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\StockReservation;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Repositories\InventoryRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InventoryService implements InventoryServiceInterface
{
    public function __construct(
        private readonly InventoryRepositoryInterface $inventoryRepository,
    ) {}

    // === Warehouse ===

    public function createWarehouse(array $data): Warehouse
    {
        if (! empty($data['is_default'])) {
            Warehouse::where('is_default', true)->update(['is_default' => false]);
        }

        return $this->inventoryRepository->createWarehouse($data);
    }

    public function updateWarehouse(Warehouse $warehouse, array $data): Warehouse
    {
        if (! empty($data['is_default'])) {
            Warehouse::where('is_default', true)->where('id', '!=', $warehouse->id)->update(['is_default' => false]);
        }

        return $this->inventoryRepository->updateWarehouse($warehouse, $data);
    }

    public function deleteWarehouse(Warehouse $warehouse): void
    {
        DB::transaction(function () use ($warehouse) {
            $warehouse->inventory()->delete();
            $this->inventoryRepository->deleteWarehouse($warehouse);
        });
    }

    public function listWarehouses(int $perPage = 15): LengthAwarePaginator
    {
        return $this->inventoryRepository->paginateWarehouses($perPage);
    }

    // === Inventory ===

    public function searchInventory(array $filters): LengthAwarePaginator
    {
        return $this->inventoryRepository->searchInventory(
            $filters,
            $filters['per_page'] ?? 15
        );
    }

    public function getLowStockItems(int $perPage = 15): LengthAwarePaginator
    {
        return $this->inventoryRepository->getLowStock($perPage);
    }

    public function getInventoryStatistics(): array
    {
        return [
            'warehouses' => $this->inventoryRepository->getWarehouseCount(),
            'products_in_stock' => $this->inventoryRepository->getTotalProductsInStock(),
            'low_stock' => $this->inventoryRepository->getLowStockCount(),
            'out_of_stock' => $this->inventoryRepository->getOutOfStockCount(),
            'total_stock_value' => $this->inventoryRepository->getTotalStockValue(),
        ];
    }

    // === Stock In ===

    public function stockIn(int $productId, int $warehouseId, int $quantity, array $data = []): Inventory
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }

        return DB::transaction(function () use ($productId, $warehouseId, $quantity, $data) {
            $variantId = $data['product_variant_id'] ?? null;
            $inventory = $this->inventoryRepository->getOrCreateInventory($productId, $warehouseId, $variantId);

            $before = $inventory->current_stock;
            $inventory->increment('current_stock', $quantity);
            $after = $inventory->fresh()->current_stock;

            if ($variantId) {
                ProductVariant::where('id', $variantId)->increment('stock', $quantity);
            }

            // Create transaction record
            $this->inventoryRepository->createTransaction([
                'inventory_id' => $inventory->id,
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'product_variant_id' => $variantId,
                'user_id' => $data['user_id'] ?? auth()->id(),
                'type' => $data['transaction_type'] ?? InventoryTransaction::TYPE_PURCHASE,
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'quantity_before' => $before,
                'quantity_change' => $quantity,
                'quantity_after' => $after,
                'unit_cost' => $data['unit_cost'] ?? null,
                'status' => 'completed',
                'reason' => $data['reason'] ?? 'Stock in',
            ]);

            return $inventory->fresh(['product', 'warehouse']);
        });
    }

    // === Stock Out ===

    public function stockOut(int $productId, int $warehouseId, int $quantity, array $data = []): Inventory
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }

        return DB::transaction(function () use ($productId, $warehouseId, $quantity, $data) {
            $variantId = $data['product_variant_id'] ?? null;
            $inventory = $this->inventoryRepository->getOrCreateInventory($productId, $warehouseId, $variantId);

            $available = $inventory->current_stock - $inventory->reserved_stock;
            if ($available < $quantity) {
                throw new \RuntimeException(
                    "Insufficient stock. Available: {$available}, Requested: {$quantity}"
                );
            }

            $before = $inventory->current_stock;
            $inventory->decrement('current_stock', $quantity);
            $after = $inventory->fresh()->current_stock;

            if ($variantId) {
                ProductVariant::where('id', $variantId)->decrement('stock', $quantity);
            }

            $this->inventoryRepository->createTransaction([
                'inventory_id' => $inventory->id,
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'product_variant_id' => $variantId,
                'user_id' => $data['user_id'] ?? auth()->id(),
                'type' => $data['transaction_type'] ?? InventoryTransaction::TYPE_SALE,
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'quantity_before' => $before,
                'quantity_change' => -$quantity,
                'quantity_after' => $after,
                'unit_cost' => $data['unit_cost'] ?? null,
                'status' => 'completed',
                'reason' => $data['reason'] ?? 'Stock out',
            ]);

            return $inventory->fresh(['product', 'warehouse']);
        });
    }

    // === Stock Adjustment ===

    public function adjustStock(int $productId, int $warehouseId, int $newQuantity, string $reason, array $data = []): Inventory
    {
        return DB::transaction(function () use ($productId, $warehouseId, $newQuantity, $reason, $data) {
            $variantId = $data['product_variant_id'] ?? null;
            $inventory = $this->inventoryRepository->getOrCreateInventory($productId, $warehouseId, $variantId);

            if ($newQuantity < 0) {
                throw new \InvalidArgumentException('Stock cannot be negative.');
            }

            $before = $inventory->current_stock;
            $change = $newQuantity - $before;

            if ($change === 0) {
                return $inventory;
            }

            $inventory->update(['current_stock' => $newQuantity]);
            $after = $newQuantity;

            if ($variantId) {
                ProductVariant::where('id', $variantId)->update(['stock' => $newQuantity]);
            }

            $type = $change > 0
                ? InventoryTransaction::TYPE_ADJUSTMENT
                : InventoryTransaction::TYPE_ADJUSTMENT;

            $this->inventoryRepository->createTransaction([
                'inventory_id' => $inventory->id,
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'product_variant_id' => $variantId,
                'user_id' => $data['user_id'] ?? auth()->id(),
                'type' => $type,
                'reference_type' => 'adjustment',
                'quantity_before' => $before,
                'quantity_change' => $change,
                'quantity_after' => $after,
                'status' => 'completed',
                'reason' => $reason,
            ]);

            return $inventory->fresh(['product', 'warehouse']);
        });
    }

    // === Stock Transfer ===

    public function transferStock(int $fromWarehouseId, int $toWarehouseId, int $productId, int $quantity, array $data = []): StockTransfer
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }

        if ($fromWarehouseId === $toWarehouseId) {
            throw new \InvalidArgumentException('Source and destination warehouses must be different.');
        }

        return DB::transaction(function () use ($fromWarehouseId, $toWarehouseId, $productId, $quantity, $data) {
            // Validate stock availability
            $variantId = $data['product_variant_id'] ?? null;
            $fromInventory = $this->inventoryRepository->findInventoryByProduct($productId, $fromWarehouseId, $variantId);

            if (! $fromInventory || ($fromInventory->current_stock - $fromInventory->reserved_stock) < $quantity) {
                $available = $fromInventory ? ($fromInventory->current_stock - $fromInventory->reserved_stock) : 0;
                throw new \RuntimeException(
                    "Insufficient stock in source warehouse. Available: {$available}, Requested: {$quantity}"
                );
            }

            // Generate transfer number
            $prefix = 'TRF-'.now()->format('Ymd');
            $last = StockTransfer::where('transfer_number', 'like', "{$prefix}%")
                ->orderByDesc('id')->first();
            $seq = $last ? ((int) substr($last->transfer_number, -4)) + 1 : 1;
            $transferNumber = "{$prefix}-".str_pad((string) $seq, 4, '0', STR_PAD_LEFT);

            // Create transfer record
            $transfer = StockTransfer::create([
                'transfer_number' => $transferNumber,
                'from_warehouse_id' => $fromWarehouseId,
                'to_warehouse_id' => $toWarehouseId,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
                'unit_cost' => $data['unit_cost'] ?? null,
                'status' => StockTransfer::STATUS_PENDING,
                'requested_by' => $data['user_id'] ?? auth()->id(),
                'notes' => $data['notes'] ?? null,
            ]);

            // Stock out from source
            $this->stockOut($productId, $fromWarehouseId, $quantity, [
                'product_variant_id' => $variantId,
                'user_id' => $data['user_id'] ?? auth()->id(),
                'transaction_type' => InventoryTransaction::TYPE_TRANSFER_OUT,
                'reference_type' => 'transfer',
                'reference_id' => $transfer->id,
                'reference_number' => $transferNumber,
                'reason' => "Transfer to warehouse #{$toWarehouseId}",
                'unit_cost' => $data['unit_cost'] ?? null,
            ]);

            return $transfer->fresh(['fromWarehouse', 'toWarehouse', 'product']);
        });
    }

    public function completeTransfer(StockTransfer $transfer): StockTransfer
    {
        return DB::transaction(function () use ($transfer) {
            // Stock in at destination
            $this->stockIn(
                $transfer->product_id,
                $transfer->to_warehouse_id,
                $transfer->quantity,
                [
                    'product_variant_id' => $transfer->product_variant_id,
                    'user_id' => auth()->id(),
                    'transaction_type' => InventoryTransaction::TYPE_TRANSFER_IN,
                    'reference_type' => 'transfer',
                    'reference_id' => $transfer->id,
                    'reference_number' => $transfer->transfer_number,
                    'reason' => "Transfer from warehouse #{$transfer->from_warehouse_id}",
                    'unit_cost' => $transfer->unit_cost,
                ]
            );

            return $this->inventoryRepository->updateTransfer($transfer, [
                'status' => StockTransfer::STATUS_COMPLETED,
                'received_at' => now(),
                'received_by' => auth()->id(),
            ]);
        });
    }

    public function cancelTransfer(StockTransfer $transfer): StockTransfer
    {
        return DB::transaction(function () use ($transfer) {
            // Reverse the stock-out from source
            $this->stockIn(
                $transfer->product_id,
                $transfer->from_warehouse_id,
                $transfer->quantity,
                [
                    'product_variant_id' => $transfer->product_variant_id,
                    'user_id' => auth()->id(),
                    'transaction_type' => InventoryTransaction::TYPE_ADJUSTMENT,
                    'reference_type' => 'transfer',
                    'reference_id' => $transfer->id,
                    'reference_number' => $transfer->transfer_number,
                    'reason' => "Transfer cancelled - stock returned to warehouse #{$transfer->from_warehouse_id}",
                    'unit_cost' => $transfer->unit_cost,
                ]
            );

            return $this->inventoryRepository->updateTransfer($transfer, [
                'status' => StockTransfer::STATUS_CANCELLED,
            ]);
        });
    }

    // === Stock Reservation ===

    public function reserveStock(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $warehouseId = $this->inventoryRepository->getDefaultWarehouse()?->id;
            if (! $warehouseId) {
                throw new \RuntimeException('No warehouse configured.');
            }

            foreach ($order->items as $item) {
                $inventory = $this->inventoryRepository->findInventoryByProduct(
                    $item->product_id,
                    $warehouseId,
                    $item->product_variant_id
                );

                if (! $inventory || $inventory->current_stock < $item->quantity) {
                    throw new \RuntimeException(
                        "Insufficient stock for product: {$item->product_name}"
                    );
                }

                // Create reservation
                StockReservation::create([
                    'inventory_id' => $inventory->id,
                    'product_id' => $item->product_id,
                    'warehouse_id' => $warehouseId,
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'quantity' => $item->quantity,
                    'status' => StockReservation::STATUS_ACTIVE,
                ]);

                // Increment reserved stock
                $inventory->increment('reserved_stock', $item->quantity);
            }
        });
    }

    public function releaseReservation(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $reservations = StockReservation::where('order_id', $order->id)
                ->where('status', StockReservation::STATUS_ACTIVE)
                ->get();

            foreach ($reservations as $reservation) {
                $inventory = $reservation->inventory;
                $inventory->decrement('reserved_stock', $reservation->quantity);

                $reservation->update([
                    'status' => StockReservation::STATUS_RELEASED,
                    'released_at' => now(),
                ]);
            }
        });
    }

    public function convertReservation(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $reservations = StockReservation::where('order_id', $order->id)
                ->where('status', StockReservation::STATUS_ACTIVE)
                ->get();

            foreach ($reservations as $reservation) {
                $inventory = $reservation->inventory;

                // Reduce actual stock and release reservation
                $before = $inventory->current_stock;
                $inventory->decrement('current_stock', $reservation->quantity);
                $inventory->decrement('reserved_stock', $reservation->quantity);
                $after = $inventory->fresh()->current_stock;

                $reservation->update([
                    'status' => StockReservation::STATUS_CONVERTED,
                    'released_at' => now(),
                ]);

                // Create sale transaction
                $this->inventoryRepository->createTransaction([
                    'inventory_id' => $inventory->id,
                    'product_id' => $reservation->product_id,
                    'warehouse_id' => $reservation->warehouse_id,
                    'user_id' => $order->user_id,
                    'type' => InventoryTransaction::TYPE_SALE,
                    'reference_type' => 'order',
                    'reference_id' => $order->id,
                    'reference_number' => $order->order_number,
                    'quantity_before' => $before,
                    'quantity_change' => -$reservation->quantity,
                    'quantity_after' => $after,
                    'status' => 'completed',
                    'reason' => "Order #{$order->order_number} completed",
                ]);
            }
        });
    }

    // === Ledger ===

    public function getLedger(int $productId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->inventoryRepository->getProductTransactions($productId, $perPage);
    }

    public function searchTransactions(array $filters): LengthAwarePaginator
    {
        return $this->inventoryRepository->searchTransactions(
            $filters,
            $filters['per_page'] ?? 15
        );
    }

    public function searchTransfers(array $filters): LengthAwarePaginator
    {
        $query = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'product', 'variant', 'requester']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['from_warehouse_id'])) {
            $query->where('from_warehouse_id', $filters['from_warehouse_id']);
        }

        if (isset($filters['to_warehouse_id'])) {
            $query->where('to_warehouse_id', $filters['to_warehouse_id']);
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

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function validateStock(int $productId, int $warehouseId, int $quantity, ?int $variantId = null): bool
    {
        $inventory = $this->inventoryRepository->findInventoryByProduct($productId, $warehouseId, $variantId);

        if (! $inventory) {
            return false;
        }

        return ($inventory->current_stock - $inventory->reserved_stock) >= $quantity;
    }
}
