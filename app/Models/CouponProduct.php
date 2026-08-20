<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CouponProduct extends Pivot
{
    protected $table = 'coupon_products';

    protected $fillable = [
        'coupon_id',
        'product_id',
        'is_excluded',
    ];

    protected function casts(): array
    {
        return [
            'is_excluded' => 'boolean',
        ];
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
