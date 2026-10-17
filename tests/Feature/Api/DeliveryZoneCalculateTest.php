<?php

use App\Models\DeliveryZone;
use App\Models\District;
use App\Models\SiteSetting;

beforeEach(function () {
    $this->insideZone = DeliveryZone::create([
        'name' => 'Inside Dhaka',
        'type' => 'inside_dhaka',
        'charge' => 60,
        'minimum_order_amount' => 5000,
        'status' => 'active',
    ]);

    $this->outsideZone = DeliveryZone::create([
        'name' => 'Outside Dhaka',
        'type' => 'outside_dhaka',
        'charge' => 120,
        'status' => 'active',
    ]);

    $this->dhaka = District::create([
        'name' => 'Dhaka',
        'division' => 'Dhaka Division',
        'delivery_zone_id' => $this->insideZone->id,
        'status' => 'active',
    ]);

    $this->sylhet = District::create([
        'name' => 'Sylhet',
        'division' => 'Sylhet Division',
        'delivery_zone_id' => $this->outsideZone->id,
        'status' => 'active',
    ]);
});

test('zone charge is used when there is no district override', function () {
    $response = $this->getJson('/api/delivery-zones/calculate?district=Sylhet&order_amount=1000');

    $response->assertOk()->assertJsonPath('data.available', true)
        ->assertJsonPath('data.source', 'zone')
        ->assertJsonPath('data.charge', 120)
        ->assertJsonPath('data.zone_name', 'Outside Dhaka');
});

test('district charge override beats the zone charge', function () {
    $this->sylhet->update(['charge' => 80]);

    $response = $this->getJson('/api/delivery-zones/calculate?district=Sylhet&order_amount=1000');

    $response->assertOk()->assertJsonPath('data.source', 'district')
        ->assertJsonPath('data.charge', 80)
        ->assertJsonPath('data.zone_id', $this->outsideZone->id);
});

test('site default shipping rate is used when a district has no zone', function () {
    SiteSetting::where('key', 'shipping_rate')->update(['value' => '99']);
    District::create([
        'name' => 'Bogura',
        'division' => 'Rajshahi Division',
        'status' => 'active',
    ]);

    $response = $this->getJson('/api/delivery-zones/calculate?district=Bogura&order_amount=100');

    $response->assertOk()->assertJsonPath('data.available', true)
        ->assertJsonPath('data.source', 'default')
        ->assertJsonPath('data.charge', 99);
});

test('district with no zone and no default rate is not deliverable', function () {
    SiteSetting::where('key', 'shipping_rate')->delete();
    District::create([
        'name' => 'Bogura',
        'division' => 'Rajshahi Division',
        'status' => 'active',
    ]);

    $response = $this->getJson('/api/delivery-zones/calculate?district=Bogura');

    $response->assertOk()->assertJsonPath('data.available', false);
});

test('inactive district is not deliverable', function () {
    $this->sylhet->update(['status' => 'inactive']);

    $response = $this->getJson('/api/delivery-zones/calculate?district=Sylhet');

    $response->assertOk()->assertJsonPath('data.available', false);
});

test('district of an inactive zone is not deliverable', function () {
    $this->outsideZone->update(['status' => 'inactive']);

    $response = $this->getJson('/api/delivery-zones/calculate?district=Sylhet');

    $response->assertOk()->assertJsonPath('data.available', false);
});

test('unknown district is not deliverable', function () {
    $response = $this->getJson('/api/delivery-zones/calculate?district=Atlantis');

    $response->assertOk()->assertJsonPath('data.available', false);
});

test('order amount above the zone threshold gets free delivery', function () {
    $response = $this->getJson('/api/delivery-zones/calculate?district=Dhaka&order_amount=5000');

    $response->assertOk()->assertJsonPath('data.free_delivery', true)
        ->assertJsonPath('data.charge', 0)
        ->assertJsonPath('data.original_charge', 60);
});

test('order amount below the zone threshold still pays the charge', function () {
    $response = $this->getJson('/api/delivery-zones/calculate?district=Dhaka&order_amount=4999');

    $response->assertOk()->assertJsonPath('data.free_delivery', false)
        ->assertJsonPath('data.charge', 60);
});

test('districts api returns effective charge division and zone', function () {
    $this->sylhet->update(['charge' => 90]);
    District::create([
        'name' => 'Bogura',
        'division' => 'Rajshahi Division',
        'status' => 'active',
    ]);
    SiteSetting::where('key', 'shipping_rate')->update(['value' => '70']);

    $response = $this->getJson('/api/delivery-zones/districts');

    $response->assertOk();
    $data = collect($response->json('data'))->keyBy('name');

    expect($data['Dhaka']['charge'])->toEqual(60.0)
        ->and($data['Dhaka']['source'])->toBe('zone')
        ->and($data['Dhaka']['division'])->toBe('Dhaka Division')
        ->and($data['Sylhet']['charge'])->toEqual(90.0)
        ->and($data['Sylhet']['source'])->toBe('district')
        ->and($data['Bogura']['charge'])->toEqual(70.0)
        ->and($data['Bogura']['source'])->toBe('default');
});

test('districts api hides districts without any charge source', function () {
    SiteSetting::where('key', 'shipping_rate')->delete();
    District::create([
        'name' => 'Bogura',
        'division' => 'Rajshahi Division',
        'status' => 'active',
    ]);

    $response = $this->getJson('/api/delivery-zones/districts');

    $response->assertOk();
    expect(collect($response->json('data'))->pluck('name'))->not->toContain('Bogura');
});
