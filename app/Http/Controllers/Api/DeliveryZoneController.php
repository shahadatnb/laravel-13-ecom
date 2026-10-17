<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\District;
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
                    'formatted_charge' => currency_symbol().number_format((float) $zone->charge, 2),
                ];
            }),
        ]);
    }

    /**
     * Get a flat list of all deliverable districts with their zone info
     * and the effective charge (district override → zone → site default).
     * Districts without any charge source are hidden.
     */
    public function districts(): JsonResponse
    {
        $districts = District::with('zone')
            ->active()
            ->where(function ($query) {
                $query->whereNull('delivery_zone_id')
                    ->orWhereHas('zone', function ($zoneQuery) {
                        $zoneQuery->where('status', 'active');
                    });
            })
            ->orderBy('name')
            ->get()
            ->map(function (District $district) {
                $resolved = $this->deliveryZoneService->resolveCharge($district);

                if ($resolved === null) {
                    return null;
                }

                return [
                    'name' => $district->name,
                    'division' => $district->division,
                    'zone_id' => $district->delivery_zone_id,
                    'zone_name' => $district->zone->name ?? null,
                    'charge' => $resolved['charge'],
                    'source' => $resolved['source'],
                ];
            })
            ->filter()
            ->values();

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
