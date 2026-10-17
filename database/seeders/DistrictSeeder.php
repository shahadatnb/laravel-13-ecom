<?php

namespace Database\Seeders;

use App\Helpers\BdDistricts;
use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    /**
     * Seed the canonical 64 districts with their division.
     * Existing rows keep their zone, status and charge override.
     */
    public function run(): void
    {
        foreach (BdDistricts::all() as $name => $division) {
            $district = District::firstOrNew(['name' => $name]);

            if (! $district->division) {
                $district->division = $division;
            }

            if (! $district->status) {
                $district->status = District::STATUS_ACTIVE;
            }

            $district->save();
        }
    }
}
