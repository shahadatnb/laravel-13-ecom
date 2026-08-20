<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CouponCategory extends Pivot
{
    protected $table = 'coupon_categories';

    protected $fillable = [
        'coupon_id',
        'category_id',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
