<?php

namespace App\Repositories;

use App\Models\DeliveryZone;
use App\Models\District;
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
     * Find a district record (with its zone) by name.
     */
    public function findDistrictByName(string $district): ?District
    {
        return District::with('zone')
            ->where('name', trim($district))
            ->first();
    }

    /**
     * Sync zone membership by district ids.
     *
     * Districts unchecked in the zone form are unassigned (they remain
     * global rows so renames, statuses and charge overrides survive).
     * Checked districts are moved to this zone, keeping all their edits.
     *
     * @param  list<int>  $districtIds
     */
    public function syncDistricts(DeliveryZone $zone, array $districtIds): void
    {
        District::where('delivery_zone_id', $zone->id)
            ->whereNotIn('id', $districtIds)
            ->update(['delivery_zone_id' => null]);

        District::whereIn('id', $districtIds)->update(['delivery_zone_id' => $zone->id]);
    }

    /**
     * Paginated district list for the admin, optionally filtered by name.
     *
     * @return LengthAwarePaginator
     */
    public function getDistricts(?string $search = null)
    {
        return District::with('zone')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();
    }

    public function updateDistrict(District $district, array $data): District
    {
        $district->update($data);

        return $district->fresh('zone');
    }
}
