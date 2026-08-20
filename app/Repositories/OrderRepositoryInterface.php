<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function find(int $id): ?Order;

    public function findByOrderNumber(string $orderNumber): ?Order;

    public function findByCustomer(int $customerId, int $perPage = 15): LengthAwarePaginator;

    /** @deprecated Use findByCustomer() instead. */
    public function findByUser(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function getAll(): Collection;

    public function create(array $data): Order;

    public function update(Order $order, array $data): Order;

    public function delete(Order $order): void;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function search(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function count(): int;

    public function countByStatus(string $status): int;

    public function countPending(): int;

    public function countProcessing(): int;

    public function countCompleted(): int;

    public function countCancelled(): int;

    public function revenueBetween(string $startDate, string $endDate): float;

    public function ordersBetween(string $startDate, string $endDate, int $perPage = 15): LengthAwarePaginator;
}
