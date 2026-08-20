<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'logo',
        'banner',
        'website',
        'country',
        'email',
        'phone',
        'address',
        'sort_order',
        'featured',
        'status',
        'visibility',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
    ];

    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
        ];
    }
}
