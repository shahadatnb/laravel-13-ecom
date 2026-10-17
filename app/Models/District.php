<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class District extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'name',
        'division',
        'delivery_zone_id',
        'charge',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'charge' => 'decimal:2',
        ];
    }

    /**
     * The delivery zone that determines this district's rate
     * when no per-district charge override is set.
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZone::class, 'delivery_zone_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
