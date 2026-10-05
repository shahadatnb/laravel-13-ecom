<?php

use App\Models\Customer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses()->group('api', 'profile');

function profilePayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Updated Customer',
        'email' => 'updated@example.com',
        'phone' => '01700000000',
    ], $overrides);
}

test('unauthenticated user cannot update profile', function () {
    $this->postJson('/api/customer')->assertUnauthorized();
});

test('customer can update name email and phone', function () {
    $customer = Customer::factory()->create();

    $response = $this->actingAs($customer, 'customer')
        ->postJson('/api/customer', profilePayload());

    $response->assertOk()->assertJson([
        'success' => true,
        'message' => 'Profile updated successfully.',
        'data' => [
            'name' => 'Updated Customer',
            'email' => 'updated@example.com',
            'phone' => '01700000000',
        ],
    ]);

    $fresh = $customer->fresh();
    expect($fresh->name)->toBe('Updated Customer');
    expect($fresh->email)->toBe('updated@example.com');
    expect($fresh->phone)->toBe('01700000000');
});

test('customer cannot use an email that belongs to another customer', function () {
    $customer = Customer::factory()->create();
    Customer::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs($customer, 'customer')
        ->postJson('/api/customer', profilePayload(['email' => 'taken@example.com']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('profile update requires name and a valid email', function () {
    $customer = Customer::factory()->create();

    $this->actingAs($customer, 'customer')
        ->postJson('/api/customer', ['name' => '', 'email' => 'not-an-email'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email']);
});

test('changing the email clears email verification', function () {
    $customer = Customer::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($customer, 'customer')
        ->postJson('/api/customer', profilePayload(['email' => 'brandnew@example.com']))
        ->assertOk();

    expect($customer->fresh()->email_verified_at)->toBeNull();
});

test('customer can upload a new avatar and the old file is removed', function () {
    Storage::fake('public');

    $customer = Customer::factory()->create(['avatar' => 'avatars/old.jpg']);
    Storage::disk('public')->put('avatars/old.jpg', 'old-image-content');

    $response = $this->actingAs($customer, 'customer')->post('/api/customer', [
        'name' => $customer->name,
        'email' => $customer->email,
        'avatar' => UploadedFile::fake()->image('avatar.jpg'),
    ], ['Accept' => 'application/json']);

    $response->assertOk()->assertJson(['success' => true]);

    $fresh = $customer->fresh();
    expect($fresh->avatar)->toStartWith('avatars/');
    Storage::disk('public')->assertExists($fresh->avatar);
    Storage::disk('public')->assertMissing('avatars/old.jpg');
});

test('profile update without an avatar keeps the current one', function () {
    Storage::fake('public');

    $customer = Customer::factory()->create(['avatar' => 'avatars/current.jpg']);
    Storage::disk('public')->put('avatars/current.jpg', 'content');

    $this->actingAs($customer, 'customer')
        ->postJson('/api/customer', profilePayload())
        ->assertOk();

    expect($customer->fresh()->avatar)->toBe('avatars/current.jpg');
    Storage::disk('public')->assertExists('avatars/current.jpg');
});

test('avatar must be a valid image file', function () {
    Storage::fake('public');

    $customer = Customer::factory()->create();

    $this->actingAs($customer, 'customer')->post('/api/customer', [
        'name' => $customer->name,
        'email' => $customer->email,
        'avatar' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
    ], ['Accept' => 'application/json'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['avatar']);
});
