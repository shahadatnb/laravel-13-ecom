<?php

use App\Models\DeliveryZone;
use App\Models\District;
use App\Models\User;

uses()->in('Feature\\Admin');

beforeEach(function () {
    $this->actingAs(User::factory()->create());

    $this->dhakaDistrict = District::create([
        'name' => 'Dhaka',
        'division' => 'Dhaka Division',
        'status' => 'active',
    ]);

    $this->gazipurDistrict = District::create([
        'name' => 'Gazipur',
        'division' => 'Dhaka Division',
        'status' => 'active',
    ]);

    $this->sylhetDistrict = District::create([
        'name' => 'Sylhet',
        'division' => 'Sylhet Division',
        'status' => 'active',
    ]);
});

test('admin can create a zone and assign districts by id', function () {
    $response = $this->post(route('admin.delivery-zones.store'), [
        'name' => 'Inside Dhaka',
        'districts' => [$this->dhakaDistrict->id, $this->gazipurDistrict->id],
        'charge' => 60,
        'minimum_order_amount' => 5000,
        'status' => 'active',
    ]);

    $response->assertRedirect(route('admin.delivery-zones.index'));
    $response->assertSessionHas('success', 'Delivery zone created successfully.');

    $zone = DeliveryZone::where('name', 'Inside Dhaka')->firstOrFail();
    expect($zone->charge)->toEqual(60.0);

    $this->assertDatabaseHas('districts', ['id' => $this->dhakaDistrict->id, 'delivery_zone_id' => $zone->id]);
    $this->assertDatabaseHas('districts', ['id' => $this->gazipurDistrict->id, 'delivery_zone_id' => $zone->id]);
    $this->assertDatabaseHas('districts', ['id' => $this->sylhetDistrict->id, 'delivery_zone_id' => null]);
});

test('zone form is populated from the districts table', function () {
    $response = $this->get(route('admin.delivery-zones.create'));

    $response->assertOk();
    $response->assertSee('Dhaka Division');
    $response->assertSee('Sylhet');
});

test('zone creation rejects an unknown district id', function () {
    $response = $this->post(route('admin.delivery-zones.store'), [
        'name' => 'Broken Zone',
        'districts' => [999999],
        'charge' => 60,
        'status' => 'active',
    ]);

    $response->assertSessionHasErrors(['districts.0']);
});

test('updating a zone unassigns unchecked districts without deleting them', function () {
    $zone = DeliveryZone::create([
        'name' => 'Inside Dhaka',
        'type' => 'inside_dhaka',
        'charge' => 60,
        'status' => 'active',
    ]);
    $this->dhakaDistrict->update(['delivery_zone_id' => $zone->id]);
    $this->gazipurDistrict->update(['delivery_zone_id' => $zone->id]);

    $response = $this->put(route('admin.delivery-zones.update', $zone->id), [
        'name' => 'Inside Dhaka',
        'districts' => [$this->dhakaDistrict->id],
        'charge' => 75,
        'status' => 'active',
    ]);

    $response->assertRedirect(route('admin.delivery-zones.index'));

    $this->assertDatabaseHas('districts', ['id' => $this->dhakaDistrict->id, 'delivery_zone_id' => $zone->id]);
    $this->assertDatabaseHas('districts', ['id' => $this->gazipurDistrict->id, 'delivery_zone_id' => null]);
    $this->assertDatabaseHas('districts', ['id' => $this->sylhetDistrict->id, 'status' => 'active']);
});

test('saving a zone preserves district charge overrides', function () {
    $zone = DeliveryZone::create([
        'name' => 'Inside Dhaka',
        'type' => 'inside_dhaka',
        'charge' => 60,
        'status' => 'active',
    ]);
    $this->gazipurDistrict->update(['delivery_zone_id' => $zone->id, 'charge' => 45]);

    $this->put(route('admin.delivery-zones.update', $zone->id), [
        'name' => 'Inside Dhaka',
        'districts' => [$this->gazipurDistrict->id],
        'charge' => 70,
        'status' => 'active',
    ]);

    $this->assertDatabaseHas('districts', [
        'id' => $this->gazipurDistrict->id,
        'charge' => 45,
        'delivery_zone_id' => $zone->id,
    ]);
});

test('admin can delete a zone and its districts survive unassigned', function () {
    $zone = DeliveryZone::create([
        'name' => 'Temp Zone',
        'type' => 'outside_dhaka',
        'charge' => 100,
        'status' => 'active',
    ]);
    $this->sylhetDistrict->update(['delivery_zone_id' => $zone->id]);

    $response = $this->delete(route('admin.delivery-zones.destroy', $zone->id));

    $response->assertRedirect(route('admin.delivery-zones.index'));
    $this->assertDatabaseMissing('delivery_zones', ['id' => $zone->id]);
    $this->assertDatabaseHas('districts', ['id' => $this->sylhetDistrict->id, 'delivery_zone_id' => null]);
});
