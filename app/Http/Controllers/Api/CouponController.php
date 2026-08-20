<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use App\Services\CouponServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct(
        private readonly CouponServiceInterface $couponService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $coupons = $this->couponService->listCoupons(
            $request->get('per_page', 15)
        );

        return response()->json([
            'data' => CouponResource::collection($coupons),
            'meta' => [
                'current_page' => $coupons->currentPage(),
                'last_page' => $coupons->lastPage(),
                'total' => $coupons->total(),
            ],
        ]);
    }

    public function show(Coupon $coupon): JsonResponse
    {
        $coupon->load(['products', 'categories', 'customers', 'createdBy']);

        return response()->json([
            'data' => new CouponResource($coupon),
        ]);
    }

    public function store(CouponRequest $request): JsonResponse
    {
        $coupon = $this->couponService->createCoupon($request->validated());

        return response()->json([
            'message' => 'Coupon created successfully.',
            'data' => new CouponResource($coupon),
        ], 201);
    }

    public function update(CouponRequest $request, Coupon $coupon): JsonResponse
    {
        $coupon = $this->couponService->updateCoupon($coupon, $request->validated());

        return response()->json([
            'message' => 'Coupon updated successfully.',
            'data' => new CouponResource($coupon),
        ]);
    }

    public function destroy(Coupon $coupon): JsonResponse
    {
        $this->couponService->deleteCoupon($coupon);

        return response()->json([
            'message' => 'Coupon deleted successfully.',
        ]);
    }

    /**
     * Validate a coupon code without applying it.
     * NOTE: Named checkCoupon() to avoid overriding Controller::validate().
     */
    public function checkCoupon(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'order_amount' => 'nullable|numeric|min:0',
        ]);

        $result = $this->couponService->validateCoupon(
            $validated['code'],
            $request->user()?->id,
            ['order_amount' => $validated['order_amount'] ?? 0]
        );

        if (! $result['valid']) {
            return response()->json([
                'valid' => false,
                'error' => $result['error'],
            ], 422);
        }

        /** @var Coupon $coupon */
        $coupon = $result['coupon'];
        $discount = $this->couponService->calculateDiscount(
            $coupon,
            (float) ($validated['order_amount'] ?? 0),
            ['order_amount' => $validated['order_amount'] ?? 0]
        );

        return response()->json([
            'valid' => true,
            'data' => [
                'coupon' => new CouponResource($coupon),
                'discount' => $discount,
                'discount_formatted' => number_format($discount, 2),
            ],
        ]);
    }

    /**
     * Get valid/available coupons for the current user.
     */
    public function available(Request $request): JsonResponse
    {
        $orderAmount = (float) $request->get('order_amount', 0);
        $coupons = $this->couponService->listCoupons(50);

        $available = collect($coupons->items())->filter(function ($coupon) use ($request, $orderAmount) {
            $result = $this->couponService->validateCoupon(
                $coupon->code,
                $request->user()?->id,
                ['order_amount' => $orderAmount]
            );

            return $result['valid'];
        });

        return response()->json([
            'data' => CouponResource::collection($available),
        ]);
    }
}
