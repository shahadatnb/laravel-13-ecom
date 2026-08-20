<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // ── Basic Stats ──
        $stats = [
            'products'    => Product::count(),
            'orders'      => Order::count(),
            'customers'   => Customer::count(),
            'revenue'     => (float) Order::where('payment_status', Order::PAYMENT_PAID)->sum('grand_total'),
        ];

        // ── Revenue: last 12 months ──
        $revenueByMonth = Order::where('payment_status', Order::PAYMENT_PAID)
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('SUM(grand_total) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $revenueMonths = [];
        $revenueValues = [];
        for ($i = 11; $i >= 0; $i--) {
            $key = Carbon::now()->subMonths($i)->format('Y-m');
            $label = Carbon::now()->subMonths($i)->format('M Y');
            $revenueMonths[] = $label;
            $revenueValues[] = round($revenueByMonth[$key] ?? 0);
        }

        // ── Orders: last 12 months ──
        $ordersByMonth = Order::where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $orderMonths = [];
        $orderValues = [];
        for ($i = 11; $i >= 0; $i--) {
            $key = Carbon::now()->subMonths($i)->format('Y-m');
            $orderMonths[] = Carbon::now()->subMonths($i)->format('M Y');
            $orderValues[] = (int) ($ordersByMonth[$key] ?? 0);
        }

        // ── Order Status Distribution ──
        $orderStatusCounts = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── Payment Status Distribution ──
        $paymentStatusCounts = Order::select('payment_status', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_status')
            ->pluck('count', 'payment_status')
            ->toArray();

        // ── Top Selling Products (by order count) ──
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // ── Recent Orders ──
        $recentOrders = Order::with('customer')
            ->latest()
            ->limit(10)
            ->get();

        // ── Low Stock Products ──
        $lowStockProducts = Product::whereColumn('stock', '<=', 'minimum_stock')
            ->where('minimum_stock', '>', 0)
            ->orderBy('stock')
            ->limit(5)
            ->get();

        // ── Today vs Yesterday ──
        $today = Carbon::today();
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $todayRevenue = (float) Order::whereDate('created_at', $today)
            ->where('payment_status', Order::PAYMENT_PAID)->sum('grand_total');
        $yesterdayOrders = Order::whereDate('created_at', Carbon::yesterday())->count();
        $yesterdayRevenue = (float) Order::whereDate('created_at', Carbon::yesterday())
            ->where('payment_status', Order::PAYMENT_PAID)->sum('grand_total');

        // ── Pending Counts ──
        $pendingOrders = Order::where('status', Order::STATUS_PENDING)->count();
        $processingOrders = Order::where('status', Order::STATUS_PROCESSING)->count();

        return view('admin.dashboard', compact(
            'stats',
            'revenueMonths', 'revenueValues',
            'orderMonths', 'orderValues',
            'orderStatusCounts', 'paymentStatusCounts',
            'topProducts', 'recentOrders', 'lowStockProducts',
            'todayOrders', 'todayRevenue',
            'yesterdayOrders', 'yesterdayRevenue',
            'pendingOrders', 'processingOrders',
        ));
    }
}
