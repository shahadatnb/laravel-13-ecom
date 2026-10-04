<?php

use App\Models\DeliveryZone;
use App\Models\User;

uses()->in('Feature\\Admin');

beforeEach(function () {
    $this->actingAs(User::factory()->create());

    $this->zone = DeliveryZone::create([
        'name' => 'Inside Dhaka',
        'type' => 'inside_dhaka',
        'charge' => 60,
        'minimum_order_amount' => 1000,
        'status' => 'active',
    ]);

    $this->district = $this->zone->districts()->create([
        'name' => 'Gazipur',
        'status' => 'active',
    ]);
});

test('admin can view district list', function () {
    $response = $this->get(route('admin.districts.index'));

    $response->assertOk();
    $response->assertViewIs('admin.district.index');
    $response->assertViewHas('districts');
});

test('district list can be searched by name', function () {
    $this->zone->districts()->create(['name' => 'Sylhet', 'status' => 'active']);

    $response = $this->get(route('admin.districts.index', ['q' => 'Sy']));

    $response->assertOk();
    $districts = $response->viewData('districts');
    expect($districts->pluck('name')->all())->toContain('Sylhet');
    expect($districts->pluck('name')->all())->not->toContain('Gazipur');
});

test('admin can open the district edit form', function () {
    $response = $this->get(route('admin.districts.edit', $this->district->id));

    $response->assertOk();
    $response->assertViewIs('admin.district.edit');
    $response->assertViewHas('district');
    $response->assertViewHas('zones');
});

test('admin can update a district', function () {
    $response = $this->put(route('admin.districts.update', $this->district->id), [
        'name' => 'Gazipur Sadar',
        'delivery_zone_id' => $this->zone->id,
        'status' => 'inactive',
    ]);

    $response->assertRedirect(route('admin.districts.index'));
    $response->assertSessionHas('success', 'District updated successfully.');

    $this->assertDatabaseHas('delivery_zone_districts', [
        'id' => $this->district->id,
        'name' => 'Gazipur Sadar',
        'status' => 'inactive',
    ]);
});

test('district name must be unique', function () {
    $otherZone = DeliveryZone::create([
        'name' => 'Outside Dhaka',
        'type' => 'outside_dhaka',
        'charge' => 120,
        'status' => 'active',
    ]);
    $otherZone->districts()->create(['name' => 'Sylhet', 'status' => 'active']);

    $response = $this->put(route('admin.districts.update', $this->district->id), [
        'name' => 'Sylhet',
        'delivery_zone_id' => $this->zone->id,
        'status' => 'active',
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('district status must be valid', function () {
    $response = $this->put(route('admin.districts.update', $this->district->id), [
        'name' => 'Gazipur',
        'delivery_zone_id' => $this->zone->id,
        'status' => 'archived',
    ]);

    $response->assertSessionHasErrors(['status']);
});

test('admin can toggle district status', function () {
    $response = $this->post(route('admin.districts.toggle-status', $this->district->id));

    $response->assertRedirect(route('admin.districts.index'));
    $this->assertDatabaseHas('delivery_zone_districts', [
        'id' => $this->district->id,
        'status' => 'inactive',
    ]);

    $this->post(route('admin.districts.toggle-status', $this->district->id));

    $this->assertDatabaseHas('delivery_zone_districts', [
        'id' => $this->district->id,
        'status' => 'active',
    ]);
});

test('inactive district is hidden from the frontend district list', function () {
    $this->zone->districts()->create(['name' => 'Dhaka', 'status' => 'active']);

    $this->post(route('admin.districts.toggle-status', $this->district->id));

    $response = $this->getJson('/api/delivery-zones/districts');

    $response->assertOk();
    $names = collect($response->json('data'))->pluck('name');

    expect($names)->toContain('Dhaka');
    expect($names)->not->toContain('Gazipur');
});

test('districts of an inactive zone are hidden from the frontend district list', function () {
    $this->zone->update(['status' => 'inactive']);

    $response = $this->getJson('/api/delivery-zones/districts');

    $response->assertOk();
    expect(collect($response->json('data'))->pluck('name'))->not->toContain('Gazipur');
});

test('saving a zone does not reset edited district statuses', function () {
    $this->district->update(['status' => 'inactive']);

    $response = $this->put(route('admin.delivery-zones.update', $this->zone->id), [
        'name' => 'Inside Dhaka',
        'districts' => ['Gazipur'],
        'charge' => 60,
        'minimum_order_amount' => 1000,
        'status' => 'active',
    ]);

    $response->assertRedirect(route('admin.delivery-zones.index'));

    $this->assertDatabaseHas('delivery_zone_districts', [
        'id' => $this->district->id,
        'name' => 'Gazipur',
        'status' => 'inactive',
    ]);
});

test('district data is missing required fields', function () {
    $response = $this->put(route('admin.districts.update', $this->district->id), [
        'status' => 'active',
    ]);

    $response->assertSessionHasErrors(['name', 'delivery_zone_id']);
});
