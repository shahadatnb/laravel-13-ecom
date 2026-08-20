<?php

use App\Models\Customer;

test('authenticated customer can fetch profile via API', function () {
    $customer = Customer::factory()->create();

    $this->actingAs($customer, 'customer')
        ->getJson('/api/customer')
        ->assertOk()
        ->assertJson([
            'success' => true,
        ])
        ->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'name',
                'email',
                'phone',
                'avatar',
                'email_verified_at',
                'created_at',
                'updated_at',
            ],
        ]);
});

test('unauthenticated user cannot access profile API', function () {
    $this->getJson('/api/customer')->assertUnauthorized();
});

test('profile email matches the authenticated customer', function () {
    $customer = Customer::factory()->create();

    $response = $this->actingAs($customer, 'customer')->getJson('/api/customer');

    $response->assertOk();
    expect($response->json('data.email'))->toBe($customer->email);
});
