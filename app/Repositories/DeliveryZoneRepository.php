<?php

namespace App\Repositories;

use App\Models\DeliveryZone;
use App\Models\DeliveryZoneDistrict;

class DeliveryZoneRepository
{
    public function getAll()
    {
        return DeliveryZone::with('districts')->orderBy('name')->get();
    }

    public function find(int $id): ?DeliveryZone
    {
        return DeliveryZone::with('districts')->find($id);
    }

    public function create(array $data): DeliveryZone
    {
        return DeliveryZone::create($data);
    }

    public function update(DeliveryZone $deliveryZone, array $data): DeliveryZone
    {
        $deliveryZone->update($data);

        return $deliveryZone->fresh();
    }

    public function delete(DeliveryZone $deliveryZone): void
    {
        $deliveryZone->delete();
    }

    public function getActive()
    {
        return DeliveryZone::with('districts')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    public function findByType(string $type): ?DeliveryZone
    {
        return DeliveryZone::with('districts')->where('type', $type)->first();
    }

    /**
     * Find the delivery zone for a given district name.
     */
    public function findByDistrict(string $district): ?DeliveryZone
    {
        $districtRecord = DeliveryZoneDistrict::where('name', $district)
            ->where('status', 'active')
            ->first();

        if (! $districtRecord) {
            return null;
        }

        return $this->find($districtRecord->delivery_zone_id);
    }

    /**
     * Sync districts for a delivery zone.
     */
    public function syncDistricts(DeliveryZone $zone, array $districtNames): void
    {
        $zone->districts()->delete();

        $districts = array_map(function ($name) {
            return ['name' => $name, 'status' => 'active'];
        }, $districtNames);

        $zone->districts()->createMany($districts);
    }
}
