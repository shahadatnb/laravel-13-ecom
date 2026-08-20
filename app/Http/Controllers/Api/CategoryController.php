<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::with(['children', 'products'])
            ->where('parent_id', null)
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Display active categories.
     */
    public function active()
    {
        $categories = Category::with('products')
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Display a single category.
     */
    public function show($slug)
    {
        $category = Category::with(['children', 'products.category', 'products.brand', 'products.images'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }
}
