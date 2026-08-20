<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $table = 'wallet_transactions';

    protected $fillable = [
        'wallet_id',
        'user_id',
        'customer_id',
        'transaction_code',
        'type',
        'category',
        'amount',
        'balance_before',
        'balance_after',
        'reference_type',
        'reference_id',
        'description',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public const TYPE_CREDIT = 'credit';

    public const TYPE_DEBIT = 'debit';



    public const CATEGORY_REFUND = 'refund';

    public const CATEGORY_BONUS = 'bonus';

    public const CATEGORY_PURCHASE = 'purchase';

    public const CATEGORY_ADMIN_ADJUSTMENT = 'admin_adjustment';

    public const CATEGORY_OTHER = 'other';

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeCredit($query): void
    {
        $query->where('type', self::TYPE_CREDIT);
    }

    public function scopeDebit($query): void
    {
        $query->where('type', self::TYPE_DEBIT);
    }

    public function isCredit(): bool
    {
        return $this->type === self::TYPE_CREDIT;
    }

    public function isDebit(): bool
    {
        return $this->type === self::TYPE_DEBIT;
    }
}
