<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'product_variant_id',
        'sku',
        'barcode',
        'current_stock',
        'reserved_stock',
        'minimum_stock',
        'maximum_stock',
        'reorder_level',
        'location',
    ];

    protected function casts(): array
    {
        return [
            'current_stock' => 'integer',
            'reserved_stock' => 'integer',
            'minimum_stock' => 'integer',
            'maximum_stock' => 'integer',
            'reorder_level' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'inventory_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(StockReservation::class, 'inventory_id');
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(InventoryAdjustment::class, 'inventory_id');
    }

    public function getAvailableStockAttribute(): int
    {
        return $this->current_stock - $this->reserved_stock;
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock && $this->minimum_stock > 0;
    }

    public function isOutOfStock(): bool
    {
        return $this->available_stock <= 0;
    }

    public function isOverstocked(): bool
    {
        return $this->maximum_stock !== null && $this->current_stock > $this->maximum_stock;
    }

    public function needsReorder(): bool
    {
        return $this->reorder_level > 0 && $this->current_stock <= $this->reorder_level;
    }
}
