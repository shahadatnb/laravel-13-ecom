<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Wallet summary plus paginated transaction history
     * for the authenticated customer.
     */
    public function index(Request $request): JsonResponse
    {
        $customer = $request->user();

        $wallet = Wallet::firstOrCreate(
            ['customer_id' => $customer->id],
            ['balance' => 0, 'locked_balance' => 0, 'status' => Wallet::STATUS_ACTIVE],
        );

        $perPage = min(max((int) $request->integer('per_page', 15), 1), 50);

        $transactions = $wallet->transactions()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Wallet retrieved successfully',
            'data' => [
                'id' => $wallet->id,
                'status' => $wallet->status,
                'balance' => (float) $wallet->balance,
                'locked_balance' => (float) $wallet->locked_balance,
                'available_balance' => (float) $wallet->available_balance,
                'total_credits' => (float) $wallet->transactions()->credit()->sum('amount'),
                'total_debits' => (float) $wallet->transactions()->debit()->sum('amount'),
                'transactions' => $transactions,
            ],
        ]);
    }
}
