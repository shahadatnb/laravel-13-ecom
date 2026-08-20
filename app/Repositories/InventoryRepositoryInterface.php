<?php

namespace App\Repositories;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface InventoryRepositoryInterface
{
    // Warehouse
    public function findWarehouse(int $id): ?Warehouse;

    public function createWarehouse(array $data): Warehouse;

    public function updateWarehouse(Warehouse $warehouse, array $data): Warehouse;

    public function deleteWarehouse(Warehouse $warehouse): void;

    public function listWarehouses(): Collection;

    public function paginateWarehouses(int $perPage = 15): LengthAwarePaginator;

    public function getDefaultWarehouse(): ?Warehouse;

    // Inventory
    public function findInventory(int $id): ?Inventory;

    public function findInventoryByProduct(int $productId, int $warehouseId, ?int $variantId = null): ?Inventory;

    public function getOrCreateInventory(int $productId, int $warehouseId, ?int $variantId = null): Inventory;

    public function updateInventory(Inventory $inventory, array $data): Inventory;

    public function paginateInventory(int $perPage = 15): LengthAwarePaginator;

    public function searchInventory(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function getLowStock(int $perPage = 15): LengthAwarePaginator;

    public function getOutOfStock(): Collection;

    public function getWarehouseStock(int $warehouseId): Collection;

    // Transactions
    public function createTransaction(array $data): InventoryTransaction;

    public function paginateTransactions(int $perPage = 15): LengthAwarePaginator;

    public function searchTransactions(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function getProductTransactions(int $productId, int $perPage = 15): LengthAwarePaginator;

    // Transfers
    public function createTransfer(StockTransfer $transfer): StockTransfer;

    public function updateTransfer(StockTransfer $transfer, array $data): StockTransfer;

    public function findTransfer(int $id): ?StockTransfer;

    public function paginateTransfers(int $perPage = 15): LengthAwarePaginator;

    // Statistics
    public function getWarehouseCount(): int;

    public function getTotalProductsInStock(): int;

    public function getLowStockCount(): int;

    public function getOutOfStockCount(): int;

    public function getTotalStockValue(): float;
}
