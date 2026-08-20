<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    public function find(int $id): ?Order
    {
        return Order::with(['customer', 'items.product.images', 'statusHistories'])
            ->find($id);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return Order::with(['customer', 'items.product.images', 'statusHistories'])
            ->where('order_number', $orderNumber)
            ->first();
    }

    public function findByCustomer(int $customerId, int $perPage = 15): LengthAwarePaginator
    {
        return Order::with(['items.product.images'])
            ->where('customer_id', $customerId)
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * @deprecated Use findByCustomer() instead.
     */
    public function findByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->findByCustomer($userId, $perPage);
    }

    public function getAll(): Collection
    {
        return Order::with(['customer', 'items'])
            ->orderByDesc('id')
            ->get();
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(Order $order, array $data): Order
    {
        $order->update($data);

        return $order->fresh(['customer', 'items.product.images']);
    }

    public function delete(Order $order): void
    {
        $order->delete();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Order::with(['customer', 'items.product.images'])
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function search(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Order::with(['customer', 'items.product.images']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('guest_email', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        // Backward compatibility: also accept user_id
        if (isset($filters['user_id']) && ! isset($filters['customer_id'])) {
            $query->where('customer_id', $filters['user_id']);
        }

        if (isset($filters['sort'])) {
            $direction = $filters['sort_direction'] ?? 'desc';
            $query->orderBy($filters['sort'], $direction);
        }

        return $query->paginate($perPage);
    }

    public function count(): int
    {
        return Order::count();
    }

    public function countByStatus(string $status): int
    {
        return Order::where('status', $status)->count();
    }

    public function countPending(): int
    {
        return Order::where('status', Order::STATUS_PENDING)->count();
    }

    public function countProcessing(): int
    {
        return Order::where('status', Order::STATUS_PROCESSING)->count();
    }

    public function countCompleted(): int
    {
        return Order::where('status', Order::STATUS_COMPLETED)->count();
    }

    public function countCancelled(): int
    {
        return Order::where('status', Order::STATUS_CANCELLED)->count();
    }

    public function revenueBetween(string $startDate, string $endDate): float
    {
        return (float) Order::where('payment_status', Order::PAYMENT_PAID)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('grand_total');
    }

    public function ordersBetween(string $startDate, string $endDate, int $perPage = 15): LengthAwarePaginator
    {
        return Order::with(['customer'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('id')
            ->paginate($perPage);
    }
}
