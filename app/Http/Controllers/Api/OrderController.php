<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use App\Services\OrderServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderServiceInterface $orderService,
    ) {}

    /**
     * Public order tracking — no auth required.
     * Look up an order by email + order_number.
     */
    public function trackByOrderNumber(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'order_number' => 'required|string|max:30',
        ]);

        // Try guest lookup first (guest_email matches)
        $order = Order::where('order_number', $request->input('order_number'))
            ->where('guest_email', $request->input('email'))
            ->first();

        // If not found as guest, try registered customer lookup
        if (! $order) {
            $customer = Customer::where('email', $request->input('email'))->first();
            if ($customer) {
                $order = Order::where('order_number', $request->input('order_number'))
                    ->where('customer_id', $customer->id)
                    ->first();
            }
        }

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'No order found with that email and order number combination.',
            ], 404);
        }

        $order->load(['items.product', 'statusHistories']);

        return response()->json([
            'success' => true,
            'message' => 'Order found successfully',
            'data' => [
                'order' => new OrderResource($order),
                'timeline' => $order->statusHistories->map(function ($history) {
                    return [
                        'status' => $history->to_status,
                        'notes' => $history->notes,
                        'date' => $history->created_at?->toIso8601String(),
                    ];
                }),
            ],
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $orders = $this->orderService->getCustomerOrders(
            $request->user()->id,
            $request->get('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'data' => OrderResource::collection($orders),
        ]);
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        $order->load(['items.product', 'statusHistories']);

        return response()->json([
            'success' => true,
            'message' => 'Order retrieved successfully',
            'data' => new OrderResource($order),
        ]);
    }

    public function store(OrderRequest $request): JsonResponse
    {
        $data = [
            'customer_id' => $request->user()?->id,
            'guest_email' => $request->input('guest_email'),
            'items' => $request->input('items'),
            'coupon_code' => $request->input('coupon_code'),
            'discount' => $request->input('discount', 0),
            'tax' => $request->input('tax', 0),
            'shipping_charge' => $request->input('shipping_charge', 0),
            'currency' => $request->input('currency', 'BDT'),
            'payment_method' => $request->input('payment_method'),
            'shipping_address' => $request->input('shipping_address'),
            'billing_address' => $request->input('billing_address'),
            'notes' => $request->input('notes'),
            'referrer_code' => $request->input('referrer_code'),
            'referrer_at' => $request->input('referrer_at'),
        ];

        // For guest checkout, we store the guest email
        // For authenticated users, we use their registered email
        if ($request->user()) {
            $data['guest_email'] = null;
        } elseif (! $data['guest_email']) {
            return response()->json([
                'success' => false,
                'message' => 'Guest email is required for guest checkout.',
            ], 422);
        }

        $order = $this->orderService->createOrder($data);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => new OrderResource($order),
        ], 201);
    }

    public function cancel(Order $order): JsonResponse
    {
        $this->authorize('cancel', $order);

        if ($order->isCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Order is already cancelled.',
            ], 409);
        }

        $order = $this->orderService->changeStatus(
            $order,
            Order::STATUS_CANCELLED,
            'Cancelled by customer'
        );

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => new OrderResource($order),
        ]);
    }

    public function tracking(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        $order->load('statusHistories');

        return response()->json([
            'success' => true,
            'message' => 'Order tracking retrieved successfully',
            'data' => [
                'order' => new OrderResource($order),
                'timeline' => $order->statusHistories->map(function ($history) {
                    return [
                        'status' => $history->to_status,
                        'notes' => $history->notes,
                        'date' => $history->created_at?->toIso8601String(),
                    ];
                }),
            ],
        ]);
    }

    public function allOrders(Request $request): JsonResponse
    {
        $orders = $this->orderService->listOrders(
            $request->get('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'data' => OrderResource::collection($orders),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $orders = $this->orderService->searchOrders($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Orders searched successfully',
            'data' => OrderResource::collection($orders),
        ]);
    }

    public function statistics(): JsonResponse
    {
        $statistics = $this->orderService->getOrderStatistics();

        return response()->json([
            'success' => true,
            'message' => 'Statistics retrieved successfully',
            'data' => $statistics,
        ]);
    }
}
