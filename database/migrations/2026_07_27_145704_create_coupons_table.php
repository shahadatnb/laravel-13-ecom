<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type', 30); // percentage, fixed, free_shipping, buy_x_get_y, cashback, gift, referral
            $table->string('discount_type', 30); // product, category, cart, shipping, order
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->decimal('max_discount', 12, 2)->nullable();
            $table->decimal('min_order_amount', 12, 2)->nullable();
            $table->decimal('max_order_amount', 12, 2)->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('per_user_limit')->default(1);
            $table->unsignedInteger('total_used')->default(0);
            $table->string('status', 20)->default('draft'); // draft, active, inactive, expired, cancelled
            $table->integer('priority')->default(0);
            $table->string('scope', 30)->default('all'); // all, products, categories, brands, customers
            $table->boolean('is_auto_apply')->default(false);
            $table->boolean('is_first_order_only')->default(false);
            $table->boolean('is_guest_allowed')->default(false);
            $table->string('customer_restriction', 30)->nullable(); // guest, registered, vip, affiliate, wholesale, new, returning
            $table->string('payment_method', 50)->nullable();
            $table->string('shipping_method', 50)->nullable();
            $table->boolean('allow_multiple')->default(false);
            $table->json('settings')->nullable(); // buy_x_get_y config, stacking rules, etc.
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'valid_from', 'valid_until']);
            $table->index('type');
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
