<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\User;
use App\Repositories\CouponRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CouponService implements CouponServiceInterface
{
    public function __construct(
        private readonly CouponRepositoryInterface $couponRepository,
    ) {}

    public function getCoupon(int $id): ?Coupon
    {
        return $this->couponRepository->find($id);
    }

    public function findByCode(string $code): ?Coupon
    {
        return $this->couponRepository->findByCode($code);
    }

    public function createCoupon(array $data): Coupon
    {
        return DB::transaction(function () use ($data) {
            $data['code'] = $data['code'] ?? $this->generateCouponCode();
            $data['status'] = $data['status'] ?? Coupon::STATUS_DRAFT;
            $data['total_used'] = 0;

            $coupon = $this->couponRepository->create($data);

            // Sync relations
            if (isset($data['product_ids'])) {
                $syncData = [];
                foreach ($data['product_ids'] as $productId) {
                    $syncData[$productId] = ['is_excluded' => false];
                }
                if (isset($data['excluded_product_ids'])) {
                    foreach ($data['excluded_product_ids'] as $productId) {
                        $syncData[$productId] = ['is_excluded' => true];
                    }
                }
                $coupon->products()->sync($syncData);
            }

            if (isset($data['category_ids'])) {
                $syncData = [];
                foreach ($data['category_ids'] as $categoryId) {
                    $syncData[$categoryId] = ['is_excluded' => false];
                }
                if (isset($data['excluded_category_ids'])) {
                    foreach ($data['excluded_category_ids'] as $categoryId) {
                        $syncData[$categoryId] = ['is_excluded' => true];
                    }
                }
                $coupon->categories()->sync($syncData);
            }

            if (isset($data['customer_ids'])) {
                $coupon->customers()->sync($data['customer_ids']);
            }

            return $coupon->fresh(['products', 'categories', 'customers']);
        });
    }

    public function updateCoupon(Coupon $coupon, array $data): Coupon
    {
        return DB::transaction(function () use ($coupon, $data) {
            $coupon = $this->couponRepository->update($coupon, $data);

            if (isset($data['product_ids'])) {
                $syncData = [];
                foreach ($data['product_ids'] as $productId) {
                    $syncData[$productId] = ['is_excluded' => false];
                }
                if (isset($data['excluded_product_ids'])) {
                    foreach ($data['excluded_product_ids'] as $productId) {
                        $syncData[$productId] = ['is_excluded' => true];
                    }
                }
                $coupon->products()->sync($syncData);
            }

            if (isset($data['category_ids'])) {
                $syncData = [];
                foreach ($data['category_ids'] as $categoryId) {
                    $syncData[$categoryId] = ['is_excluded' => false];
                }
                if (isset($data['excluded_category_ids'])) {
                    foreach ($data['excluded_category_ids'] as $categoryId) {
                        $syncData[$categoryId] = ['is_excluded' => true];
                    }
                }
                $coupon->categories()->sync($syncData);
            }

            if (isset($data['customer_ids'])) {
                $coupon->customers()->sync($data['customer_ids']);
            }

            return $coupon;
        });
    }

    public function deleteCoupon(Coupon $coupon): void
    {
        DB::transaction(function () use ($coupon) {
            $coupon->products()->detach();
            $coupon->categories()->detach();
            $coupon->customers()->detach();
            $this->couponRepository->delete($coupon);
        });
    }

    public function listCoupons(int $perPage = 15): LengthAwarePaginator
    {
        return $this->couponRepository->paginate($perPage);
    }

    public function searchCoupons(array $filters): LengthAwarePaginator
    {
        return $this->couponRepository->search($filters, $filters['per_page'] ?? 15);
    }

    public function getCouponStatistics(): array
    {
        $usageStats = $this->couponRepository->getUsageStatistics();

        return [
            'total' => $this->couponRepository->count(),
            'active' => $this->couponRepository->countActive(),
            'expired' => $this->couponRepository->countExpired(),
            'total_discount' => $usageStats['total_discount'],
            'total_orders_with_coupon' => $usageStats['total_orders'],
            'total_usages' => $usageStats['total_usages'],
        ];
    }

    public function validateCoupon(string $code, ?int $userId = null, ?array $context = []): array
    {
        $coupon = $this->couponRepository->findByCode($code);

        if (! $coupon) {
            return ['valid' => false, 'error' => 'Invalid coupon code.'];
        }

        // Check if coupon is active
        if ($coupon->status !== Coupon::STATUS_ACTIVE) {
            return ['valid' => false, 'error' => 'This coupon is not active.'];
        }

        // Check date range
        if ($coupon->valid_from && now()->lt($coupon->valid_from)) {
            return ['valid' => false, 'error' => 'This coupon is not yet valid.'];
        }

        if ($coupon->valid_until && now()->gt($coupon->valid_until)) {
            return ['valid' => false, 'error' => 'This coupon has expired.'];
        }

        // Check global usage limit
        if ($coupon->usage_limit !== null && $coupon->total_used >= $coupon->usage_limit) {
            return ['valid' => false, 'error' => 'This coupon has reached its usage limit.'];
        }

        // Check per-user limit
        if ($userId && $coupon->per_user_limit > 0) {
            $userUsageCount = CouponUsage::where('coupon_id', $coupon->id)
                ->where('user_id', $userId)
                ->count();

            if ($userUsageCount >= $coupon->per_user_limit) {
                return ['valid' => false, 'error' => 'You have already used this coupon the maximum number of times.'];
            }
        }

        // Check guest restriction
        if (! $userId && ! $coupon->is_guest_allowed) {
            return ['valid' => false, 'error' => 'Please log in to use this coupon.'];
        }

        // Check first order only
        if ($coupon->is_first_order_only && $userId) {
            $completedOrders = Order::where('user_id', $userId)
                ->whereIn('status', [Order::STATUS_COMPLETED, Order::STATUS_DELIVERED])
                ->count();

            if ($completedOrders > 0) {
                return ['valid' => false, 'error' => 'This coupon is for first-time orders only.'];
            }
        }

        // Check minimum order amount
        $orderAmount = $context['order_amount'] ?? 0;
        if ($coupon->min_order_amount && $orderAmount < $coupon->min_order_amount) {
            return [
                'valid' => false,
                'error' => 'Minimum order amount of '.number_format($coupon->min_order_amount, 2).' is required.',
            ];
        }

        // Check maximum order amount
        if ($coupon->max_order_amount && $orderAmount > $coupon->max_order_amount) {
            return [
                'valid' => false,
                'error' => 'This coupon is only valid for orders under '.number_format($coupon->max_order_amount, 2).'.',
            ];
        }

        // Check customer restriction
        if ($coupon->customer_restriction && $userId) {
            $user = User::find($userId);

            if ($coupon->customer_restriction === 'vip' && ! $user?->isVip()) {
                return ['valid' => false, 'error' => 'This coupon is for VIP customers only.'];
            }

        }

        // Check customer-specific coupon
        if ($coupon->scope === Coupon::SCOPE_CUSTOMERS && $userId) {
            $isCustomerAssigned = $coupon->customers()->where('user_id', $userId)->exists();
            if (! $isCustomerAssigned) {
                return ['valid' => false, 'error' => 'This coupon is not available for you.'];
            }
        }

        return ['valid' => true, 'coupon' => $coupon];
    }

    public function calculateDiscount(Coupon $coupon, float $orderAmount, ?array $context = []): float
    {
        $discount = 0.0;

        switch ($coupon->type) {
            case Coupon::TYPE_PERCENTAGE:
                $discount = $orderAmount * ($coupon->discount_value / 100);
                // Apply max discount cap
                if ($coupon->max_discount && $discount > $coupon->max_discount) {
                    $discount = $coupon->max_discount;
                }
                break;

            case Coupon::TYPE_FIXED:
                $discount = $coupon->discount_value;
                break;

            case Coupon::TYPE_FREE_SHIPPING:
                $shippingCharge = $context['shipping_charge'] ?? 0;
                $discount = $shippingCharge;
                break;

            case Coupon::TYPE_CASHBACK:
                $discount = $orderAmount * ($coupon->discount_value / 100);
                if ($coupon->max_discount && $discount > $coupon->max_discount) {
                    $discount = $coupon->max_discount;
                }
                break;

            case Coupon::TYPE_BUY_X_GET_Y:
                // Buy X Get Y - discount equals the price of the qualifying "get" items
                $settings = $coupon->settings;
                $buyQty = $settings['buy_quantity'] ?? 1;
                $getQty = $settings['get_quantity'] ?? 1;
                $getDiscountPct = $settings['get_discount_percent'] ?? 100;
                $items = $context['items'] ?? [];

                $qualifyingItems = collect($items)->sortByDesc('unit_price');
                $freeItems = $qualifyingItems->slice($buyQty, $getQty);

                foreach ($freeItems as $item) {
                    $discount += ($item['unit_price'] ?? 0) * ($getDiscountPct / 100) * ($item['quantity'] ?? 1);
                }
                break;

            case Coupon::TYPE_GIFT:
                // Gift coupon - full coupon value is the discount
                $discount = $coupon->discount_value;
                break;

            case Coupon::TYPE_REFERRAL:
                $discount = $orderAmount * ($coupon->discount_value / 100);
                if ($coupon->max_discount && $discount > $coupon->max_discount) {
                    $discount = $coupon->max_discount;
                }
                break;
        }

        // Ensure discount doesn't exceed order amount
        return max(0, min($discount, $orderAmount));
    }

    public function applyCoupon(Coupon $coupon, Order $order): array
    {
        return DB::transaction(function () use ($coupon, $order) {
            $orderAmount = (float) $order->subtotal;
            $context = [
                'order_amount' => $orderAmount,
                'shipping_charge' => (float) $order->shipping_charge,
                'items' => $order->items->toArray(),
            ];

            $discountAmount = $this->calculateDiscount($coupon, $orderAmount, $context);

            // Record usage
            CouponUsage::create([
                'coupon_id' => $coupon->id,
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'discount_amount' => $discountAmount,
                'order_amount' => $orderAmount,
                'notes' => 'Applied via checkout',
            ]);

            // Increment coupon usage counter
            $this->couponRepository->incrementUsage($coupon);

            // Update order with coupon info
            $order->update([
                'coupon_code' => $coupon->code,
                'coupon_discount' => $discountAmount,
                'discount' => ($order->discount ?? 0) + $discountAmount,
                'grand_total' => max(0, ($order->grand_total - $discountAmount)),
                'due_amount' => max(0, ($order->due_amount - $discountAmount)),
            ]);

            return [
                'coupon_code' => $coupon->code,
                'discount_amount' => $discountAmount,
                'new_grand_total' => $order->fresh()->grand_total,
            ];
        });
    }

    public function reverseCouponUsage(Order $order): void
    {
        if (! $order->coupon_code) {
            return;
        }

        DB::transaction(function () use ($order) {
            $usages = CouponUsage::where('order_id', $order->id)->get();

            foreach ($usages as $usage) {
                $coupon = $usage->coupon;

                if ($coupon) {
                    $coupon->decrement('total_used');
                    if ($coupon->total_used < 0) {
                        $coupon->update(['total_used' => 0]);
                    }
                }

                $usage->update(['notes' => 'Reversed - order cancelled/refunded']);
            }

            // Restore order discount
            $couponDiscount = $order->coupon_discount ?? 0;
            $order->update([
                'coupon_code' => null,
                'coupon_discount' => 0,
                'discount' => max(0, ($order->discount ?? 0) - $couponDiscount),
                'grand_total' => $order->grand_total + $couponDiscount,
                'due_amount' => $order->due_amount + $couponDiscount,
            ]);
        });
    }

    public function syncRelations(Coupon $coupon, array $data): Coupon
    {
        if (isset($data['product_ids'])) {
            $syncData = [];
            foreach ($data['product_ids'] as $productId) {
                $syncData[$productId] = ['is_excluded' => false];
            }
            if (isset($data['excluded_product_ids'])) {
                foreach ($data['excluded_product_ids'] as $productId) {
                    $syncData[$productId] = ['is_excluded' => true];
                }
            }
            $coupon->products()->sync($syncData);
        }

        if (isset($data['category_ids'])) {
            $syncData = [];
            foreach ($data['category_ids'] as $categoryId) {
                $syncData[$categoryId] = ['is_excluded' => false];
            }
            if (isset($data['excluded_category_ids'])) {
                foreach ($data['excluded_category_ids'] as $categoryId) {
                    $syncData[$categoryId] = ['is_excluded' => true];
                }
            }
            $coupon->categories()->sync($syncData);
        }

        if (isset($data['customer_ids'])) {
            $coupon->customers()->sync($data['customer_ids']);
        }

        return $coupon->fresh(['products', 'categories', 'customers']);
    }

    private function generateCouponCode(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';

        do {
            $code = '';
            for ($i = 0; $i < 8; $i++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } while (Coupon::where('code', $code)->exists());

        return $code;
    }
}
