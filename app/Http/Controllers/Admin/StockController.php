<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Warehouse;
use App\Services\InventoryServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockController extends Controller
{
    public function __construct(
        private readonly InventoryServiceInterface $inventoryService,
    ) {}

    /**
     * Display a listing of products with stock information (simple + variant).
     */
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $stockStatus = $request->get('stock_status', '');

        $query = Product::with(['variants' => function ($q) {
            $q->select('id', 'product_id', 'name', 'sku', 'stock', 'attributes');
        }])->withCount(['variants']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($stockStatus === 'has_variants') {
            $query->whereHas('variants');
        } elseif ($stockStatus === 'no_variants') {
            $query->doesntHave('variants');
        } elseif ($stockStatus === 'simple') {
            $query->where('product_type', 'simple');
        } elseif ($stockStatus === 'low_stock') {
            $query->where('product_type', 'simple')
                ->whereColumn('stock', '<=', 'minimum_stock')
                ->where('minimum_stock', '>', 0);
        } elseif ($stockStatus === 'out_of_stock') {
            $query->where('product_type', 'simple')
                ->where('stock', '<=', 0);
        }

        $products = $query->orderBy('name')->paginate(50);

        // Compute total stock for each product
        $products->getCollection()->transform(function ($product) {
            if ($product->product_type === 'simple') {
                $product->total_stock = $product->stock;
            } else {
                $product->total_stock = $product->variants->sum('stock');
            }
            return $product;
        });

        // Summary stats
        $simpleCount = Product::where('product_type', 'simple')->count();
        $simpleTotalStock = Product::where('product_type', 'simple')->sum('stock');
        $lowStockCount = Product::where('product_type', 'simple')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->where('minimum_stock', '>', 0)->count();
        $outOfStockCount = Product::where('product_type', 'simple')->where('stock', '<=', 0)->count();

        return view('admin.stock.index', compact(
            'products', 'search', 'stockStatus',
            'simpleCount', 'simpleTotalStock', 'lowStockCount', 'outOfStockCount'
        ));
    }

    /**
     * Show the stock-in form for a product's variants or simple product stock.
     */
    public function stockInForm(Product $product): View
    {
        $product->load(['variants' => function ($q) {
            $q->orderBy('sort_order');
        }]);

        $warehouses = Warehouse::active()->get();

        return view('admin.stock.stock-in', compact('product', 'warehouses'));
    }

    /**
     * Process stock-in for a product (simple or with variants).
     */
    public function stockIn(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'variant_stocks' => 'nullable|array',
            'variant_stocks.*.variant_id' => 'exists:product_variants,id',
            'variant_stocks.*.quantity' => 'integer|min:1',
            'variant_stocks.*.reason' => 'nullable|string|max:500',
            'variant_stocks.*.unit_cost' => 'nullable|numeric|min:0',
            'variant_stocks.*.reference_number' => 'nullable|string|max:100',
            'quantity' => 'nullable|integer|min:1',
            'reason' => 'nullable|string|max:500',
            'unit_cost' => 'nullable|numeric|min:0',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $warehouseId = $validated['warehouse_id'];

        if (isset($validated['variant_stocks']) && count($validated['variant_stocks']) > 0) {
            foreach ($validated['variant_stocks'] as $variantStock) {
                if (empty($variantStock['variant_id']) || empty($variantStock['quantity'])) {
                    continue;
                }

                $this->inventoryService->stockIn(
                    $product->id,
                    $warehouseId,
                    $variantStock['quantity'],
                    [
                        'product_variant_id' => $variantStock['variant_id'],
                        'reason' => $variantStock['reason'] ?? 'Bulk stock in',
                        'reference_number' => $variantStock['reference_number'] ?? null,
                        'unit_cost' => $variantStock['unit_cost'] ?? null,
                        'user_id' => auth()->id(),
                        'reference_type' => 'manual',
                        'transaction_type' => InventoryTransaction::TYPE_PURCHASE,
                    ]
                );
            }

            return redirect()->route('admin.stock.index')
                ->with('success', 'Stock added successfully for variants.');
        }

        $quantity = $validated['quantity'] ?? 0;
        if ($quantity <= 0) {
            return back()->withErrors(['quantity' => 'Please enter a valid quantity.'])->withInput();
        }

        $this->inventoryService->stockIn(
            $product->id,
            $warehouseId,
            $quantity,
            [
                'reason' => $validated['reason'] ?? 'Stock in',
                'reference_number' => $validated['reference_number'] ?? null,
                'unit_cost' => $validated['unit_cost'] ?? null,
                'user_id' => auth()->id(),
                'reference_type' => 'manual',
                'transaction_type' => InventoryTransaction::TYPE_PURCHASE,
            ]
        );

        Product::where('id', $product->id)->increment('stock', $quantity);

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stock added successfully.');
    }

    /**
     * Show stock details for a specific product and variant.
     */
    public function show(Product $product, ProductVariant $variant): View
    {
        if ($variant->product_id !== $product->id) {
            abort(404);
        }

        $variant->load(['product', 'images']);

        $inventory = DB::table('inventory')
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variant->id)
            ->join('warehouses', 'inventory.warehouse_id', '=', 'warehouses.id')
            ->select('inventory.*', 'warehouses.name as warehouse_name')
            ->get();

        return view('admin.stock.show', compact('product', 'variant', 'inventory'));
    }

    // === Bulk Stock Adjustment ===

    /**
     * Show the bulk stock adjustment form for simple products.
     */
    public function bulkAdjustForm(Request $request): View
    {
        $search = $request->get('search', '');

        $query = Product::where('product_type', 'simple')
            ->whereNull('deleted_at')
            ->select('id', 'name', 'sku', 'stock', 'minimum_stock');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->paginate(30);
        $warehouses = Warehouse::active()->get();

        return view('admin.stock.bulk-adjust', compact('products', 'search', 'warehouses'));
    }

    /**
     * Process bulk stock adjustment for simple products.
     */
    public function bulkAdjust(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'mode' => 'required|in:set,add,subtract',
            'reason' => 'nullable|string|max:500',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:0',
        ]);

        $warehouseId = $validated['warehouse_id'];
        $mode = $validated['mode'];
        $reason = $validated['reason'] ?? 'Bulk stock adjustment';
        $productIds = $validated['product_ids'];
        $quantities = $validated['quantities'];
        $updated = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($productIds as $productId) {
                $qty = $quantities[$productId] ?? null;
                if ($qty === null || $qty === '' || $qty < 0) {
                    continue;
                }
                $qty = (int) $qty;

                $product = Product::find($productId);
                if (!$product || $product->product_type !== 'simple') {
                    $errors[] = "Product #{$productId}: Not a simple product — skipped.";
                    continue;
                }

                $currentStock = (int) $product->stock;
                $newStock = match ($mode) {
                    'set' => $qty,
                    'add' => $currentStock + $qty,
                    'subtract' => max(0, $currentStock - $qty),
                    default => $currentStock,
                };

                // Update product stock
                $product->update(['stock' => $newStock]);

                // Update inventory record
                $this->inventoryService->adjustStock(
                    $productId,
                    $warehouseId,
                    $newStock,
                    $reason,
                    [
                        'user_id' => auth()->id(),
                        'description' => "Bulk {$mode}: {$currentStock} → {$newStock}",
                    ]
                );

                $updated++;
            }

            DB::commit();

            $summary = "Bulk adjustment complete: {$updated} products updated ({$mode}).";
            if (!empty($errors)) {
                $summary .= ' Errors: ' . implode(' | ', array_slice($errors, 0, 5));
            }

            return redirect()->route('admin.stock.index')
                ->with('success', $summary);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Bulk adjustment failed: ' . $e->getMessage()])->withInput();
        }
    }
}
