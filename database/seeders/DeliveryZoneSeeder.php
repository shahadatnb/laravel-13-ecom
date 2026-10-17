<?php

namespace Database\Seeders;

use App\Helpers\BdDistricts;
use App\Models\DeliveryZone;
use App\Models\District;
use Illuminate\Database\Seeder;

class DeliveryZoneSeeder extends Seeder
{
    public function run(): void
    {
        $insideDhaka = DeliveryZone::updateOrCreate(
            ['type' => 'inside_dhaka'],
            [
                'name' => 'Inside Dhaka',
                'type' => 'inside_dhaka',
                'charge' => 10.00,
                'minimum_order_amount' => 100.00,
                'status' => 'active',
            ]
        );

        $outsideDhaka = DeliveryZone::updateOrCreate(
            ['type' => 'outside_dhaka'],
            [
                'name' => 'Outside Dhaka',
                'type' => 'outside_dhaka',
                'charge' => 25.00,
                'minimum_order_amount' => 200.00,
                'status' => 'active',
            ]
        );

        $insideDistricts = BdDistricts::greaterDhaka();

        foreach (BdDistricts::all() as $name => $division) {
            District::where('name', $name)->update([
                'delivery_zone_id' => in_array($name, $insideDistricts, true)
                    ? $insideDhaka->id
                    : $outsideDhaka->id,
            ]);
        }
    }
}
