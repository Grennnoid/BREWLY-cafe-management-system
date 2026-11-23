<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Menu;
use App\Models\CafeTable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Payment;
use App\Models\CartItem;
use App\Services\CartService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * DFD 1.0: Kelola & Lihat Menu (Pelanggan)
     */
    public function index(Request $request)
    {
        $categories = Category::withCount('menus')->get();
        $selectedCategory = $request->query('category');
        $search = $request->query('search');

        $query = Menu::query()->with('category');

        if ($selectedCategory) {
            $query->where('category_id', $selectedCategory);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $menus = $query->orderBy('is_available', 'desc')->get();
        $cart = CartService::getCart();
        $tables = CafeTable::all();
        $selectedTable = $request->query('table');

        return view('customer.menu', compact('categories', 'menus', 'cart', 'tables', 'selectedCategory', 'search', 'selectedTable'));
    }

    /**
     * DFD 2.0: Kelola Keranjang
     */
    public function cart()
    {
        $cart = CartService::getCart()->load('items.menu');
        $tables = CafeTable::where('status', 'AVAILABLE')->get();

        return view('customer.cart', compact('cart', 'tables'));
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,menu_id',
            'quantity' => 'nullable|integer|min:1',
            'note' => 'nullable|string|max:255',
        ]);

        $quantity = $validated['quantity'] ?? 1;
        $note = $validated['note'] ?? null;

        $menu = Menu::findOrFail($validated['menu_id']);
        if (!$menu->is_available) {
            return back()->with('error', 'Maaf, menu sedang tidak tersedia.');
        }

        CartService::addItem($menu->menu_id, $quantity, $note);

        return back()->with('success', "{$menu->name} berhasil ditambahkan ke keranjang!");
    }

    public function updateCart(Request $request, $id)
    {
        $cartItem = CartItem::findOrFail($id);

        if ($request->has('action')) {
            if ($request->action === 'increase') {
                $cartItem->increment('quantity');
            } elseif ($request->action === 'decrease') {
                if ($cartItem->quantity > 1) {
                    $cartItem->decrement('quantity');
                } else {
                    $cartItem->delete();
                    return back()->with('info', 'Item dihapus dari keranjang.');
                }
            }
        }

        if ($request->has('note')) {
            $cartItem->update(['note' => $request->note]);
        }

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function removeFromCart($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cartItem->delete();

        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    /**
     * Checkout View
     */
    public function checkout(Request $request)
    {
        $cart = CartService::getCart()->load('items.menu');

        if ($cart->items->isEmpty()) {
            return redirect()->route('home')->with('error', 'Keranjang Anda masih kosong.');
        }

        $tables = CafeTable::where('status', 'AVAILABLE')->get();
        $preselectedTable = $request->query('table');

        return view('customer.checkout', compact('cart', 'tables', 'preselectedTable'));
    }

    /**
     * DFD 2.0: Buat Pesanan (Checkout Order)
     */
    public function placeOrder(Request $request)
    {
        $cart = CartService::getCart()->load('items.menu');

        if ($cart->items->isEmpty()) {
            return redirect()->route('home')->with('error', 'Keranjang Anda kosong.');
        }

        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:100',
            'order_type' => 'required|in:DINE_IN,TAKE_AWAY',
            'table_id' => 'nullable|required_if:order_type,DINE_IN|exists:cafe_tables,table_id',
            'payment_method' => 'required|in:CASH,QRIS,TRANSFER',
            'note' => 'nullable|string|max:500',
        ]);

        $order = DB::transaction(function () use ($cart, $validated) {
            $totalAmount = $cart->total;

            $tableId = ($validated['order_type'] === 'DINE_IN') ? $validated['table_id'] : null;

            // Mark table as OCCUPIED if Dine-in
            if ($tableId) {
                CafeTable::where('table_id', $tableId)->update(['status' => 'OCCUPIED']);
            }

            // Create Order
            $order = Order::create([
                'customer_name' => $validated['customer_name'] ?: 'Pelanggan',
                'table_id' => $tableId,
                'order_type' => $validated['order_type'],
                'status' => 'PENDING',
                'total_amount' => $totalAmount,
                'note' => $validated['note'] ?? null,
                'ordered_at' => Carbon::now(),
                'completed_at' => null,
            ]);

            // Create Order Items
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'menu_id' => $item->menu_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->menu->price,
                    'subtotal' => $item->quantity * $item->menu->price,
                    'note' => $item->note,
                ]);
            }

            // Create Status Log
            OrderStatusLog::create([
                'order_id' => $order->order_id,
                'changed_by' => null,
                'old_status' => null,
                'new_status' => 'PENDING',
                'changed_at' => Carbon::now(),
            ]);

            // Create Initial Payment Record
            Payment::create([
                'order_id' => $order->order_id,
                'confirmed_by' => null,
                'method' => $validated['payment_method'],
                'status' => 'PENDING',
                'amount_paid' => 0,
                'change_amount' => 0,
                'paid_at' => null,
            ]);

            // Clear Cart
            CartService::clearCart();

            return $order;
        });

        return redirect()->route('customer.track', $order->order_id)
            ->with('success', 'Pesanan berhasil dibuat! Nomor Pesanan Anda: #' . $order->order_id);
    }

    /**
     * DFD 4.0: Lacak Pesanan (Track Order Status)
     */
    public function trackOrder($id)
    {
        $order = Order::with(['table', 'items.menu', 'payment.receipt', 'statusLogs.changedByStaff'])->findOrFail($id);

        return view('customer.track', compact('order'));
    }

    /**
     * Use Case: Cancel Order (Customer can cancel if status is PENDING)
     */
    public function cancelOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'PENDING') {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah diproses oleh kafe.');
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'CANCELLED']);

            // Free table if Dine-In
            if ($order->table_id) {
                CafeTable::where('table_id', $order->table_id)->update(['status' => 'AVAILABLE']);
            }

            // Log cancellation
            OrderStatusLog::create([
                'order_id' => $order->order_id,
                'changed_by' => null,
                'old_status' => 'PENDING',
                'new_status' => 'CANCELLED',
                'changed_at' => Carbon::now(),
            ]);

            if ($order->payment) {
                $order->payment->update(['status' => 'FAILED']);
            }
        });

        return back()->with('info', 'Pesanan #' . $order->order_id . ' telah berhasil dibatalkan.');
    }

    /**
     * DFD 3.0: Lihat Struk (Customer Digital Receipt)
     */
    public function receipt($id)
    {
        $order = Order::with(['table', 'items.menu', 'payment.receipt.issuedByStaff'])->findOrFail($id);

        if (!$order->payment || $order->payment->status !== 'PAID') {
            return redirect()->route('customer.track', $order->order_id)
                ->with('error', 'Struk belum diterbitkan karena pembayaran belum dikonfirmasi.');
        }

        return view('customer.receipt', compact('order'));
    }
}

