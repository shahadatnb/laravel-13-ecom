<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatBoxesTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_stat_boxes_show_real_data(): void
    {
        $admin = User::factory()->create();

        Product::factory()->count(5)->create();
        Customer::factory()->count(2)->create();

        $customer = Customer::factory()->create();
        Order::factory()->create([
            'customer_id' => $customer->id,
            'grand_total' => 1000,
            'paid_amount' => 1000,
            'payment_status' => Order::PAYMENT_PAID,
        ]);
        Order::factory()->create([
            'customer_id' => $customer->id,
            'grand_total' => 500,
            'paid_amount' => 0,
            'payment_status' => Order::PAYMENT_PENDING,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('<h3>5</h3>', false)    // products
            ->assertSee('<h3>2</h3>', false)    // orders
            ->assertSee('<h3>3</h3>', false)    // customers
            ->assertSee('<h3>৳1,000</h3>', false); // revenue = only the paid order
    }

    public function test_dashboard_revenue_counts_only_paid_orders(): void
    {
        $admin = User::factory()->create();
        $customer = Customer::factory()->create();

        Order::factory()->create([
            'customer_id' => $customer->id,
            'grand_total' => 1000,
            'paid_amount' => 1000,
            'payment_status' => Order::PAYMENT_PAID,
        ]);
        Order::factory()->create([
            'customer_id' => $customer->id,
            'grand_total' => 2000,
            'paid_amount' => 0,
            'payment_status' => Order::PAYMENT_PENDING,
        ]);
        Order::factory()->create([
            'customer_id' => $customer->id,
            'grand_total' => 3000,
            'paid_amount' => 0,
            'payment_status' => Order::PAYMENT_CANCELLED,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('৳1,000', false)
            ->assertDontSee('৳6,000', false);
    }
}
