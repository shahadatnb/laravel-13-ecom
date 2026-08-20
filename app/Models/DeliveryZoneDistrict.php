<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryZoneDistrict extends Model
{
    protected $fillable = [
        'delivery_zone_id',
        'name',
        'status',
    ];

    public function zone()
    {
        return $this->belongsTo(DeliveryZone::class, 'delivery_zone_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
