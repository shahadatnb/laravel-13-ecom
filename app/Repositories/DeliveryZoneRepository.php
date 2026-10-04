<?php

namespace App\Repositories;

use App\Models\DeliveryZone;
use App\Models\DeliveryZoneDistrict;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
     *
     * Existing districts keep their id and status so per-district
     * edits (rename, inactive) survive a zone form save. Districts
     * unchecked in the form are removed from this zone.
     */
    public function syncDistricts(DeliveryZone $zone, array $districtNames): void
    {
        $zone->districts()->whereNotIn('name', $districtNames)->delete();

        foreach ($districtNames as $name) {
            DeliveryZoneDistrict::updateOrCreate(
                ['name' => $name],
                ['delivery_zone_id' => $zone->id]
            );
        }
    }

    /**
     * Paginated district list for the admin, optionally filtered by name.
     *
     * @return LengthAwarePaginator
     */
    public function getDistricts(?string $search = null)
    {
        return DeliveryZoneDistrict::with('zone')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();
    }

    public function updateDistrict(DeliveryZoneDistrict $district, array $data): DeliveryZoneDistrict
    {
        $district->update($data);

        return $district->fresh('zone');
    }
}
