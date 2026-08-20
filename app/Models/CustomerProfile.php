<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customer_profiles';

    protected $fillable = [
        'customer_id',
        'user_id',
        'customer_code',
        'gender',
        'date_of_birth',
        'marketing_opt_in',
        'status',
        'notes',
    ];

    protected $casts = [
        'marketing_opt_in' => 'boolean',
        'date_of_birth' => 'date',
    ];

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_BANNED = 'banned';

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query): void
    {
        $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactive($query): void
    {
        $query->where('status', self::STATUS_INACTIVE);
    }

    public function scopeBanned($query): void
    {
        $query->where('status', self::STATUS_BANNED);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isBanned(): bool
    {
        return $this->status === self::STATUS_BANNED;
    }
}
