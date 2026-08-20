<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Product\CreateProductAction;
use App\Actions\Product\DeleteProductAction;
use App\Actions\Product\UpdateProductAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService,
        private CreateProductAction $createAction,
        private UpdateProductAction $updateAction,
        private DeleteProductAction $deleteAction
    ) {}

    /**
     * Display a listing of products.
     */
    public function index(): View
    {
        $stockStatus = request()->get('stock_status', '');

        $products = match ($stockStatus) {
            'in' => $this->productService->getInStockPaginated(50),
            'low' => $this->productService->getLowStockPaginated(50),
            'out' => $this->productService->getOutOfStockPaginated(50),
            default => $this->productService->listPaginated(50),
        };

        return view('admin.product.index', compact('products', 'stockStatus'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $categoryTree = $this->buildCategoryTree($categories);
        $attributes = ProductAttribute::with('values')->orderBy('sort_order')->get();

        return view('admin.product.create', compact('brands', 'categories', 'categoryTree', 'attributes'));
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request): RedirectResponse|JsonResponse
    {
        // $this->authorizeResource(Product::class, Product::class);

        $data = $request->validated();

        // Merge variant data which has no validation rules
        $data['variants'] = $request->input('variants', []);
        $data['variant_images'] = $request->input('variant_images', []);
        $data['variant_existing_images'] = $request->input('variant_existing_images', []);

        $this->createAction->execute($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'redirect' => route('admin.product.index'),
            ]);
        }

        return redirect()->route('admin.product.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        $product->load(['variants.images', 'brand', 'category']);

        return view('admin.product.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        // $this->authorize('update', $product);

        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $categoryTree = $this->buildCategoryTree($categories);
        $attributes = ProductAttribute::with('values')->orderBy('sort_order')->get();
        $product->load(['variants.images', 'categories']);

        return view('admin.product.edit', compact('product', 'brands', 'categories', 'categoryTree', 'attributes'));
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse|JsonResponse
    {
        // $this->authorize('update', $product);

        $data = $request->validated();

        // Merge variant data which has no validation rules
        $data['variants'] = $request->input('variants', []);
        $data['variant_images'] = $request->input('variant_images', []);
        $data['variant_existing_images'] = $request->input('variant_existing_images', []);
        $data['deleted_images'] = $request->input('deleted_images', []);

        $this->updateAction->execute($product, $data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'redirect' => route('admin.product.index'),
            ]);
        }

        return redirect()->route('admin.product.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // $this->authorize('delete', $product);

        $this->deleteAction->execute($product);

        return redirect()->route('admin.product.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Build a hierarchical category tree for the form.
     *
     * @return array<int, array{id: int, name: string, depth: int, parent_id: int|null}>
     */
    private function buildCategoryTree(Collection $categories): array
    {
        $tree = [];
        $grouped = $categories->groupBy('parent_id');

        $this->addChildren($tree, $grouped, null, 0);

        return $tree;
    }

    /**
     * Recursively add child categories to the tree.
     */
    private function addChildren(array &$tree, Collection $grouped, ?int $parentId, int $depth): void
    {
        $children = $grouped->get($parentId);

        if (! $children) {
            return;
        }

        foreach ($children as $child) {
            $tree[] = [
                'id' => $child->id,
                'name' => $child->name,
                'depth' => $depth,
                'parent_id' => $child->parent_id,
            ];

            $this->addChildren($tree, $grouped, $child->id, $depth + 1);
        }
    }

    /**
     * Upload thumbnail image via AJAX (immediate upload on edit page).
     */
    public function uploadThumbnail(Product $product): JsonResponse
    {
        $request = request();
        $request->validate([
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $file = $request->file('thumbnail');
        $filename = 'thumbnail_'.$product->id.'_'.time().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('products', $filename, 'public');

        // Delete old thumbnail if exists
        if ($product->thumbnail && Storage::disk('public')->exists($product->thumbnail)) {
            Storage::disk('public')->delete($product->thumbnail);
        }

        $product->update(['thumbnail' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Thumbnail uploaded successfully.',
        ]);
    }

    /**
     * Remove thumbnail image via AJAX.
     */
    public function removeThumbnail(Product $product): JsonResponse
    {
        if ($product->thumbnail && Storage::disk('public')->exists($product->thumbnail)) {
            Storage::disk('public')->delete($product->thumbnail);
        }

        $product->update(['thumbnail' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Thumbnail removed successfully.',
        ]);
    }

    /**
     * Upload gallery images via AJAX (immediate upload on edit page).
     */
    public function uploadGallery(Product $product): JsonResponse
    {
        $request = request();
        $request->validate([
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $files = $request->file('images');
        $uploaded = [];

        foreach ($files as $file) {
            $filename = 'gallery_'.$product->id.'_'.time().'_'.rand(1000, 9999).'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('products', $filename, 'public');

            $image = ProductImage::create([
                'product_id' => $product->id,
                'image' => $path,
                'sort_order' => $product->images()->count() + 1,
            ]);

            $uploaded[] = $image;
        }

        return response()->json([
            'success' => true,
            'message' => 'Image(s) uploaded successfully.',
            'images' => $uploaded,
        ]);
    }

    /**
     * Delete gallery image via AJAX.
     */
    public function deleteGallery(Product $product): JsonResponse
    {
        $request = request();
        $request->validate([
            'image_id' => 'required|integer|exists:product_galleries,id',
        ]);

        $image = ProductImage::where('id', $request->input('image_id'))
            ->where('product_id', $product->id)
            ->first();

        if ($image) {
            if (Storage::disk('public')->exists($image->image)) {
                Storage::disk('public')->delete($image->image);
            }
            $image->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully.',
        ]);
    }

    /**
     * Upload variant image via AJAX (immediate upload on edit page).
     */
    public function uploadVariantImage(Product $product): JsonResponse
    {
        $request = request();
        $request->validate([
            'variant_id' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $file = $request->file('image');
        $filename = 'variant_'.$request->input('variant_id').'_'.time().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('product-variants', $filename, 'public');

        // Find or create variant image record using ProductImage model
        // (variant images are stored in product_galleries table with product_variant_id)
        $variantImage = ProductImage::firstOrCreate(
            [
                'product_id' => $product->id,
                'product_variant_id' => $request->input('variant_id'),
            ],
            [
                'image' => $path,
            ]
        );

        // Update if exists
        $variantImage->update(['image' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Variant image uploaded successfully.',
            'image_url' => asset('storage/'.$path),
            'variant_id' => $request->input('variant_id'),
        ]);
    }

    /**
     * Delete variant image via AJAX.
     */
    public function deleteVariantImage(Product $product): JsonResponse
    {
        $request = request();
        $request->validate([
            'variant_id' => 'required|integer',
        ]);

        $variantImage = ProductImage::where('product_id', $product->id)
            ->where('product_variant_id', $request->input('variant_id'))
            ->first();

        if ($variantImage) {
            if (Storage::disk('public')->exists($variantImage->image)) {
                Storage::disk('public')->delete($variantImage->image);
            }
            $variantImage->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Variant image deleted successfully.',
        ]);
    }

    /**
     * Generate and save variants via AJAX (edit page).
     * Compares with existing variants and only adds new ones.
     * Duplicate check is order-independent (handles JSON key ordering differences).
     */
    public function generateVariants(Product $product): JsonResponse
    {
        $request = request();
        $request->validate([
            'variants' => 'required|array',
            'variants.*.attributes' => 'required|json',
        ]);

        $variantsData = $request->input('variants');
        $addedCount = 0;
        $skippedCount = 0;
        $newVariantIds = [];

        // Load all existing variants for this product once (for order-independent comparison)
        $existingVariants = DB::table('product_variants')
            ->where('product_id', $product->id)
            ->get(['id', 'attributes']);

        foreach ($variantsData as $variant) {
            $attributes = is_string($variant['attributes'])
                ? json_decode($variant['attributes'], true)
                : $variant['attributes'];

            // Check if variant with same attributes already exists.
            // Use array comparison (==) to handle different JSON key ordering.
            $existingVariant = $existingVariants->first(function ($v) use ($attributes) {
                $stored = json_decode($v->attributes, true);

                return is_array($stored) && is_array($attributes) && $stored == $attributes;
            });

            if ($existingVariant) {
                $skippedCount++;
                $newVariantIds[] = $existingVariant->id;

                continue;
            }

            // Create new variant — autofill SKU and price from the product's values
            $variantSku = $variant['sku'] ?? $product->sku;
            $variantPrice = $variant['price'] ?? ($product->sale_price ?? $product->regular_price ?? 0);
            $variantData = [
                'product_id' => $product->id,
                'name' => $variant['name'] ?? null,
                'sku' => $variantSku ?? null,
                'barcode' => $variant['barcode'] ?? null,
                'price' => $variantPrice ?? 0,
                'stock' => $variant['stock'] ?? 0,
                'attributes' => json_encode($attributes),
                'sort_order' => DB::table('product_variants')
                    ->where('product_id', $product->id)
                    ->max('sort_order') + 1,
            ];

            $variantId = DB::table('product_variants')->insertGetId($variantData);
            $newVariantIds[] = $variantId;
            $addedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Added {$addedCount} new variant(s), skipped {$skippedCount} existing.",
            'variant_ids' => $newVariantIds,
            'added_count' => $addedCount,
            'skipped_count' => $skippedCount,
        ]);
    }

    /**
     * Get all variants for a product via AJAX.
     */
    public function getVariants(Product $product): JsonResponse
    {
        $variants = $product->variants()
            ->with('images')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $variants,
        ]);
    }

    /**
     * Delete a single variant via AJAX (edit page).
     */
    public function deleteVariant(Product $product): JsonResponse
    {
        $request = request();
        $request->validate([
            'variant_id' => 'required|integer|exists:product_variants,id',
        ]);

        $variant = ProductVariant::where('id', $request->input('variant_id'))
            ->where('product_id', $product->id)
            ->firstOrFail();

        $variant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Variant removed successfully.',
        ]);
    }

    /**
     * Update a single variant field (sku, price, stock) via AJAX (edit page).
     */
    public function updateVariantField(Product $product): JsonResponse
    {
        $request = request();
        $request->validate([
            'variant_id' => 'required|integer|exists:product_variants,id',
            'field' => 'required|string|in:sku,price',
            'value' => 'nullable',
        ]);

        $variant = ProductVariant::where('id', $request->input('variant_id'))
            ->where('product_id', $product->id)
            ->firstOrFail();

        $variant->update([
            $request->input('field') => $request->input('value'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Variant updated successfully.',
        ]);
    }
}
