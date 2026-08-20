<?php

namespace Database\Seeders;

use App\Models\DeliveryZone;
use App\Models\DeliveryZoneDistrict;
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

        $insideDistricts = [
            'Dhaka', 'Gazipur', 'Narayanganj', 'Munshiganj',
            'Manikganj', 'Narsingdi', 'Madaripur', 'Shariatpur',
            'Tangail', 'Kishoreganj', 'Faridpur', 'Gopalganj', 'Rajbari',
        ];

        $insideDhaka->districts()->delete();
        foreach ($insideDistricts as $name) {
            DeliveryZoneDistrict::create([
                'delivery_zone_id' => $insideDhaka->id,
                'name' => $name,
                'status' => 'active',
            ]);
        }

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

        $outsideDistricts = [
            'Jamalpur', 'Mymensingh', 'Netrokona', 'Sherpur',
            'Bandarban', 'Brahmanbaria', 'Chandpur', 'Chittagong', 'Comilla',
            'Cox\'s Bazar', 'Feni', 'Khagrachhari', 'Lakshmipur', 'Noakhali', 'Rangamati',
            'Bagerhat', 'Chuadanga', 'Jessore', 'Jhenaidah', 'Khulna', 'Kushtia',
            'Magura', 'Meherpur', 'Narail', 'Satkhira',
            'Bogra', 'Joypurhat', 'Naogaon', 'Natore', 'Nawabganj', 'Pabna',
            'Rajshahi', 'Sirajganj',
            'Dinajpur', 'Gaibandha', 'Kurigram', 'Lalmonirhat', 'Nilphamari',
            'Panchagarh', 'Rangpur', 'Thakurgaon',
            'Habiganj', 'Maulvibazar', 'Sunamganj', 'Sylhet',
            'Barguna', 'Barisal', 'Bhola', 'Jhalokati', 'Patuakhali', 'Pirojpur',
        ];

        $outsideDhaka->districts()->delete();
        foreach ($outsideDistricts as $name) {
            DeliveryZoneDistrict::create([
                'delivery_zone_id' => $outsideDhaka->id,
                'name' => $name,
                'status' => 'active',
            ]);
        }
    }
}
