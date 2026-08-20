<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Order\UpdateOrderRequest;
use App\Models\Order;
use App\Services\OrderServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderServiceInterface $orderService,
    ) {}

    public function index(Request $request): View
    {
        $status = $request->get('status');
        $filters = $request->only(['status', 'payment_status', 'search', 'date_from', 'date_to']);

        if ($status) {
            $filters['status'] = $status;
        }

        $orders = ! empty($filters)
            ? $this->orderService->searchOrders($filters)
            : $this->orderService->listOrders(15);

        $stats = $this->orderService->getOrderStatistics();

        return view('admin.orders.index', compact('orders', 'stats', 'status'));
    }

    public function show(Order $order): View
    {
        $order->load(['customer', 'items.product.images', 'statusHistories']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->orderService->changeStatus(
            $order,
            $validated['status'],
            $validated['notes'] ?? null
        );

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order status updated successfully.');
    }

    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        $data = $request->validated();
        $this->orderService->updateOrder($order, $data);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    public function updatePayment(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'payment_status' => ['required', 'string'],
        ]);

        $this->orderService->updatePaymentStatus($order, $validated['payment_status']);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Payment status updated successfully.');
    }

    public function updateShipping(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'shipping_status' => ['required', 'string'],
        ]);

        $this->orderService->updateShippingStatus($order, $validated['shipping_status']);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Shipping status updated successfully.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $this->orderService->deleteOrder($order);

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully.');
    }
}
