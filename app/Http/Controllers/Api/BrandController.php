<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;

class BrandController extends Controller
{
    /**
     * List all active brands with product counts.
     */
    public function index()
    {
        $brands = Brand::where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'slug' => $brand->slug,
                    'logo' => $brand->logo,
                    'product_count' => $brand->products()
                        ->whereIn('status', ['published', 'active'])
                        ->count(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $brands,
        ]);
    }
}
