<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\UserAddress;
use App\Models\Wallet;
use App\Models\WishlistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Summary statistics for the authenticated customer's dashboard.
     */
    public function stats(Request $request): JsonResponse
    {
        $customerId = $request->user()->id;

        $wallet = Wallet::where('customer_id', $customerId)->first();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard stats retrieved successfully',
            'data' => [
                'total_orders' => Order::where('customer_id', $customerId)->count(),
                'wallet_balance' => (float) ($wallet?->balance ?? 0),
                'total_addresses' => UserAddress::where('customer_id', $customerId)->count(),
                'total_wishlist' => WishlistItem::where('customer_id', $customerId)->count(),
            ],
        ]);
    }
}
