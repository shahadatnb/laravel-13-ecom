<?php

namespace Database\Seeders;

use App\Models\CustomerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereDoesntHave('customerProfile')->get();

        foreach ($users as $user) {
            CustomerProfile::factory()->create([
                'user_id' => $user->id,
            ]);
        }

        // Create additional customer profiles for testing
        CustomerProfile::factory(10)->create();
    }
}
