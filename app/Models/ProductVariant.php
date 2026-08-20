<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'barcode',
        'price',
        'stock',
        'attributes',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    /**
     * Get the variant attributes as an array.
     *
     * Handles double-encoded JSON from existing records where attributes
     * were stored as a JSON-string inside the JSON column.
     */
    public function getAttributesAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            // Try normal decode first ({"Size":"S","Color":"Blue"})
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }

            // Handle double-encoded: "{\"Size\":\"S\"}" → the value is a JSON string
            // json_decode gives us the inner string, then decode again
            $inner = json_decode($value);
            if (is_string($inner)) {
                $decoded = json_decode($inner, true);

                return is_array($decoded) ? $decoded : [];
            }
        }

        return [];
    }

    /**
     * Set the variant attributes, ensuring it's stored as a proper JSON object.
     */
    public function setAttributesAttribute($value): void
    {
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $this->attributes['attributes'] = json_encode($decoded);

                return;
            }
        }

        $this->attributes['attributes'] = is_array($value)
            ? json_encode($value)
            : ($value ?? '[]');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductGallery::class);
    }
}
