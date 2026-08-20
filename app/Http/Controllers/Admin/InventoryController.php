<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Services\InventoryServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function __construct(
        private readonly InventoryServiceInterface $inventoryService,
    ) {}

    // === Dashboard ===

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'warehouse_id', 'stock_status']);

        $inventory = $this->inventoryService->searchInventory($filters);
        $stats = $this->inventoryService->getInventoryStatistics();
        $warehouses = Warehouse::active()->get();

        return view('admin.inventory.index', compact('inventory', 'stats', 'warehouses'));
    }

    // === Warehouses ===

    public function warehouses(Request $request): View
    {
        $warehouses = $this->inventoryService->listWarehouses(
            $request->get('per_page', 15)
        );

        return view('admin.inventory.warehouses', compact('warehouses'));
    }

    public function createWarehouse(): View
    {
        return view('admin.inventory.warehouse-create');
    }

    public function storeWarehouse(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:warehouses,code',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'manager_name' => 'nullable|string|max:100',
            'is_default' => 'boolean',
            'status' => 'required|string|in:active,inactive',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['created_by'] = auth()->id();
        $this->inventoryService->createWarehouse($validated);

        return redirect()->route('admin.inventory.warehouses')
            ->with('success', 'Warehouse created successfully.');
    }

    public function editWarehouse(Warehouse $warehouse): View
    {
        return view('admin.inventory.warehouse-edit', compact('warehouse'));
    }

    public function updateWarehouse(Request $request, Warehouse $warehouse): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:warehouses,code,'.$warehouse->id,
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'manager_name' => 'nullable|string|max:100',
            'is_default' => 'boolean',
            'status' => 'required|string|in:active,inactive',
            'notes' => 'nullable|string|max:1000',
        ]);

        $this->inventoryService->updateWarehouse($warehouse, $validated);

        return redirect()->route('admin.inventory.warehouses')
            ->with('success', 'Warehouse updated successfully.');
    }

    public function destroyWarehouse(Warehouse $warehouse): RedirectResponse
    {
        $this->inventoryService->deleteWarehouse($warehouse);

        return redirect()->route('admin.inventory.warehouses')
            ->with('success', 'Warehouse deleted successfully.');
    }

    // === Stock Operations ===

    public function show(Inventory $inventory): View
    {
        $inventory->load(['product', 'warehouse', 'variant', 'transactions' => function ($q) {
            $q->latest()->limit(50);
        }]);

        return view('admin.inventory.show', compact('inventory'));
    }

    public function adjustForm(Inventory $inventory): View
    {
        return view('admin.inventory.adjust', compact('inventory'));
    }

    public function adjust(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'new_quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'product_variant_id' => 'nullable|exists:product_variants,id',
        ]);

        try {
            $this->inventoryService->adjustStock(
                $validated['product_id'],
                $validated['warehouse_id'],
                $validated['new_quantity'],
                $validated['reason'],
                [
                    'description' => $validated['description'] ?? null,
                    'product_variant_id' => $validated['product_variant_id'] ?? null,
                    'user_id' => auth()->id(),
                ]
            );

            return redirect()->route('admin.inventory.index')
                ->with('success', 'Stock adjusted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function stockInForm(): View
    {
        $warehouses = Warehouse::active()->get();
        $products = Product::with('variants')->select('id', 'name', 'sku')->get();
        $productsWithVariants = $products->map(fn ($p) => [
            'id' => $p->id,
            'variants' => $p->variants->map(fn ($v) => [
                'id' => $v->id,
                'name' => $v->name,
                'sku' => $v->sku,
            ]),
        ]);

        return view('admin.inventory.stock-in', compact('warehouses', 'products', 'productsWithVariants'));
    }

    public function stockIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:500',
            'reference_number' => 'nullable|string|max:100',
            'unit_cost' => 'nullable|numeric|min:0',
        ]);

        try {
            $this->inventoryService->stockIn(
                $validated['product_id'],
                $validated['warehouse_id'],
                $validated['quantity'],
                [
                    'product_variant_id' => $validated['product_variant_id'] ?? null,
                    'reason' => $validated['reason'] ?? 'Manual stock in',
                    'reference_number' => $validated['reference_number'] ?? null,
                    'unit_cost' => $validated['unit_cost'] ?? null,
                    'user_id' => auth()->id(),
                    'reference_type' => 'manual',
                    'transaction_type' => InventoryTransaction::TYPE_ADJUSTMENT,
                ]
            );

            return redirect()->route('admin.inventory.index')
                ->with('success', 'Stock added successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    // === Transfers ===

    public function transfers(Request $request): View
    {
        $filters = $request->only(['status', 'from_warehouse_id', 'to_warehouse_id']);

        $transfers = $this->inventoryService->searchTransfers($filters);
        $warehouses = Warehouse::active()->get();

        return view('admin.inventory.transfers', compact('transfers', 'warehouses'));
    }

    public function createTransfer(Request $request): View
    {
        $warehouses = Warehouse::active()->get();

        return view('admin.inventory.transfer-create', compact('warehouses'));
    }

    public function storeTransfer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id|different:to_warehouse_id',
            'to_warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->inventoryService->transferStock(
                $validated['from_warehouse_id'],
                $validated['to_warehouse_id'],
                $validated['product_id'],
                $validated['quantity'],
                ['notes' => $validated['notes'] ?? null]
            );

            return redirect()->route('admin.inventory.transfers')
                ->with('success', 'Stock transfer initiated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function completeTransfer(StockTransfer $transfer): RedirectResponse
    {
        try {
            $this->inventoryService->completeTransfer($transfer);

            return redirect()->route('admin.inventory.transfers')
                ->with('success', 'Transfer completed successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function cancelTransfer(StockTransfer $transfer): RedirectResponse
    {
        try {
            $this->inventoryService->cancelTransfer($transfer);

            return redirect()->route('admin.inventory.transfers')
                ->with('success', 'Transfer cancelled successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // === Ledger ===

    public function ledger(Request $request): View
    {
        $filters = $request->only(['type', 'warehouse_id', 'product_id', 'date_from', 'date_to']);

        $transactions = $this->inventoryService->searchTransactions($filters);
        $warehouses = Warehouse::active()->get();

        return view('admin.inventory.ledger', compact('transactions', 'warehouses'));
    }
}
