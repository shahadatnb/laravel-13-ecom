<?php

namespace Tests\Feature\Module;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_modules_enabled_by_default(): void
    {
        $this->assertTrue(module_enabled('affiliate'));
        $this->assertTrue(module_enabled('coupons'));
        $this->assertSame(config('modules.affiliate.label'), 'Affiliate Program');

        $this->getJson('/api/modules')
            ->assertOk()
            ->assertJsonPath('modules.0.key', 'affiliate')
            ->assertJsonPath('modules.0.enabled', true);
    }

    public function test_api_modules_endpoint_reflects_disabled_module(): void
    {
        config(['modules.wishlist.enabled' => false]);

        $this->getJson('/api/modules')
            ->assertOk()
            ->assertJsonPath('modules.2.key', 'wishlist')
            ->assertJsonPath('modules.2.enabled', false)
            ->assertJsonPath('modules.0.enabled', true);
    }

    public function test_disabling_a_module_blocks_customer_api_routes(): void
    {
        config(['modules.affiliate.enabled' => false]);

        $customer = Customer::factory()->create();

        $this->actingAs($customer, 'customer')
            ->getJson('/api/affiliates/my-profile')
            ->assertNotFound();

        $this->actingAs($customer, 'customer')
            ->getJson('/api/commissions/mine')
            ->assertNotFound();
    }

    public function test_disabling_a_module_blocks_admin_routes(): void
    {
        config(['modules.affiliate.enabled' => false]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/affiliates')
            ->assertNotFound();

        $this->actingAs($user)
            ->get('/admin/commissions')
            ->assertNotFound();
    }

    public function test_enabling_a_module_restores_routes(): void
    {
        config(['modules.affiliate.enabled' => false]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/affiliates')
            ->assertNotFound();

        config(['modules.affiliate.enabled' => true]);

        $this->actingAs($user)
            ->get('/admin/affiliates')
            ->assertOk();

        $customer = Customer::factory()->create();

        $this->actingAs($customer, 'customer')
            ->getJson('/api/affiliates/my-profile')
            ->assertOk();
    }

    public function test_admin_dashboard_hides_affiliate_menu_when_disabled(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Commissions')
            ->assertSee('Affiliates');

        config(['modules.affiliate.enabled' => false]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertDontSee('Commissions')
            ->assertDontSee('Affiliates');
    }
}
