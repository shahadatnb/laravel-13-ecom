<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'brand_id',
        'category_id',
        'name',
        'name_bn',
        'slug',
        'short_description',
        'description',
        'sku',
        'barcode',
        'thumbnail',
        'regular_price',
        'sale_price',
        'wholesale_price',
        'cost_price',
        'stock',
        'minimum_stock',
        'maximum_order',
        'weight',
        'length',
        'width',
        'height',
        'tax_class',
        'shipping_class',
        'status',
        'product_type',
        'featured',
        'visibility',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'stock' => 'integer',
            'minimum_stock' => 'integer',
            'maximum_order' => 'integer',
            'regular_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'wholesale_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'weight' => 'decimal:2',
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'published_at' => 'datetime',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product')
            ->withTimestamps();
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductGallery::class);
    }

    public function variantImages(): HasMany
    {
        return $this->hasMany(ProductGallery::class, 'product_variant_id');
    }

    public function getTotalStockAttribute(): int
    {
        if ($this->product_type === 'simple') {
            return (int) $this->stock;
        }
        return (int) $this->variants->sum('stock');
    }

    public function getVariantsCountAttribute(): int
    {
        return $this->variants->count();
    }
}
