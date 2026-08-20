<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        // Active percentage coupon
        Coupon::create([
            'code' => 'SUMMER20',
            'title' => 'Summer Sale 20% Off',
            'description' => 'Get 20% off on all orders this summer. Maximum discount of $50.',
            'type' => Coupon::TYPE_PERCENTAGE,
            'discount_type' => Coupon::DISCOUNT_CART,
            'discount_value' => 20,
            'max_discount' => 50.00,
            'min_order_amount' => 50.00,
            'usage_limit' => 500,
            'per_user_limit' => 1,
            'total_used' => 0,
            'status' => Coupon::STATUS_ACTIVE,
            'priority' => 10,
            'scope' => Coupon::SCOPE_ALL,
            'is_auto_apply' => false,
            'is_first_order_only' => false,
            'is_guest_allowed' => true,
            'valid_from' => now(),
            'valid_until' => now()->addMonths(3),
            'created_by' => $admin?->id,
        ]);

        // Fixed amount coupon
        Coupon::create([
            'code' => 'SAVE25',
            'title' => 'Save $25',
            'description' => 'Get $25 off on orders over $100.',
            'type' => Coupon::TYPE_FIXED,
            'discount_type' => Coupon::DISCOUNT_CART,
            'discount_value' => 25.00,
            'min_order_amount' => 100.00,
            'usage_limit' => 200,
            'per_user_limit' => 2,
            'total_used' => 0,
            'status' => Coupon::STATUS_ACTIVE,
            'priority' => 5,
            'scope' => Coupon::SCOPE_ALL,
            'is_auto_apply' => false,
            'is_first_order_only' => false,
            'is_guest_allowed' => true,
            'valid_from' => now(),
            'valid_until' => now()->addMonths(6),
            'created_by' => $admin?->id,
        ]);

        // Free shipping coupon
        Coupon::create([
            'code' => 'FREESHIP',
            'title' => 'Free Shipping',
            'description' => 'Free shipping on orders over $75.',
            'type' => Coupon::TYPE_FREE_SHIPPING,
            'discount_type' => Coupon::DISCOUNT_SHIPPING,
            'discount_value' => 0,
            'min_order_amount' => 75.00,
            'usage_limit' => 1000,
            'per_user_limit' => 5,
            'total_used' => 0,
            'status' => Coupon::STATUS_ACTIVE,
            'priority' => 1,
            'scope' => Coupon::SCOPE_ALL,
            'is_auto_apply' => true,
            'is_first_order_only' => false,
            'is_guest_allowed' => true,
            'valid_from' => now(),
            'valid_until' => now()->addMonths(12),
            'created_by' => $admin?->id,
        ]);

        // First order only
        Coupon::create([
            'code' => 'WELCOME10',
            'title' => 'Welcome 10% Off',
            'description' => '10% off your first order!',
            'type' => Coupon::TYPE_PERCENTAGE,
            'discount_type' => Coupon::DISCOUNT_ORDER,
            'discount_value' => 10,
            'max_discount' => 30.00,
            'usage_limit' => 1000,
            'per_user_limit' => 1,
            'total_used' => 0,
            'status' => Coupon::STATUS_ACTIVE,
            'priority' => 100,
            'scope' => Coupon::SCOPE_ALL,
            'is_auto_apply' => false,
            'is_first_order_only' => true,
            'is_guest_allowed' => true,
            'valid_from' => now(),
            'valid_until' => now()->addMonths(12),
            'created_by' => $admin?->id,
        ]);

        // Draft coupon
        Coupon::create([
            'code' => 'DRAFT50',
            'title' => 'Flash Sale 50% Off',
            'description' => 'Draft coupon for upcoming flash sale.',
            'type' => Coupon::TYPE_PERCENTAGE,
            'discount_type' => Coupon::DISCOUNT_CART,
            'discount_value' => 50,
            'max_discount' => 200.00,
            'usage_limit' => 100,
            'per_user_limit' => 1,
            'total_used' => 0,
            'status' => Coupon::STATUS_DRAFT,
            'priority' => 50,
            'scope' => Coupon::SCOPE_ALL,
            'is_auto_apply' => false,
            'is_first_order_only' => false,
            'is_guest_allowed' => false,
            'valid_from' => now()->addMonths(1),
            'valid_until' => now()->addMonths(2),
            'created_by' => $admin?->id,
        ]);

        // Expired coupon
        Coupon::create([
            'code' => 'EXPIRED15',
            'title' => 'Spring Sale 15% Off',
            'description' => 'Spring promotion - expired.',
            'type' => Coupon::TYPE_PERCENTAGE,
            'discount_type' => Coupon::DISCOUNT_CART,
            'discount_value' => 15,
            'max_discount' => 40.00,
            'usage_limit' => 500,
            'per_user_limit' => 1,
            'total_used' => 350,
            'status' => Coupon::STATUS_EXPIRED,
            'priority' => 0,
            'scope' => Coupon::SCOPE_ALL,
            'is_auto_apply' => false,
            'is_first_order_only' => false,
            'is_guest_allowed' => true,
            'valid_from' => now()->subMonths(6),
            'valid_until' => now()->subMonth(),
            'created_by' => $admin?->id,
        ]);

        if (Coupon::count() === 0) {
            Coupon::factory(10)->active()->create();
        }
    }
}
