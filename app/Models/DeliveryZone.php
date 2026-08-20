<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryZone extends Model
{
    protected $fillable = [
        'name',
        'type',
        'charge',
        'minimum_order_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'charge' => 'decimal:2',
            'minimum_order_amount' => 'decimal:2',
        ];
    }

    /**
     * The districts belonging to this delivery zone.
     */
    public function districts(): HasMany
    {
        return $this->hasMany(DeliveryZoneDistrict::class);
    }

    /**
     * Scope for active zones.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
