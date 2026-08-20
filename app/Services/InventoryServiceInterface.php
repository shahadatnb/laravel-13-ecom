<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Illuminate\Pagination\LengthAwarePaginator;

interface InventoryServiceInterface
{
    // Warehouse management
    public function createWarehouse(array $data): Warehouse;

    public function updateWarehouse(Warehouse $warehouse, array $data): Warehouse;

    public function deleteWarehouse(Warehouse $warehouse): void;

    public function listWarehouses(int $perPage = 15): LengthAwarePaginator;

    // Inventory management
    public function searchInventory(array $filters): LengthAwarePaginator;

    public function getLowStockItems(int $perPage = 15): LengthAwarePaginator;

    public function getInventoryStatistics(): array;

    // Stock operations
    public function stockIn(int $productId, int $warehouseId, int $quantity, array $data = []): Inventory;

    public function stockOut(int $productId, int $warehouseId, int $quantity, array $data = []): Inventory;

    public function adjustStock(int $productId, int $warehouseId, int $newQuantity, string $reason, array $data = []): Inventory;

    public function transferStock(int $fromWarehouseId, int $toWarehouseId, int $productId, int $quantity, array $data = []): StockTransfer;

    public function completeTransfer(StockTransfer $transfer): StockTransfer;

    public function cancelTransfer(StockTransfer $transfer): StockTransfer;

    // Reservation
    public function reserveStock(Order $order): void;

    public function releaseReservation(Order $order): void;

    public function convertReservation(Order $order): void;

    // Ledger
    public function getLedger(int $productId, int $perPage = 15): LengthAwarePaginator;

    public function searchTransactions(array $filters): LengthAwarePaginator;

    public function searchTransfers(array $filters): LengthAwarePaginator;

    public function validateStock(int $productId, int $warehouseId, int $quantity, ?int $variantId = null): bool;
}
