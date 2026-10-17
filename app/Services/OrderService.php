<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SiteSetting;
use App\Repositories\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly DeliveryZoneService $deliveryZoneService,
        private readonly InventoryServiceInterface $inventoryService,
        private readonly CouponServiceInterface $couponService,
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
        return DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];

            // ── Server-side pricing ──
            // Unit prices and quantities always resolve from the database so
            // a tampered client payload cannot change what the customer pays.
            $context = $this->loadStockContext($items);
            $pricing = $this->resolveItemPricing($items, $context);

            $shippingCharge = $this->calculateShippingCharge($data['shipping_address'] ?? null, $pricing['subtotal']);
            $tax = $this->calculateTax($pricing['subtotal']);

            // A coupon must validate server-side; any client-sent discount
            // amount is ignored entirely.
            $coupon = null;
            if (! empty($data['coupon_code'])) {
                $customerId = isset($data['customer_id']) ? (int) $data['customer_id'] : (isset($data['user_id']) ? (int) $data['user_id'] : null);
                $coupon = $this->validateOrderCoupon((string) $data['coupon_code'], $customerId, $pricing['subtotal']);
            }

            $grandTotal = max($pricing['subtotal'] + $tax + $shippingCharge, 0);

            $data['order_number'] = $this->generateOrderNumber();
            $data['status'] = $data['status'] ?? Order::STATUS_PENDING;
            $data['payment_status'] = $data['payment_status'] ?? Order::PAYMENT_PENDING;
            $data['shipping_status'] = $data['shipping_status'] ?? Order::SHIPPING_PENDING;

            $data['subtotal'] = $pricing['subtotal'];
            $data['discount'] = 0;
            $data['tax'] = $tax;
            $data['shipping_charge'] = $shippingCharge;
            $data['grand_total'] = $grandTotal;
            $data['due_amount'] = $grandTotal - ($data['paid_amount'] ?? 0);

            $order = $this->orderRepository->create($data);

            foreach ($pricing['items'] as $index => $line) {
                $itemSubtotal = $line['unit_price'] * $line['quantity'];

                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'product_variant_id' => $line['variant']?->id,
                    'product_name' => $items[$index]['product_name'] ?? '',
                    'product_sku' => $items[$index]['product_sku'] ?? null,
                    'unit_price' => $line['unit_price'],
                    'wholesale_price' => $line['product']->wholesale_price,
                    'quantity' => $line['quantity'],
                    'subtotal' => $itemSubtotal,
                    'discount' => 0,
                    'tax' => 0,
                    'total' => $itemSubtotal,
                    'variant_attributes' => $items[$index]['variant_attributes'] ?? null,
                ]);
            }

            // Log status history
            $order->statusHistories()->create([
                'from_status' => null,
                'to_status' => $order->status,
                'changed_by_type' => 'user',
                'changed_by' => $data['created_by'] ?? $data['customer_id'] ?? $data['user_id'] ?? null,
                'notes' => 'Order created',
            ]);

            // ── Stock validation + deduction ──
            // Runs after the order rows exist so warehouse ledger entries can
            // reference the order. Throws ValidationException (rolling the
            // transaction back) when any item lacks stock.
            $stockDeducted = $this->validateAndDeductStock($items, $context, $order);

            if ($stockDeducted) {
                $order->update(['stock_deducted_at' => now()]);
            }

            // Apply the validated coupon: records usage and adjusts totals.
            if ($coupon !== null) {
                $this->couponService->applyCoupon($coupon, $order);
            }

            // Attribute the order to the affiliate whose link brought the buyer.
            if (! empty($data['referrer_code'])) {
                $order->load('items');
            }

            return $order->fresh(['customer', 'items.product']);
        });
    }

    /**
     * Load the products and variants referenced by the cart under row locks
     * so concurrent orders cannot oversell.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array{products: Collection<int, Product>, variants: Collection<int, ProductVariant>}
     */
    private function loadStockContext(array $items): array
    {
        $productIds = [];
        $variantIds = [];

        foreach ($items as $item) {
            if (! empty($item['product_id'])) {
                $productIds[] = (int) $item['product_id'];
            }

            if (! empty($item['product_variant_id'])) {
                $variantIds[] = (int) $item['product_variant_id'];
            }
        }

        return [
            'products' => Product::whereIn('id', array_unique($productIds))
                ->lockForUpdate()
                ->get()
                ->keyBy('id'),
            'variants' => ProductVariant::whereIn('id', array_unique($variantIds))
                ->lockForUpdate()
                ->get()
                ->keyBy('id'),
        ];
    }

    /**
     * Resolve each item's unit price and quantity server-side.
     *
     * The unit price comes from the variant (falling back to the product's
     * sale/regular price) and the quantity is clamped to a positive integer,
     * so a tampered payload cannot change what the customer pays.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @param  array{products: Collection<int, Product>, variants: Collection<int, ProductVariant>}  $context
     * @return array{items: array<int, array{unit_price: float, quantity: int, product: Product, variant: ProductVariant|null}>, subtotal: float}
     *
     * @throws ValidationException when a product or variant is missing
     */
    private function resolveItemPricing(array $items, array $context): array
    {
        $resolved = [];
        $subtotal = 0.0;
        $errors = [];

        foreach ($items as $index => $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $variantId = ! empty($item['product_variant_id']) ? (int) $item['product_variant_id'] : null;
            $productName = $item['product_name'] ?? "Product #{$productId}";

            $product = $context['products']->get($productId);

            if (! $product) {
                $errors["items.{$index}.product_id"] = ["{$productName}: product does not exist."];

                continue;
            }

            $variant = null;
            if ($variantId !== null) {
                $variant = $context['variants']->get($variantId);

                if (! $variant || (int) $variant->product_id !== $productId) {
                    $errors["items.{$index}.product_variant_id"] = ["{$productName}: selected variant does not exist."];

                    continue;
                }
            }

            $price = $this->resolveUnitPrice($product, $variant);
            $quantity = max(1, (int) ($item['quantity'] ?? 1));

            $resolved[$index] = [
                'unit_price' => $price,
                'quantity' => $quantity,
                'product' => $product,
                'variant' => $variant,
            ];

            $subtotal += $price * $quantity;
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return ['items' => $resolved, 'subtotal' => round($subtotal, 2)];
    }

    /**
     * Resolve the unit price a customer pays for one item line.
     *
     * A variant's own price always wins. Otherwise the product-level rate
     * applies — but on a kilogram pool (stock_unit = 'kg') those rates are
     * per kilogram while a variant represents a whole pack, so the fallback
     * is scaled by the pack's unit weight (৳60/kg × 5 kg = ৳300).
     */
    private function resolveUnitPrice(Product $product, ?ProductVariant $variant): float
    {
        if ($variant !== null && $variant->price !== null) {
            return (float) $variant->price;
        }

        $rate = (float) ($product->sale_price ?? $product->regular_price ?? 0);

        if ($variant !== null && $product->stock_unit === 'kg') {
            $weight = (float) ($variant->unit_weight ?? 0);

            if ($weight > 0 && $rate > 0) {
                return round($rate * $weight, 2);
            }
        }

        return $rate;
    }

    /**
     * Validate a coupon against the server-resolved subtotal. Any
     * client-computed discount amount is ignored; only a coupon that passes
     * all server-side rules contributes a discount.
     *
     * @throws ValidationException when the coupon is not applicable
     */
    private function validateOrderCoupon(string $code, ?int $customerId, float $subtotal): Coupon
    {
        $result = $this->couponService->validateCoupon($code, $customerId, ['order_amount' => $subtotal]);

        if (! ($result['valid'] ?? false)) {
            throw ValidationException::withMessages([
                'coupon_code' => [$result['error'] ?? 'The coupon could not be applied.'],
            ]);
        }

        return $result['coupon'];
    }

    /**
     * Validate stock for every item and deduct it inside the current
     * transaction using row locks.
     *
     * - stock_unit = 'kg' products: demand is aggregated per product and
     *   deducted from the product-level kilogram pool (qty × unit weight).
     * - stock_unit = 'unit' products: deducted per variant, or from
     *   products.stock for simple products.
     *
     * Every deduction is mirrored into the warehouse ledger via
     * InventoryService::recordOrderSale().
     *
     * @param  array<int, array<string, mixed>>  $items
     * @param  array{products: Collection<int, Product>, variants: Collection<int, ProductVariant>}  $context
     * @return bool true when stock was deducted (false for empty carts)
     *
     * @throws ValidationException when any item exceeds available stock
     */
    private function validateAndDeductStock(array $items, array $context, Order $order): bool
    {
        if (empty($items)) {
            return false;
        }

        $products = $context['products'];
        $variants = $context['variants'];

        $errors = [];
        $kgDemand = [];
        $variantDemand = [];
        $simpleDemand = [];
        $names = ['product' => [], 'variant' => []];

        foreach ($items as $index => $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $variantId = ! empty($item['product_variant_id']) ? (int) $item['product_variant_id'] : null;
            $requested = max(1, (int) ($item['quantity'] ?? 1));
            $productName = $item['product_name'] ?? "Product #{$productId}";

            $product = $products->get($productId);
            if (! $product) {
                $errors["items.{$index}.product_id"] = ["{$productName}: product does not exist."];

                continue;
            }

            $variant = null;
            if ($variantId !== null) {
                $variant = $variants->get($variantId);
                if (! $variant || (int) $variant->product_id !== $productId) {
                    $errors["items.{$index}.product_variant_id"] = ["{$productName}: selected variant does not exist."];

                    continue;
                }
            }

            $names['product'][$productId] ??= ['index' => $index, 'name' => $productName];

            if ($product->usesWeightPool()) {
                $weight = $variant !== null
                    ? $variant->resolveUnitWeight()
                    : (($product->weight !== null && (float) $product->weight > 0) ? (float) $product->weight : null);

                if ($weight === null || $weight <= 0) {
                    $label = $variant !== null ? "{$productName} ({$variant->name})" : $productName;
                    $errors["items.{$index}.quantity"] = [
                        "{$label}: kilogram weight per unit is not configured for this product.",
                    ];

                    continue;
                }

                $kgDemand[$productId] = round(($kgDemand[$productId] ?? 0.0) + $requested * $weight, 3);

                continue;
            }

            if ($variant !== null) {
                $variantDemand[$variant->id] = ($variantDemand[$variant->id] ?? 0) + $requested;
                $names['variant'][$variant->id] ??= ['index' => $index, 'name' => $productName];
            } else {
                $simpleDemand[$productId] = ($simpleDemand[$productId] ?? 0) + $requested;
            }
        }

        // ── Aggregate stock checks (skipped when overselling is allowed) ──
        $allowOversell = SiteSetting::getValue('allow_oversell') === '1';

        if (! $allowOversell) {
            foreach ($kgDemand as $productId => $neededKg) {
                $available = (float) $products->get($productId)->stock;
                if ($neededKg > $available) {
                    $ref = $names['product'][$productId];
                    $errors["items.{$ref['index']}.quantity"] ??= [
                        "{$ref['name']}: only {$available} kg in stock, but {$neededKg} kg requested.",
                    ];
                }
            }

            foreach ($variantDemand as $variantId => $qty) {
                $variant = $variants->get($variantId);
                if ($qty > (int) $variant->stock) {
                    $ref = $names['variant'][$variantId];
                    $errors["items.{$ref['index']}.quantity"] ??= [
                        "{$ref['name']} ({$variant->name}): only {$variant->stock} in stock, but {$qty} requested.",
                    ];
                }
            }

            foreach ($simpleDemand as $productId => $qty) {
                $product = $products->get($productId);
                if ($qty > (int) $product->stock) {
                    $ref = $names['product'][$productId];
                    $errors["items.{$ref['index']}.quantity"] ??= [
                        "{$ref['name']}: only {$product->stock} in stock, but {$qty} requested.",
                    ];
                }
            }
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        // ── Deduct + mirror into the warehouse ledger ──
        $deltas = [];

        foreach ($kgDemand as $productId => $neededKg) {
            $product = $products->get($productId);
            $product->decrement('stock', $neededKg);
            $deltas[] = [
                'product_id' => $productId,
                'product_variant_id' => null,
                'quantity' => $neededKg,
                'stock_after' => (float) $product->fresh()->stock,
                'product_name' => $names['product'][$productId]['name'],
            ];
        }

        foreach ($variantDemand as $variantId => $qty) {
            $variant = $variants->get($variantId);
            $variant->decrement('stock', $qty);
            $deltas[] = [
                'product_id' => (int) $variant->product_id,
                'product_variant_id' => $variantId,
                'quantity' => $qty,
                'stock_after' => (float) $variant->fresh()->stock,
                'product_name' => $names['variant'][$variantId]['name'],
            ];
        }

        foreach ($simpleDemand as $productId => $qty) {
            $product = $products->get($productId);
            $product->decrement('stock', $qty);
            $deltas[] = [
                'product_id' => $productId,
                'product_variant_id' => null,
                'quantity' => $qty,
                'stock_after' => (float) $product->fresh()->stock,
                'product_name' => $names['product'][$productId]['name'],
            ];
        }

        $this->inventoryService->recordOrderSale($deltas, $order);

        return true;
    }

    /**
     * Return an order's items to stock (reverse of validateAndDeductStock).
     * Invoked at most once per order, guarded by stock_restocked_at, and
     * only for orders whose stock was actually deducted. Each increment is
     * mirrored into the warehouse ledger via InventoryService::recordOrderRestock().
     */
    private function restockOrder(Order $order): void
    {
        $order->loadMissing('items');

        if ($order->items->isEmpty()) {
            return;
        }

        $products = Product::whereIn('id', $order->items->pluck('product_id')->filter()->unique())
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
        $variants = ProductVariant::whereIn('id', $order->items->pluck('product_variant_id')->filter()->unique())
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $deltas = [];

        foreach ($order->items as $item) {
            $product = $products->get((int) $item->product_id);
            if (! $product) {
                continue;
            }

            $qty = max(1, (int) $item->quantity);
            $variant = $item->product_variant_id
                ? $variants->get((int) $item->product_variant_id)
                : null;

            if ($product->usesWeightPool()) {
                $weight = $variant !== null
                    ? $variant->resolveUnitWeight()
                    : (($product->weight !== null && (float) $product->weight > 0) ? (float) $product->weight : null);

                if ($weight !== null && $weight > 0) {
                    $returned = round($qty * $weight, 3);
                    Product::where('id', $product->id)->increment('stock', $returned);
                    $deltas[] = [
                        'product_id' => $product->id,
                        'product_variant_id' => null,
                        'quantity' => $returned,
                        'stock_after' => (float) $product->fresh()->stock,
                        'product_name' => $item->product_name,
                    ];
                }

                continue;
            }

            if ($variant !== null) {
                ProductVariant::where('id', $variant->id)->increment('stock', $qty);
                $deltas[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $qty,
                    'stock_after' => (float) $variant->fresh()->stock,
                    'product_name' => $item->product_name,
                ];
            } elseif (! $item->product_variant_id) {
                Product::where('id', $product->id)->increment('stock', $qty);
                $deltas[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'quantity' => $qty,
                    'stock_after' => (float) $product->fresh()->stock,
                    'product_name' => $item->product_name,
                ];
            }
        }

        if ($deltas !== []) {
            $this->inventoryService->recordOrderRestock($deltas, $order);
        }
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

            // Return stock when an order moves into a stock-reversing status.
            // Guarded so legacy orders (created before stock tracking) and
            // repeated status changes never inflate stock.
            $reversesStock = in_array($newStatus, [Order::STATUS_CANCELLED, Order::STATUS_REFUNDED, Order::STATUS_RETURNED], true);
            if ($reversesStock && $order->stock_deducted_at !== null && $order->stock_restocked_at === null) {
                $this->restockOrder($order);
                $order->update(['stock_restocked_at' => now()]);
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

    /**
     * Resolve the shipping charge server-side from the district in the
     * shipping address. Any client-sent shipping_charge is ignored.
     *
     * @param  array<string, mixed>|null  $shippingAddress
     *
     * @throws ValidationException when no district is given or the
     *                             district is not deliverable
     */
    private function calculateShippingCharge(?array $shippingAddress, float $subtotal): float
    {
        if ($shippingAddress === null) {
            return 0.0;
        }

        $district = trim((string) ($shippingAddress['district'] ?? ''));

        if ($district === '') {
            throw ValidationException::withMessages([
                'shipping_address.district' => 'A delivery district is required to calculate shipping.',
            ]);
        }

        $result = $this->deliveryZoneService->calculateCharge($district, $subtotal);

        if (! ($result['available'] ?? false)) {
            throw ValidationException::withMessages([
                'shipping_address.district' => 'Delivery is not available for the selected district.',
            ]);
        }

        return (float) $result['charge'];
    }

    /**
     * Calculate tax from the site's tax_rate setting (never client-sent).
     * Mirrors the checkout preview: subtotal * tax_rate%.
     */
    private function calculateTax(float $subtotal): float
    {
        $taxRate = (float) SiteSetting::getValue('tax_rate', '5');

        return round($subtotal * ($taxRate / 100), 2);
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
