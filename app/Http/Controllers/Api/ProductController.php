<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'categories', 'brand', 'images', 'variants']);

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by brand
        if ($request->has('brand')) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('slug', $request->brand);
            });
        }

        // Filter by price range (use sale_price or regular_price)
        if ($request->has('min_price')) {
            $query->whereRaw('COALESCE(sale_price, regular_price, 0) >= ?', [$request->min_price]);
        }
        if ($request->has('max_price')) {
            $query->whereRaw('COALESCE(sale_price, regular_price, 0) <= ?', [$request->max_price]);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Sorting - map user-friendly sort keys to actual columns
        $sortMap = [
            'latest' => ['created_at', 'desc'],
            'price_low' => ['COALESCE(sale_price, regular_price, 0)', 'asc'],
            'price_high' => ['COALESCE(sale_price, regular_price, 0)', 'desc'],
            'name' => ['name', 'asc'],
            'created_at' => ['created_at', 'desc'],
            'updated_at' => ['updated_at', 'desc'],
        ];

        $sortKey = $request->get('sort', 'latest');
        $sortOrder = $request->get('order', 'desc');

        if (isset($sortMap[$sortKey])) {
            [$sortBy, $defaultOrder] = $sortMap[$sortKey];
            $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : $defaultOrder;
            $query->orderBy($sortBy, $sortOrder);
        } else {
            // Default to newest first for unknown sort keys
            $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $perPage = $request->get('per_page', 12);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Display featured products.
     */
    public function featured()
    {
        $products = Product::with(['category', 'categories', 'brand', 'images', 'variants'])
            ->where('featured', true)
            ->whereIn('status', ['published', 'active'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Display new arrivals.
     */
    public function newArrivals()
    {
        $products = Product::with(['category', 'categories', 'brand', 'images', 'variants'])
            ->whereIn('status', ['published', 'active'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Display a single product.
     */
    public function show($slug)
    {
        $product = Product::with(['category', 'categories', 'brand', 'images', 'variants.images'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Get related products
        $relatedProducts = Product::with(['images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->whereIn('status', ['published', 'active'])
            ->limit(4)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $product,
            'related_products' => $relatedProducts,
        ]);
    }

    /**
     * Search products.
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');

        if (empty($search)) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $products = Product::with(['category', 'categories', 'brand', 'images', 'variants'])
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            })
            ->whereIn('status', ['published', 'active'])
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
}
