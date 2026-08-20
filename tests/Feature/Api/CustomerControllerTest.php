<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use App\Models\CustomerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeCustomer(): Customer
    {
        return Customer::factory()->create();
    }

    public function test_customer_can_register_and_gets_token(): void
    {
        $email = 'new-'.Str::random(6).'@example.com';

        $this->postJson('/api/auth/register', [
            'name' => 'New Customer',
            'email' => $email,
            'phone' => '01712345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Registration successful.')
            ->assertJsonStructure(['data' => ['customer', 'token']]);

        $customer = Customer::where('email', $email)->firstOrFail();
        $this->assertNotNull($customer->profile);
        $this->assertSame(CustomerProfile::STATUS_ACTIVE, $customer->profile->status);
        $this->assertNotNull($customer->wallet);
    }

    public function test_duplicate_email_registration_is_rejected(): void
    {
        $customer = $this->makeCustomer();

        $this->postJson('/api/auth/register', [
            'name' => 'Another',
            'email' => $customer->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_customer_can_login_with_valid_credentials(): void
    {
        $customer = $this->makeCustomer();

        $this->postJson('/api/auth/login', [
            'email' => $customer->email,
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Login successful.')
            ->assertJsonPath('data.customer.email', $customer->email)
            ->assertJsonStructure(['data' => ['token']]);
    }

    public function test_login_rejects_wrong_credentials(): void
    {
        $customer = $this->makeCustomer();

        $this->postJson('/api/auth/login', [
            'email' => $customer->email,
            'password' => 'wrong-password',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_authenticated_customer_can_fetch_own_profile(): void
    {
        $customer = $this->makeCustomer();

        $this->actingAs($customer, 'customer')
            ->getJson('/api/customer')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', $customer->email)
            ->assertJsonPath('data.id', $customer->id);
    }

    public function test_profile_endpoint_requires_authentication(): void
    {
        $this->getJson('/api/customer')->assertUnauthorized();
    }
}
