<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Statuses the public API is allowed to expose.
     */
    private const PUBLIC_STATUSES = ['published'];

    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'categories', 'brand', 'images', 'variants']);

        // Public API only exposes published products (draft/hidden/archived excluded)
        $query->whereIn('status', self::PUBLIC_STATUSES);

        // Filter by category (includes all subcategories recursively)
        // $categoryIds is ordered: parent first, then children by sort_order
        $categoryIds = null;
        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $categoryIds = $this->getCategoryAndChildIds($category);
                // Grouped so the OR cannot bypass the status filter above
                $query->where(function ($q) use ($categoryIds) {
                    $q->whereIn('category_id', $categoryIds)
                        ->orWhereHas('categories', function ($cq) use ($categoryIds) {
                            $cq->whereIn('category_product.category_id', $categoryIds);
                        });
                });
            }
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

        // When browsing a category with the default sort, group products by
        // their (sub)category sort_order so subcategory order drives the list.
        // Products outside the category tree are pushed to the end (value 999999).
        if ($categoryIds !== null && $sortKey === 'latest') {
            $orderedIds = array_map('intval', $categoryIds);
            $field = 'FIELD(category_id, '.implode(', ', $orderedIds).')';
            $query->orderByRaw("CASE WHEN {$field} = 0 THEN 999999 ELSE {$field} END");
        }

        if (isset($sortMap[$sortKey])) {
            [$sortBy, $defaultOrder] = $sortMap[$sortKey];
            $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : $defaultOrder;
            if (str_contains($sortBy, '(')) {
                // Expression (e.g. COALESCE) must not be quoted as a column name
                $query->orderByRaw("{$sortBy} {$sortOrder}");
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
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
            ->whereIn('status', self::PUBLIC_STATUSES)
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
            ->whereIn('status', self::PUBLIC_STATUSES)
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
            ->whereIn('status', self::PUBLIC_STATUSES)
            ->firstOrFail();

        // Get 5 other products from the same category (excluding current)
        $relatedProducts = Product::with(['category', 'brand', 'images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->whereIn('status', self::PUBLIC_STATUSES)
            ->limit(5)
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
            ->whereIn('status', self::PUBLIC_STATUSES)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Recursively collect category ID and all descendant IDs.
     * Order is meaningful: parent first, then children by sort_order (depth-first).
     */
    private function getCategoryAndChildIds(Category $category): array
    {
        $ids = [$category->id];

        foreach ($category->children()->orderBy('sort_order')->orderBy('id')->get() as $child) {
            $ids = array_merge($ids, $this->getCategoryAndChildIds($child));
        }

        return $ids;
    }
}
