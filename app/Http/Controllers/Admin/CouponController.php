<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Models\Coupon;
use App\Services\CouponServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function __construct(
        private readonly CouponServiceInterface $couponService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'status', 'type', 'date_from', 'date_to']);

        $coupons = ! empty($filters)
            ? $this->couponService->searchCoupons($filters)
            : $this->couponService->listCoupons(15);

        $stats = $this->couponService->getCouponStatistics();

        $statuses = [
            Coupon::STATUS_DRAFT,
            Coupon::STATUS_ACTIVE,
            Coupon::STATUS_INACTIVE,
            Coupon::STATUS_EXPIRED,
            Coupon::STATUS_CANCELLED,
        ];

        $types = [
            Coupon::TYPE_PERCENTAGE,
            Coupon::TYPE_FIXED,
            Coupon::TYPE_FREE_SHIPPING,
            Coupon::TYPE_BUY_X_GET_Y,
            Coupon::TYPE_CASHBACK,
            Coupon::TYPE_GIFT,
            Coupon::TYPE_REFERRAL,
        ];

        return view('admin.coupons.index', compact('coupons', 'stats', 'statuses', 'types'));
    }

    public function create(): View
    {
        $statuses = [
            Coupon::STATUS_DRAFT,
            Coupon::STATUS_ACTIVE,
            Coupon::STATUS_INACTIVE,
        ];

        $types = [
            Coupon::TYPE_PERCENTAGE,
            Coupon::TYPE_FIXED,
            Coupon::TYPE_FREE_SHIPPING,
            Coupon::TYPE_BUY_X_GET_Y,
            Coupon::TYPE_CASHBACK,
        ];

        return view('admin.coupons.create', compact('statuses', 'types'));
    }

    public function store(CouponRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $this->couponService->createCoupon($data);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    public function show(Coupon $coupon): View
    {
        $coupon->load(['products', 'categories', 'customers', 'usages.user', 'usages.order', 'createdBy']);

        return view('admin.coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon): View
    {
        $coupon->load(['products', 'categories', 'customers']);

        $statuses = [
            Coupon::STATUS_DRAFT,
            Coupon::STATUS_ACTIVE,
            Coupon::STATUS_INACTIVE,
            Coupon::STATUS_EXPIRED,
            Coupon::STATUS_CANCELLED,
        ];

        $types = [
            Coupon::TYPE_PERCENTAGE,
            Coupon::TYPE_FIXED,
            Coupon::TYPE_FREE_SHIPPING,
            Coupon::TYPE_BUY_X_GET_Y,
            Coupon::TYPE_CASHBACK,
        ];

        return view('admin.coupons.edit', compact('coupon', 'statuses', 'types'));
    }

    public function update(CouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $this->couponService->updateCoupon($coupon, $data);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $this->couponService->deleteCoupon($coupon);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }

    public function toggleStatus(Coupon $coupon): RedirectResponse
    {
        $newStatus = $coupon->status === Coupon::STATUS_ACTIVE
            ? Coupon::STATUS_INACTIVE
            : Coupon::STATUS_ACTIVE;

        $this->couponService->updateCoupon($coupon, [
            'status' => $newStatus,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon status updated successfully.');
    }
}
