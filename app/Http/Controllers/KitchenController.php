<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\CafeTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KitchenController extends Controller
{
    /**
     * DFD 4.0: Kitchen Display System (KDS)
     */
    public function index()
    {
        $pendingOrders = Order::with(['table', 'items.menu', 'payment'])
            ->where('status', 'PENDING')
            ->orderBy('created_at', 'asc')
            ->get();

        $acceptedOrders = Order::with(['table', 'items.menu', 'payment'])
            ->where('status', 'ACCEPTED')
            ->orderBy('created_at', 'asc')
            ->get();

        $preparingOrders = Order::with(['table', 'items.menu', 'payment'])
            ->where('status', 'PREPARING')
            ->orderBy('created_at', 'asc')
            ->get();

        $readyOrders = Order::with(['table', 'items.menu', 'payment'])
            ->where('status', 'READY')
            ->orderBy('created_at', 'asc')
            ->get();

        $recentCompleted = Order::with(['table', 'items.menu'])
            ->where('status', 'COMPLETED')
            ->whereDate('completed_at', Carbon::today())
            ->orderBy('completed_at', 'desc')
            ->take(5)
            ->get();

        return view('kitchen.index', compact('pendingOrders', 'acceptedOrders', 'preparingOrders', 'readyOrders', 'recentCompleted'));
    }

    /**
     * DFD 4.0 & Use Case: Accept Order / Update Order Status / Confirm Order Completion
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:PENDING,ACCEPTED,PREPARING,READY,COMPLETED,CANCELLED',
        ]);

        $oldStatus = $order->status;
        $newStatus = $validated['status'];

        if ($oldStatus === $newStatus) {
            return back();
        }

        DB::transaction(function () use ($order, $oldStatus, $newStatus) {
            $updateData = ['status' => $newStatus];

            if ($newStatus === 'COMPLETED') {
                $updateData['completed_at'] = Carbon::now();
                if ($order->table_id) {
                    CafeTable::where('table_id', $order->table_id)->update(['status' => 'AVAILABLE']);
                }
            }

            $order->update($updateData);

            OrderStatusLog::create([
                'order_id' => $order->order_id,
                'changed_by' => Auth::id(),
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_at' => Carbon::now(),
            ]);
        });

        return back()->with('success', "Status Pesanan #{$order->order_id} berhasil diperbarui ke {$newStatus}.");
    }
}

