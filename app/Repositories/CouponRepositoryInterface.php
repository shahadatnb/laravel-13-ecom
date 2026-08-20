<?php

namespace App\Repositories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CouponRepositoryInterface
{
    public function find(int $id): ?Coupon;

    public function findByCode(string $code): ?Coupon;

    public function create(array $data): Coupon;

    public function update(Coupon $coupon, array $data): Coupon;

    public function delete(Coupon $coupon): void;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function search(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function getValidCoupons(): Collection;

    public function getAutoApplyCoupons(): Collection;

    public function count(): int;

    public function countByStatus(string $status): int;

    public function countActive(): int;

    public function countExpired(): int;

    public function incrementUsage(Coupon $coupon): void;

    public function getTopCoupons(int $limit = 10): Collection;

    public function getUsageStatistics(): array;

    public function getDiscountSummary(string $startDate, string $endDate): array;
}
