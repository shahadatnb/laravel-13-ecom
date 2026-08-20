<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'user_id',
        'order_number',
        'subtotal',
        'discount',
        'tax',
        'shipping_charge',
        'grand_total',
        'paid_amount',
        'due_amount',
        'currency',
        'status',
        'payment_status',
        'payment_method',
        'shipping_status',
        'shipping_address',
        'billing_address',
        'coupon_code',
        'coupon_discount',
        'notes',
        'admin_notes',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'shipping_charge' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_amount' => 'decimal:2',
            'coupon_discount' => 'decimal:2',
            'shipping_address' => 'array',
            'billing_address' => 'array',
        ];
    }

    // Status constants
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_PACKED = 'packed';

    public const STATUS_SHIPPED = 'shipped';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_RETURNED = 'returned';

    public const STATUS_REFUNDED = 'refunded';

    public const STATUS_FAILED = 'failed';

    // Payment status constants
    public const PAYMENT_PENDING = 'pending';

    public const PAYMENT_PAID = 'paid';

    public const PAYMENT_PARTIALLY_PAID = 'partially_paid';

    public const PAYMENT_FAILED = 'failed';

    public const PAYMENT_REFUNDED = 'refunded';

    public const PAYMENT_CANCELLED = 'cancelled';

    // Shipping status constants
    public const SHIPPING_PENDING = 'pending';

    public const SHIPPING_PROCESSING = 'processing';

    public const SHIPPING_PACKED = 'packed';

    public const SHIPPING_HANDED_TO_COURIER = 'handed_to_courier';

    public const SHIPPING_IN_TRANSIT = 'in_transit';

    public const SHIPPING_DELIVERED = 'delivered';

    public const SHIPPING_FAILED = 'failed';

    public const SHIPPING_RETURNED = 'returned';

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }


    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
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
    public function scopePending($query): void
    {
        $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query): void
    {
        $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeProcessing($query): void
    {
        $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeCompleted($query): void
    {
        $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeCancelled($query): void
    {
        $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopePaid($query): void
    {
        $query->where('payment_status', self::PAYMENT_PAID);
    }

    public function scopeDelivered($query): void
    {
        $query->where('shipping_status', self::SHIPPING_DELIVERED);
    }

    // Helpers
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    public function hasFullPayment(): bool
    {
        return $this->due_amount <= 0;
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_PACKED => 'Packed',
            self::STATUS_SHIPPED => 'Shipped',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_RETURNED => 'Returned',
            self::STATUS_REFUNDED => 'Refunded',
            self::STATUS_FAILED => 'Failed',
            default => ucfirst($this->status),
        };
    }

    public function getPaymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_PENDING => 'Pending',
            self::PAYMENT_PAID => 'Paid',
            self::PAYMENT_PARTIALLY_PAID => 'Partially Paid',
            self::PAYMENT_FAILED => 'Failed',
            self::PAYMENT_REFUNDED => 'Refunded',
            self::PAYMENT_CANCELLED => 'Cancelled',
            default => ucfirst($this->payment_status),
        };
    }
}
