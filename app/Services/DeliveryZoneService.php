<?php

namespace App\Services;

use App\Models\DeliveryZone;
use App\Repositories\DeliveryZoneRepository;
use Illuminate\Database\Eloquent\Collection;

class DeliveryZoneService
{
    public function __construct(private DeliveryZoneRepository $deliveryZoneRepository) {}

    public function list(): Collection
    {
        return $this->deliveryZoneRepository->getAll();
    }

    public function find(int $id): ?DeliveryZone
    {
        return $this->deliveryZoneRepository->find($id);
    }

    /**
     * Auto-derive the zone type from its districts.
     * If any district is in Greater Dhaka, it's inside_dhaka, otherwise outside_dhaka.
     */
    private function deriveType(array $data): string
    {
        if (! empty($data['type'])) {
            return $data['type'];
        }

        $insideDhakaDistricts = [
            'Dhaka', 'Gazipur', 'Narayanganj', 'Munshiganj',
            'Manikganj', 'Narsingdi', 'Madaripur', 'Shariatpur',
            'Tangail', 'Kishoreganj', 'Faridpur', 'Gopalganj', 'Rajbari',
        ];

        $districts = $data['districts'] ?? [];
        foreach ($districts as $district) {
            if (in_array($district, $insideDhakaDistricts)) {
                return 'inside_dhaka';
            }
        }

        return 'outside_dhaka';
    }

    public function create(array $data): DeliveryZone
    {
        $data['type'] = $this->deriveType($data);
        $zone = $this->deliveryZoneRepository->create($data);

        if (! empty($data['districts'])) {
            $this->deliveryZoneRepository->syncDistricts($zone, $data['districts']);
        }

        return $zone->load('districts');
    }

    public function update(DeliveryZone $deliveryZone, array $data): DeliveryZone
    {
        $data['type'] = $this->deriveType($data);
        $zone = $this->deliveryZoneRepository->update($deliveryZone, $data);

        if (array_key_exists('districts', $data)) {
            $this->deliveryZoneRepository->syncDistricts($zone, $data['districts'] ?? []);
        }

        return $zone->load('districts');
    }

    public function delete(DeliveryZone $deliveryZone): void
    {
        $this->deliveryZoneRepository->delete($deliveryZone);
    }

    public function getActive(): Collection
    {
        return $this->deliveryZoneRepository->getActive();
    }

    /**
     * Calculate delivery charge for a given district and order amount.
     */
    public function calculateCharge(string $district, float $orderAmount = 0): array
    {
        $zone = $this->deliveryZoneRepository->findByDistrict($district);

        if (! $zone || $zone->status !== 'active') {
            return [
                'available' => false,
                'charge' => 0,
                'message' => 'Delivery not available for this district.',
            ];
        }

        // Free delivery if order amount meets minimum threshold
        $freeDelivery = $zone->minimum_order_amount !== null
            && $orderAmount >= (float) $zone->minimum_order_amount;

        return [
            'available' => true,
            'zone_id' => $zone->id,
            'zone_name' => $zone->name,
            'zone_type' => $zone->type,
            'charge' => $freeDelivery ? 0 : (float) $zone->charge,
            'original_charge' => (float) $zone->charge,
            'minimum_order_amount' => $zone->minimum_order_amount ? (float) $zone->minimum_order_amount : null,
            'free_delivery' => $freeDelivery,
            'message' => $freeDelivery
                ? 'Free delivery available!'
                : 'Delivery charge: '.number_format((float) $zone->charge, 2),
        ];
    }
}
