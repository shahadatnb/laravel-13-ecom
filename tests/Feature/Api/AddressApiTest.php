<?php

use App\Models\Customer;
use App\Models\UserAddress;

uses()->group('api', 'addresses');

function addressForCustomer(Customer $customer, array $overrides = []): UserAddress
{
    return UserAddress::factory()->create(array_merge([
        'user_id' => null,
        'customer_id' => $customer->id,
        'is_default' => false,
    ], $overrides));
}

function addressPayload(array $overrides = []): array
{
    return array_merge([
        'address_type' => 'home',
        'recipient_name' => 'John Doe',
        'phone' => '01712345678',
        'address_line_1' => 'House 1, Road 1',
        'address_line_2' => 'Sector 1',
        'city' => 'Dhaka',
        'state' => 'Dhaka',
        'postal_code' => '1207',
        'country' => 'Bangladesh',
        'is_default' => false,
    ], $overrides);
}

test('unauthenticated user cannot access addresses', function () {
    $this->getJson('/api/addresses')->assertUnauthorized();
    $this->postJson('/api/addresses', addressPayload())->assertUnauthorized();
});

test('customer only sees their own addresses', function () {
    $customer = Customer::factory()->create();
    $other = Customer::factory()->create();

    $mine = addressForCustomer($customer);
    addressForCustomer($other);

    $response = $this->actingAs($customer, 'customer')->getJson('/api/addresses');

    $response->assertOk()->assertJson(['success' => true]);
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.id'))->toBe($mine->id);
});

test('customer can create an address', function () {
    $customer = Customer::factory()->create();

    $response = $this->actingAs($customer, 'customer')
        ->postJson('/api/addresses', addressPayload());

    $response->assertCreated()->assertJson([
        'success' => true,
        'message' => 'Address added successfully.',
        'data' => [
            'recipient_name' => 'John Doe',
            'city' => 'Dhaka',
            'country' => 'Bangladesh',
        ],
    ]);

    $address = UserAddress::where('customer_id', $customer->id)->first();
    expect($address)->not->toBeNull();
    expect($address->address_line_1)->toBe('House 1, Road 1');
});

test('creating an address requires the required fields', function () {
    $customer = Customer::factory()->create();

    $this->actingAs($customer, 'customer')
        ->postJson('/api/addresses', ['state' => 'Dhaka'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'address_type',
            'recipient_name',
            'phone',
            'address_line_1',
            'city',
            'country',
        ]);
});

test('address type must be one of the allowed values', function () {
    $customer = Customer::factory()->create();

    $this->actingAs($customer, 'customer')
        ->postJson('/api/addresses', addressPayload(['address_type' => 'villa']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['address_type']);
});

test('customer can update their own address', function () {
    $customer = Customer::factory()->create();
    $address = addressForCustomer($customer);

    $response = $this->actingAs($customer, 'customer')
        ->putJson("/api/addresses/{$address->id}", addressPayload([
            'city' => 'Chattogram',
            'recipient_name' => 'Renamed Recipient',
        ]));

    $response->assertOk()->assertJson([
        'success' => true,
        'message' => 'Address updated successfully.',
        'data' => [
            'city' => 'Chattogram',
            'recipient_name' => 'Renamed Recipient',
        ],
    ]);

    expect($address->fresh()->city)->toBe('Chattogram');
});

test('customer cannot update another customers address', function () {
    $customer = Customer::factory()->create();
    $other = Customer::factory()->create();
    $foreign = addressForCustomer($other);
    $originalName = $foreign->recipient_name;

    $this->actingAs($customer, 'customer')
        ->putJson("/api/addresses/{$foreign->id}", addressPayload())
        ->assertForbidden();

    expect($foreign->fresh()->recipient_name)->toBe($originalName);
});

test('customer can delete their own address', function () {
    $customer = Customer::factory()->create();
    $address = addressForCustomer($customer);

    $this->actingAs($customer, 'customer')
        ->deleteJson("/api/addresses/{$address->id}")
        ->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Address deleted successfully.',
        ]);

    $this->assertSoftDeleted('user_addresses', ['id' => $address->id]);
});

test('customer cannot delete another customers address', function () {
    $customer = Customer::factory()->create();
    $other = Customer::factory()->create();
    $foreign = addressForCustomer($other);

    $this->actingAs($customer, 'customer')
        ->deleteJson("/api/addresses/{$foreign->id}")
        ->assertForbidden();

    expect(UserAddress::find($foreign->id))->not->toBeNull();
});

test('setting a new default address clears the previous default', function () {
    $customer = Customer::factory()->create();
    $first = addressForCustomer($customer, ['is_default' => true]);
    $second = addressForCustomer($customer);

    $this->actingAs($customer, 'customer')
        ->postJson('/api/addresses', addressPayload(['is_default' => true]))
        ->assertCreated();

    expect($first->fresh()->is_default)->toBeFalse();
    expect($second->fresh()->is_default)->toBeFalse();
    expect(UserAddress::where('customer_id', $customer->id)->where('is_default', true)->count())->toBe(1);
});

test('updating an address to default keeps only one default', function () {
    $customer = Customer::factory()->create();
    $first = addressForCustomer($customer, ['is_default' => true]);
    $second = addressForCustomer($customer);

    $this->actingAs($customer, 'customer')
        ->putJson("/api/addresses/{$second->id}", addressPayload(['is_default' => true]))
        ->assertOk();

    expect($first->fresh()->is_default)->toBeFalse();
    expect($second->fresh()->is_default)->toBeTrue();
});
