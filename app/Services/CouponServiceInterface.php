<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

interface CouponServiceInterface
{
    public function getCoupon(int $id): ?Coupon;

    public function findByCode(string $code): ?Coupon;

    public function createCoupon(array $data): Coupon;

    public function updateCoupon(Coupon $coupon, array $data): Coupon;

    public function deleteCoupon(Coupon $coupon): void;

    public function listCoupons(int $perPage = 15): LengthAwarePaginator;

    public function searchCoupons(array $filters): LengthAwarePaginator;

    public function getCouponStatistics(): array;

    /**
     * Validate a coupon code for the given user/order context.
     * Returns an array with 'valid' bool and optional 'error' message.
     */
    public function validateCoupon(string $code, ?int $userId = null, ?array $context = []): array;

    /**
     * Calculate the discount for a given coupon and order context.
     */
    public function calculateDiscount(Coupon $coupon, float $orderAmount, ?array $context = []): float;

    /**
     * Apply a coupon to an order.
     */
    public function applyCoupon(Coupon $coupon, Order $order): array;

    /**
     * Reverse coupon usage when order is refunded/cancelled.
     */
    public function reverseCouponUsage(Order $order): void;

    /**
     * Sync coupon relations (products, categories, customers).
     */
    public function syncRelations(Coupon $coupon, array $data): Coupon;
}
