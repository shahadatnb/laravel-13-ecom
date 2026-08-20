<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\WishlistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:customer');
    }

    /**
     * List all wishlist items for the authenticated customer.
     */
    public function index(Request $request): JsonResponse
    {
        $items = WishlistItem::with(['product.images', 'variant'])
            ->where('customer_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function (WishlistItem $item) {
                $product = $item->product;
                $variant = $item->variant;

                return [
                    'id' => $item->id,
                    'product_id' => $product->id,
                    'product_slug' => $product->slug,
                    'product_name' => $product->name,
                    'product_image' => $product->images->first()?->image,
                    'regular_price' => $product->regular_price,
                    'sale_price' => $variant?->price ?? $product->sale_price ?? $product->regular_price,
                    'stock' => $variant?->stock ?? $product->stock,
                    'variant_id' => $variant?->id,
                    'variant_name' => $variant?->name,
                    'variant_sku' => $variant?->sku,
                    'created_at' => $item->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    /**
     * Add a product (or product variant) to the wishlist.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'product_variant_id' => [
                'nullable',
                'exists:product_variants,id',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && ! ProductVariant::where('id', $value)
                        ->where('product_id', $request->product_id)->exists()) {
                        $fail('The variant does not belong to the specified product.');
                    }
                },
            ],
        ]);

        $customerId = $request->user()->id;

        // Check if already in wishlist
        $existing = WishlistItem::where('customer_id', $customerId)
            ->where('product_id', $validated['product_id'])
            ->where('product_variant_id', $validated['product_variant_id'] ?? null)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in your wishlist.',
            ], 409);
        }

        $item = WishlistItem::create([
            'customer_id' => $customerId,
            'product_id' => $validated['product_id'],
            'product_variant_id' => $validated['product_variant_id'] ?? null,
        ]);

        $item->load('product.images');

        return response()->json([
            'success' => true,
            'message' => 'Added to wishlist!',
            'data' => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'variant_id' => $item->product_variant_id,
            ],
        ], 201);
    }

    /**
     * Remove an item from the wishlist.
     */
    public function destroy(Request $request, WishlistItem $wishlistItem): JsonResponse
    {
        if ($wishlistItem->customer_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $wishlistItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Removed from wishlist.',
        ]);
    }

    /**
     * Check if specific products are in the user's wishlist.
     * Accepts a JSON body: { product_ids: [1,2,3] }
     * Returns: { 1: true, 2: false, 3: {variant_id: true} }
     */
    public function check(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_ids' => ['required', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
        ]);

        $items = WishlistItem::where('customer_id', $request->user()->id)
            ->whereIn('product_id', $validated['product_ids'])
            ->get()
            ->groupBy('product_id')
            ->map(function ($group) {
                $result = [];
                foreach ($group as $item) {
                    $key = $item->product_variant_id ? 'variant_'.$item->product_variant_id : 'base';
                    $result[$key] = $item->id;
                }

                return $result;
            });

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }
}
