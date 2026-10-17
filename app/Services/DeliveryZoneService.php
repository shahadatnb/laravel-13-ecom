<?php

namespace App\Services;

use App\Models\DeliveryZone;
use App\Models\District;
use App\Models\SiteSetting;
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

    public function create(array $data): DeliveryZone
    {
        $districtIds = $data['districts'] ?? [];
        unset($data['districts']);

        $zone = $this->deliveryZoneRepository->create($data);

        if (! empty($districtIds)) {
            $this->deliveryZoneRepository->syncDistricts($zone, $districtIds);
        }

        return $zone->load('districts');
    }

    public function update(DeliveryZone $deliveryZone, array $data): DeliveryZone
    {
        $districtIds = $data['districts'] ?? null;
        unset($data['districts']);

        $zone = $this->deliveryZoneRepository->update($deliveryZone, $data);

        if ($districtIds !== null) {
            $this->deliveryZoneRepository->syncDistricts($zone, $districtIds);
        }

        return $zone->load('districts');
    }

    public function delete(DeliveryZone $deliveryZone): void
    {
        $this->deliveryZoneRepository->delete($deliveryZone);
    }

    /**
     * Paginated district list for the admin panel.
     */
    public function listDistricts(?string $search = null)
    {
        return $this->deliveryZoneRepository->getDistricts($search);
    }

    /**
     * Districts grouped by division for the zone form's picker.
     *
     * @return Collection<string, Collection<int, District>>
     */
    public function districtChoices(): Collection
    {
        return District::orderBy('name')
            ->get()
            ->groupBy(fn (District $district) => $district->division ?: 'Other');
    }

    /**
     * Update a district (rename, reassign zone, override charge, status).
     */
    public function updateDistrict(District $district, array $data): District
    {
        return $this->deliveryZoneRepository->updateDistrict($district, $data);
    }

    /**
     * Flip a district between active and inactive.
     */
    public function toggleDistrictStatus(District $district): District
    {
        $newStatus = $district->status === District::STATUS_ACTIVE
            ? District::STATUS_INACTIVE
            : District::STATUS_ACTIVE;

        return $this->deliveryZoneRepository->updateDistrict($district, ['status' => $newStatus]);
    }

    public function getActive(): Collection
    {
        return $this->deliveryZoneRepository->getActive();
    }

    /**
     * Resolve the effective delivery charge for a district.
     *
     * Precedence: district override → zone rate → site default (shipping_rate).
     * Returns null when delivery is not available.
     *
     * @return array{charge: float, source: string, zone: DeliveryZone|null}|null
     */
    public function resolveCharge(District $district): ?array
    {
        if ($district->status !== District::STATUS_ACTIVE) {
            return null;
        }

        $zone = $district->zone;

        if ($zone && $zone->status !== 'active') {
            return null;
        }

        if ($district->charge !== null) {
            return ['charge' => (float) $district->charge, 'source' => 'district', 'zone' => $zone];
        }

        if ($zone) {
            return ['charge' => (float) $zone->charge, 'source' => 'zone', 'zone' => $zone];
        }

        $default = SiteSetting::getValue('shipping_rate');

        if ($default === null || $default === '') {
            return null;
        }

        return ['charge' => (float) $default, 'source' => 'default', 'zone' => null];
    }

    /**
     * Calculate delivery charge for a given district and order amount.
     *
     * @param  string  $district  district name (as selected at checkout)
     * @param  float  $orderAmount  order subtotal used for the free-delivery threshold
     */
    public function calculateCharge(string $district, float $orderAmount = 0): array
    {
        $record = $this->deliveryZoneRepository->findDistrictByName($district);
        $unavailable = [
            'available' => false,
            'charge' => 0,
            'message' => 'Delivery not available for this district.',
        ];

        if (! $record) {
            return $unavailable;
        }

        $resolved = $this->resolveCharge($record);

        if ($resolved === null) {
            return $unavailable;
        }

        $zone = $resolved['zone'];
        $charge = $resolved['charge'];

        // Free delivery when the order meets the zone's minimum threshold
        $freeDelivery = $zone !== null
            && $zone->minimum_order_amount !== null
            && $orderAmount >= (float) $zone->minimum_order_amount;

        return [
            'available' => true,
            'zone_id' => $zone?->id,
            'zone_name' => $zone?->name,
            'zone_type' => $zone?->type,
            'district' => $record->name,
            'source' => $resolved['source'],
            'charge' => $freeDelivery ? 0 : $charge,
            'original_charge' => $charge,
            'minimum_order_amount' => $zone?->minimum_order_amount !== null
                ? (float) $zone->minimum_order_amount
                : null,
            'free_delivery' => $freeDelivery,
            'message' => $freeDelivery
                ? 'Free delivery available!'
                : 'Delivery charge: '.number_format($charge, 2),
        ];
    }
}
