<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InventoryResource;
use App\Http\Resources\InventoryTransactionResource;
use App\Http\Resources\WarehouseResource;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Services\InventoryServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(
        private readonly InventoryServiceInterface $inventoryService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'warehouse_id', 'stock_status', 'per_page']);

        $inventory = $this->inventoryService->searchInventory($filters);

        return response()->json([
            'data' => InventoryResource::collection($inventory),
            'meta' => [
                'current_page' => $inventory->currentPage(),
                'last_page' => $inventory->lastPage(),
                'total' => $inventory->total(),
            ],
        ]);
    }

    public function show(Inventory $inventory): JsonResponse
    {
        $inventory->load(['product', 'warehouse', 'variant', 'transactions' => function ($q) {
            $q->latest()->limit(20);
        }]);

        return response()->json([
            'data' => new InventoryResource($inventory),
        ]);
    }

    public function warehouses(): JsonResponse
    {
        $warehouses = Warehouse::active()->get();

        return response()->json([
            'data' => WarehouseResource::collection($warehouses),
        ]);
    }

    public function lowStock(Request $request): JsonResponse
    {
        $items = $this->inventoryService->getLowStockItems(
            $request->get('per_page', 15)
        );

        return response()->json([
            'data' => InventoryResource::collection($items),
        ]);
    }

    public function statistics(): JsonResponse
    {
        return response()->json([
            'data' => $this->inventoryService->getInventoryStatistics(),
        ]);
    }

    public function ledger(Request $request): JsonResponse
    {
        $productId = $request->get('product_id');
        $filters = $request->only(['type', 'warehouse_id', 'product_id', 'date_from', 'date_to']);

        $transactions = $this->inventoryService->searchTransactions($filters);

        return response()->json([
            'data' => InventoryTransactionResource::collection($transactions),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'total' => $transactions->total(),
            ],
        ]);
    }

    public function adjust(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'new_quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:100',
        ]);

        try {
            $inventory = $this->inventoryService->adjustStock(
                $validated['product_id'],
                $validated['warehouse_id'],
                $validated['new_quantity'],
                $validated['reason'],
                ['user_id' => $request->user()?->id]
            );

            return response()->json([
                'message' => 'Stock adjusted successfully.',
                'data' => new InventoryResource($inventory),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
