<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderServiceInterface
{
    public function getOrder(int $id): ?Order;

    public function getCustomerOrders(int $customerId, int $perPage = 15): LengthAwarePaginator;

    /** @deprecated Use getCustomerOrders() instead. */
    public function getUserOrders(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function createOrder(array $data): Order;

    public function updateOrder(Order $order, array $data): Order;

    public function deleteOrder(Order $order): void;

    public function listOrders(int $perPage = 15): LengthAwarePaginator;

    public function searchOrders(array $filters): LengthAwarePaginator;

    public function getOrderStatistics(): array;

    public function changeStatus(Order $order, string $newStatus, ?string $notes = null): Order;

    public function updatePaymentStatus(Order $order, string $paymentStatus): Order;

    public function updateShippingStatus(Order $order, string $shippingStatus): Order;

    public function getOrdersByDateRange(string $startDate, string $endDate, int $perPage = 15): LengthAwarePaginator;

    public function generateOrderNumber(): string;
}
