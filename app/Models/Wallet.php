<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'wallets';

    protected $fillable = [
        'user_id',
        'customer_id',
        'balance',
        'locked_balance',
        'status',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'locked_balance' => 'decimal:2',
    ];

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_FROZEN = 'frozen';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function scopeActive($query): void
    {
        $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeFrozen($query): void
    {
        $query->where('status', self::STATUS_FROZEN);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isFrozen(): bool
    {
        return $this->status === self::STATUS_FROZEN;
    }

    public function getAvailableBalanceAttribute(): float
    {
        return $this->balance - $this->locked_balance;
    }

    public function hasSufficientBalance(float $amount): bool
    {
        return $this->available_balance >= $amount;
    }
}
