<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZoneDistrict;
use App\Services\DeliveryZoneService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryZoneController extends Controller
{
    public function __construct(private DeliveryZoneService $deliveryZoneService) {}

    /**
     * Get all active delivery zones with their districts.
     */
    public function index(): JsonResponse
    {
        $zones = $this->deliveryZoneService->getActive();

        return response()->json([
            'success' => true,
            'data' => $zones->map(function ($zone) {
                return [
                    'id' => $zone->id,
                    'name' => $zone->name,
                    'type' => $zone->type,
                    'districts' => $zone->districts->where('status', 'active')->pluck('name'),
                    'charge' => (float) $zone->charge,
                    'minimum_order_amount' => $zone->minimum_order_amount ? (float) $zone->minimum_order_amount : null,
                    'formatted_charge' => '$'.number_format((float) $zone->charge, 2),
                ];
            }),
        ]);
    }

    /**
     * Get a flat list of all available districts with their zone info.
     */
    public function districts(): JsonResponse
    {
        $districts = DeliveryZoneDistrict::with('zone')
            ->where('status', 'active')
            ->get()
            ->map(function ($district) {
                return [
                    'name' => $district->name,
                    'zone_id' => $district->delivery_zone_id,
                    'zone_name' => $district->zone->name ?? null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $districts,
        ]);
    }

    /**
     * Calculate delivery charge for a given district.
     *
     * Query params:
     * - district (required): the customer's district name
     * - order_amount (optional): order subtotal for free delivery check
     */
    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'district' => ['required', 'string', 'max:100'],
            'order_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $result = $this->deliveryZoneService->calculateCharge(
            $validated['district'],
            (float) ($validated['order_amount'] ?? 0)
        );

        return response()->json([
            'success' => $result['available'],
            'data' => $result,
        ]);
    }
}
