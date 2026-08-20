<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Repositories\OrderRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
    ) {}

    public function getOrder(int $id): ?Order
    {
        return $this->orderRepository->find($id);
    }

    public function getCustomerOrders(int $customerId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->findByCustomer($customerId, $perPage);
    }

    /** @deprecated Use getCustomerOrders() instead. */
    public function getUserOrders(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->getCustomerOrders($userId, $perPage);
    }

    public function createOrder(array $data): Order
    {
        // ── Stock Validation ──
        // Validate sufficient stock for every item BEFORE starting the transaction
        if (isset($data['items']) && is_array($data['items'])) {
            $errors = [];

            foreach ($data['items'] as $index => $item) {
                $productId = $item['product_id'] ?? null;
                $variantId = $item['product_variant_id'] ?? null;
                $requestedQty = $item['quantity'] ?? 1;
                $productName = $item['product_name'] ?? "Product #{$productId}";

                if ($variantId) {
                    // Check variant stock
                    $variant = ProductVariant::find($variantId);
                    if (! $variant) {
                        $errors["items.{$index}.product_variant_id"] = [
                            "{$productName}: selected variant does not exist.",
                        ];

                        continue;
                    }

                    $availableStock = (int) ($variant->stock ?? 0);
                    if ($requestedQty > $availableStock) {
                        $errors["items.{$index}.quantity"] = [
                            "{$productName} ({$variant->name}): only {$availableStock} in stock, but {$requestedQty} requested.",
                        ];
                    }
                } else {
                    // Check simple product stock
                    $product = Product::find($productId);
                    if (! $product) {
                        $errors["items.{$index}.product_id"] = [
                            "{$productName}: product does not exist.",
                        ];

                        continue;
                    }

                    $availableStock = (int) ($product->stock ?? 0);
                    if ($requestedQty > $availableStock) {
                        $errors["items.{$index}.quantity"] = [
                            "{$productName}: only {$availableStock} in stock, but {$requestedQty} requested.",
                        ];
                    }
                }
            }

            if (! empty($errors)) {
                throw ValidationException::withMessages($errors);
            }
        }

        return DB::transaction(function () use ($data) {
            $data['order_number'] = $this->generateOrderNumber();
            $data['status'] = $data['status'] ?? Order::STATUS_PENDING;
            $data['payment_status'] = $data['payment_status'] ?? Order::PAYMENT_PENDING;
            $data['shipping_status'] = $data['shipping_status'] ?? Order::SHIPPING_PENDING;

            // Calculate totals
            $subtotal = 0;
            $discount = $data['discount'] ?? 0;
            $tax = $data['tax'] ?? 0;
            $shippingCharge = $data['shipping_charge'] ?? 0;

            if (isset($data['items'])) {
                foreach ($data['items'] as $item) {
                    $subtotal += ($item['unit_price'] ?? 0) * ($item['quantity'] ?? 1);
                }
            }

            $grandTotal = ($subtotal - $discount) + $tax + $shippingCharge;

            $data['subtotal'] = $subtotal;
            $data['grand_total'] = max($grandTotal, 0);
            $data['due_amount'] = $data['grand_total'] - ($data['paid_amount'] ?? 0);

            $order = $this->orderRepository->create($data);

            // Create items
            if (isset($data['items'])) {
                foreach ($data['items'] as $itemData) {
                    $itemSubtotal = ($itemData['unit_price'] ?? 0) * ($itemData['quantity'] ?? 1);
                    $itemTotal = $itemSubtotal - ($itemData['discount'] ?? 0) + ($itemData['tax'] ?? 0);

                    $order->items()->create([
                        'product_id' => $itemData['product_id'],
                        'product_variant_id' => $itemData['product_variant_id'] ?? null,
                        'product_name' => $itemData['product_name'] ?? '',
                        'product_sku' => $itemData['product_sku'] ?? null,
                        'unit_price' => $itemData['unit_price'] ?? 0,
                        'wholesale_price' => $itemData['wholesale_price'] ?? null,
                        'quantity' => $itemData['quantity'] ?? 1,
                        'subtotal' => $itemSubtotal,
                        'discount' => $itemData['discount'] ?? 0,
                        'tax' => $itemData['tax'] ?? 0,
                        'total' => $itemTotal,
                        'variant_attributes' => $itemData['variant_attributes'] ?? null,
                    ]);
                }
            }

            // Log status history
            $order->statusHistories()->create([
                'from_status' => null,
                'to_status' => $order->status,
                'changed_by_type' => 'user',
                'changed_by' => $data['created_by'] ?? $data['customer_id'] ?? $data['user_id'] ?? null,
                'notes' => 'Order created',
            ]);

            // Attribute the order to the affiliate whose link brought the buyer.
            if (! empty($data['referrer_code'])) {
                $order->load('items');
            }

            return $order->fresh(['customer', 'items.product']);
        });
    }

    public function updateOrder(Order $order, array $data): Order
    {
        return DB::transaction(function () use ($order, $data) {
            return $this->orderRepository->update($order, $data);
        });
    }

    public function deleteOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->items()->delete();
            $order->statusHistories()->delete();
            $this->orderRepository->delete($order);
        });
    }

    public function listOrders(int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->paginate($perPage);
    }

    public function searchOrders(array $filters): LengthAwarePaginator
    {
        return $this->orderRepository->search($filters, $filters['per_page'] ?? 15);
    }

    public function getOrderStatistics(): array
    {
        return [
            'total' => $this->orderRepository->count(),
            'pending' => $this->orderRepository->countPending(),
            'processing' => $this->orderRepository->countProcessing(),
            'completed' => $this->orderRepository->countCompleted(),
            'cancelled' => $this->orderRepository->countCancelled(),
            'total_revenue' => $this->orderRepository->revenueBetween(
                now()->startOfYear()->toDateString(),
                now()->toDateString()
            ),
        ];
    }

    public function changeStatus(Order $order, string $newStatus, ?string $notes = null): Order
    {
        return DB::transaction(function () use ($order, $newStatus, $notes) {
            $oldStatus = $order->status;

            $order = $this->orderRepository->update($order, ['status' => $newStatus]);

            $order->statusHistories()->create([
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'changed_by_type' => 'admin',
                'changed_by' => auth()->id(),
                'notes' => $notes,
            ]);

            // Trigger commission generation when order is completed
            if ($newStatus === Order::STATUS_COMPLETED && $order->isPaid()) {
            }

            // Reverse commissions when order is cancelled/refunded/returned
            if (in_array($newStatus, [Order::STATUS_CANCELLED, Order::STATUS_REFUNDED, Order::STATUS_RETURNED])) {
            }

            return $order;
        });
    }

    public function updatePaymentStatus(Order $order, string $paymentStatus): Order
    {
        return DB::transaction(function () use ($order, $paymentStatus) {
            $oldStatus = $order->payment_status;

            $data = ['payment_status' => $paymentStatus];

            if ($paymentStatus === Order::PAYMENT_PAID) {
                $data['paid_amount'] = $order->grand_total;
                $data['due_amount'] = 0;
            }

            $order = $this->orderRepository->update($order, $data);

            $order->statusHistories()->create([
                'from_status' => $oldStatus,
                'to_status' => $paymentStatus,
                'changed_by_type' => 'system',
                'changed_by' => auth()->id(),
                'notes' => "Payment status changed from {$oldStatus} to {$paymentStatus}",
            ]);

            // Trigger commission if order becomes paid while already completed
            if ($paymentStatus === Order::PAYMENT_PAID && $order->isCompleted()) {
            }

            return $order;
        });
    }

    public function updateShippingStatus(Order $order, string $shippingStatus): Order
    {
        return DB::transaction(function () use ($order, $shippingStatus) {
            $oldStatus = $order->shipping_status;

            $order = $this->orderRepository->update($order, ['shipping_status' => $shippingStatus]);

            $order->statusHistories()->create([
                'from_status' => $oldStatus,
                'to_status' => $shippingStatus,
                'changed_by_type' => 'admin',
                'changed_by' => auth()->id(),
                'notes' => "Shipping status changed from {$oldStatus} to {$shippingStatus}",
            ]);

            return $order;
        });
    }

    public function getOrdersByDateRange(string $startDate, string $endDate, int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->ordersBetween($startDate, $endDate, $perPage);
    }

    public function generateOrderNumber(): string
    {
        $prefix = 'ORD-';
        $date = now()->format('Ymd');
        $lastOrder = Order::whereDate('created_at', today())
            ->orderByDesc('id')
            ->first();

        $sequence = $lastOrder ? ((int) substr($lastOrder->order_number, -6)) + 1 : 1;
        $sequencePadded = str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);

        return $prefix.$date.'-'.$sequencePadded;
    }
}
