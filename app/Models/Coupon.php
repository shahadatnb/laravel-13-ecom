<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'title',
        'description',
        'type',
        'discount_type',
        'discount_value',
        'max_discount',
        'min_order_amount',
        'max_order_amount',
        'usage_limit',
        'per_user_limit',
        'total_used',
        'status',
        'priority',
        'scope',
        'is_auto_apply',
        'is_first_order_only',
        'is_guest_allowed',
        'customer_restriction',
        'payment_method',
        'shipping_method',
        'allow_multiple',
        'settings',
        'valid_from',
        'valid_until',
        'last_used_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'max_order_amount' => 'decimal:2',
            'is_auto_apply' => 'boolean',
            'is_first_order_only' => 'boolean',
            'is_guest_allowed' => 'boolean',
            'allow_multiple' => 'boolean',
            'settings' => 'array',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
            'last_used_at' => 'datetime',
        ];
    }

    // Type constants
    public const TYPE_PERCENTAGE = 'percentage';

    public const TYPE_FIXED = 'fixed';

    public const TYPE_FREE_SHIPPING = 'free_shipping';

    public const TYPE_BUY_X_GET_Y = 'buy_x_get_y';

    public const TYPE_CASHBACK = 'cashback';

    public const TYPE_GIFT = 'gift';

    public const TYPE_REFERRAL = 'referral';

    // Discount type constants
    public const DISCOUNT_PRODUCT = 'product';

    public const DISCOUNT_CATEGORY = 'category';

    public const DISCOUNT_CART = 'cart';

    public const DISCOUNT_SHIPPING = 'shipping';

    public const DISCOUNT_ORDER = 'order';

    // Status constants
    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_CANCELLED = 'cancelled';

    // Scope constants
    public const SCOPE_ALL = 'all';

    public const SCOPE_PRODUCTS = 'products';

    public const SCOPE_CATEGORIES = 'categories';

    public const SCOPE_BRANDS = 'brands';

    public const SCOPE_CUSTOMERS = 'customers';

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'coupon_products')
            ->withPivot('is_excluded')
            ->withTimestamps();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'coupon_categories')
            ->withPivot('is_excluded')
            ->withTimestamps();
    }

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coupon_customers')
            ->withTimestamps();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query): void
    {
        $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeValid($query): void
    {
        $query->where('status', self::STATUS_ACTIVE)
            ->where(function ($q) {
                $q->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            });
    }

    public function scopeAutoApply($query): void
    {
        $query->where('is_auto_apply', true);
    }

    // Helpers
    public function isValid(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        if ($this->valid_from && now()->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && now()->gt($this->valid_until)) {
            return false;
        }

        return true;
    }

    public function hasRemainingUsage(): bool
    {
        if ($this->usage_limit === null) {
            return true;
        }

        return $this->total_used < $this->usage_limit;
    }

    public function isExpired(): bool
    {
        return $this->valid_until && now()->gt($this->valid_until);
    }

    public function isPercentage(): bool
    {
        return $this->type === self::TYPE_PERCENTAGE;
    }

    public function isFixed(): bool
    {
        return $this->type === self::TYPE_FIXED;
    }

    public function isFreeShipping(): bool
    {
        return $this->type === self::TYPE_FREE_SHIPPING;
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_PERCENTAGE => 'Percentage',
            self::TYPE_FIXED => 'Fixed Amount',
            self::TYPE_FREE_SHIPPING => 'Free Shipping',
            self::TYPE_BUY_X_GET_Y => 'Buy X Get Y',
            self::TYPE_CASHBACK => 'Cashback',
            self::TYPE_GIFT => 'Gift Coupon',
            self::TYPE_REFERRAL => 'Referral',
            default => ucfirst($this->type),
        };
    }
}
