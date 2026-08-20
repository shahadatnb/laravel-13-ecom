<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $table = 'inventory_transactions';

    protected $fillable = [
        'inventory_id',
        'product_id',
        'warehouse_id',
        'product_variant_id',
        'user_id',
        'type',
        'reference_type',
        'reference_id',
        'reference_number',
        'quantity_before',
        'quantity_change',
        'quantity_after',
        'unit_cost',
        'status',
        'reason',
        'batch_number',
        'expiry_date',
        'created_by_type',
    ];

    protected function casts(): array
    {
        return [
            'quantity_before' => 'integer',
            'quantity_change' => 'integer',
            'quantity_after' => 'integer',
            'unit_cost' => 'decimal:2',
            'expiry_date' => 'date',
        ];
    }

    // Transaction type constants
    public const TYPE_PURCHASE = 'purchase';

    public const TYPE_SALE = 'sale';

    public const TYPE_RETURN = 'return';

    public const TYPE_REFUND = 'refund';

    public const TYPE_ADJUSTMENT = 'adjustment';

    public const TYPE_TRANSFER_IN = 'transfer_in';

    public const TYPE_TRANSFER_OUT = 'transfer_out';

    public const TYPE_DAMAGE = 'damage';

    public const TYPE_EXPIRED = 'expired';

    public const TYPE_OPENING_BALANCE = 'opening_balance';

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isInbound(): bool
    {
        return in_array($this->type, [
            self::TYPE_PURCHASE,
            self::TYPE_RETURN,
            self::TYPE_REFUND,
            self::TYPE_TRANSFER_IN,
            self::TYPE_ADJUSTMENT,
            self::TYPE_OPENING_BALANCE,
        ]);
    }

    public function isOutbound(): bool
    {
        return in_array($this->type, [
            self::TYPE_SALE,
            self::TYPE_TRANSFER_OUT,
            self::TYPE_DAMAGE,
            self::TYPE_EXPIRED,
        ]);
    }
}
