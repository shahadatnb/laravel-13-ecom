<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = 'product_galleries';

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'image',
        'alt_text',
        'caption',
        'sort_order',
    ];
}
