<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Menu;
use App\Models\Staff;
use App\Models\CafeTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalRevenue = Payment::where('status', 'PAID')->sum('amount_paid');
        $todayRevenue = Payment::where('status', 'PAID')->whereDate('paid_at', $today)->sum('amount_paid');
        
        $totalOrders = Order::count();
        $todayOrders = Order::whereDate('ordered_at', $today)->count();

        $activeOrders = Order::whereIn('status', ['PENDING', 'ACCEPTED', 'PREPARING', 'READY'])->count();
        $completedOrders = Order::where('status', 'COMPLETED')->count();

        $totalStaff = Staff::count();
        $totalMenus = Menu::count();
        
        $totalTables = CafeTable::count();
        $occupiedTables = CafeTable::where('status', 'OCCUPIED')->count();

        $recentOrders = Order::with(['table', 'payment', 'items.menu'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Top 5 popular menus
        $topMenus = DB::table('order_items')
            ->join('menus', 'order_items.menu_id', '=', 'menus.menu_id')
            ->select('menus.name', 'menus.price', DB::raw('SUM(order_items.quantity) as total_sold'), DB::raw('SUM(order_items.subtotal) as total_sales'))
            ->groupBy('menus.menu_id', 'menus.name', 'menus.price')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'todayRevenue',
            'totalOrders',
            'todayOrders',
            'activeOrders',
            'completedOrders',
            'totalStaff',
            'totalMenus',
            'totalTables',
            'occupiedTables',
            'recentOrders',
            'topMenus'
        ));
    }
}

