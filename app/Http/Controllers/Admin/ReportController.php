<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * DFD 6.0: Buat Laporan Penjualan
     */
    public function index(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', Carbon::today()->format('Y-m-d'));

        $startCarbon = Carbon::parse($startDate)->startOfDay();
        $endCarbon = Carbon::parse($endDate)->endOfDay();

        // Base Query for Paid Orders within date range
        $ordersQuery = Order::with(['table', 'items.menu', 'payment.receipt', 'payment.confirmedByStaff'])
            ->whereBetween('ordered_at', [$startCarbon, $endCarbon])
            ->whereHas('payment', function ($q) {
                $q->where('status', 'PAID');
            });

        $orders = (clone $ordersQuery)->orderBy('ordered_at', 'desc')->get();

        $totalRevenue = $orders->sum('total_amount');
        $totalOrdersCount = $orders->count();
        $averageOrderValue = $totalOrdersCount > 0 ? $totalRevenue / $totalOrdersCount : 0;

        // Payment Method breakdown
        $cashTotal = (clone $ordersQuery)->whereHas('payment', fn($q) => $q->where('method', 'CASH'))->sum('total_amount');
        $qrisTotal = (clone $ordersQuery)->whereHas('payment', fn($q) => $q->where('method', 'QRIS'))->sum('total_amount');
        $transferTotal = (clone $ordersQuery)->whereHas('payment', fn($q) => $q->where('method', 'TRANSFER'))->sum('total_amount');

        // Order Type breakdown
        $dineInCount = (clone $ordersQuery)->where('order_type', 'DINE_IN')->count();
        $takeAwayCount = (clone $ordersQuery)->where('order_type', 'TAKE_AWAY')->count();

        // Top Selling Menu Items
        $topItems = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->join('menus', 'order_items.menu_id', '=', 'menus.menu_id')
            ->join('categories', 'menus.category_id', '=', 'categories.category_id')
            ->join('payments', 'orders.order_id', '=', 'payments.order_id')
            ->where('payments.status', 'PAID')
            ->whereBetween('orders.ordered_at', [$startCarbon, $endCarbon])
            ->select(
                'menus.name',
                'categories.name as category_name',
                'menus.price',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('menus.menu_id', 'menus.name', 'categories.name', 'menus.price')
            ->orderByDesc('total_quantity')
            ->take(10)
            ->get();

        $totalItemsSold = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->join('payments', 'orders.order_id', '=', 'payments.order_id')
            ->where('payments.status', 'PAID')
            ->whereBetween('orders.ordered_at', [$startCarbon, $endCarbon])
            ->sum('order_items.quantity');

        return view('admin.reports.index', compact(
            'orders',
            'startDate',
            'endDate',
            'totalRevenue',
            'totalOrdersCount',
            'averageOrderValue',
            'totalItemsSold',
            'cashTotal',
            'qrisTotal',
            'transferTotal',
            'dineInCount',
            'takeAwayCount',
            'topItems'
        ));
    }

    /**
     * Printable Sales Report View
     */
    public function print(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', Carbon::today()->format('Y-m-d'));

        $startCarbon = Carbon::parse($startDate)->startOfDay();
        $endCarbon = Carbon::parse($endDate)->endOfDay();

        $orders = Order::with(['table', 'items.menu', 'payment.receipt', 'payment.confirmedByStaff'])
            ->whereBetween('ordered_at', [$startCarbon, $endCarbon])
            ->whereHas('payment', fn($q) => $q->where('status', 'PAID'))
            ->orderBy('ordered_at', 'asc')
            ->get();

        $totalRevenue = $orders->sum('total_amount');
        $totalOrdersCount = $orders->count();

        $cashTotal = $orders->filter(fn($o) => $o->payment?->method === 'CASH')->sum('total_amount');
        $qrisTotal = $orders->filter(fn($o) => $o->payment?->method === 'QRIS')->sum('total_amount');
        $transferTotal = $orders->filter(fn($o) => $o->payment?->method === 'TRANSFER')->sum('total_amount');

        return view('admin.reports.print', compact('orders', 'startDate', 'endDate', 'totalRevenue', 'totalOrdersCount', 'cashTotal', 'qrisTotal', 'transferTotal'));
    }
}

