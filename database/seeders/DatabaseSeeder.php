<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Shahadat Hossain',
            'email' => 'shahadat@asiancoder.com',
            'password' => bcrypt('123456'),
        ]);

        $this->call([
            SiteSettingSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            ProductAttributeSeeder::class,
            ProductSeeder::class,
            DeliveryZoneSeeder::class,
        ]);
    }
}
