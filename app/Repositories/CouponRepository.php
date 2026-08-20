<?php

namespace App\Repositories;

use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CouponRepository implements CouponRepositoryInterface
{
    public function find(int $id): ?Coupon
    {
        return Coupon::with(['products', 'categories', 'customers', 'createdBy'])->find($id);
    }

    public function findByCode(string $code): ?Coupon
    {
        return Coupon::with(['products', 'categories', 'customers'])
            ->where('code', $code)
            ->first();
    }

    public function create(array $data): Coupon
    {
        return Coupon::create($data);
    }

    public function update(Coupon $coupon, array $data): Coupon
    {
        $coupon->update($data);

        return $coupon->fresh(['products', 'categories', 'customers']);
    }

    public function delete(Coupon $coupon): void
    {
        $coupon->delete();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Coupon::with(['createdBy'])
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function search(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Coupon::with(['createdBy']);

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['discount_type'])) {
            $query->where('discount_type', $filters['discount_type']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['valid_from'])) {
            $query->whereDate('valid_from', '>=', $filters['valid_from']);
        }

        if (isset($filters['valid_until'])) {
            $query->whereDate('valid_until', '<=', $filters['valid_until']);
        }

        if (isset($filters['scope'])) {
            $query->where('scope', $filters['scope']);
        }

        if (isset($filters['is_auto_apply'])) {
            $query->where('is_auto_apply', true);
        }

        $sortField = $filters['sort'] ?? 'id';
        $sortDir = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDir);

        return $query->paginate($perPage);
    }

    public function getValidCoupons(): Collection
    {
        return Coupon::valid()->orderByDesc('priority')->get();
    }

    public function getAutoApplyCoupons(): Collection
    {
        return Coupon::valid()->autoApply()
            ->orderByDesc('priority')
            ->get();
    }

    public function count(): int
    {
        return Coupon::count();
    }

    public function countByStatus(string $status): int
    {
        return Coupon::where('status', $status)->count();
    }

    public function countActive(): int
    {
        return Coupon::where('status', Coupon::STATUS_ACTIVE)->count();
    }

    public function countExpired(): int
    {
        return Coupon::where('status', Coupon::STATUS_ACTIVE)
            ->where('valid_until', '<', now())
            ->count();
    }

    public function incrementUsage(Coupon $coupon): void
    {
        $coupon->increment('total_used');
        $coupon->update(['last_used_at' => now()]);
    }

    public function getTopCoupons(int $limit = 10): Collection
    {
        return Coupon::withCount('usages')
            ->orderByDesc('usages_count')
            ->limit($limit)
            ->get();
    }

    public function getUsageStatistics(): array
    {
        $totalDiscount = CouponUsage::sum('discount_amount');
        $totalOrders = CouponUsage::distinct('order_id')->count('order_id');
        $totalUsages = CouponUsage::count();

        return [
            'total_discount' => $totalDiscount,
            'total_orders' => $totalOrders,
            'total_usages' => $totalUsages,
        ];
    }

    public function getDiscountSummary(string $startDate, string $endDate): array
    {
        $summary = CouponUsage::select(
            'coupon_id',
            DB::raw('COUNT(*) as usage_count'),
            DB::raw('SUM(discount_amount) as total_discount')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('coupon_id')
            ->with('coupon')
            ->get();

        return $summary->toArray();
    }
}
