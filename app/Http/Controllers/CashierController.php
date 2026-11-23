<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\OrderStatusLog;
use App\Models\CafeTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    /**
     * DFD 3.0 & Use Case: View Orders
     */
    public function orders(Request $request)
    {
        $status = $request->query('status');
        $paymentStatus = $request->query('payment_status');
        $search = $request->query('search');

        $query = Order::with(['table', 'items.menu', 'payment.receipt'])
            ->orderBy('created_at', 'desc');

        if ($status && $status !== 'ALL') {
            $query->where('status', $status);
        }

        if ($paymentStatus && $paymentStatus !== 'ALL') {
            $query->whereHas('payment', function ($q) use ($paymentStatus) {
                $q->where('status', $paymentStatus);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15);

        return view('cashier.orders', compact('orders', 'status', 'paymentStatus', 'search'));
    }

    /**
     * View Order Details for Cashier
     */
    public function orderDetail($id)
    {
        $order = Order::with(['table', 'items.menu', 'payment.receipt.issuedByStaff', 'statusLogs.changedByStaff'])->findOrFail($id);

        return view('cashier.order_detail', compact('order'));
    }

    /**
     * DFD 3.0 & Use Case: Confirm Payment / Process Cash Payment
     */
    public function processPayment(Request $request, $id)
    {
        $order = Order::with('payment')->findOrFail($id);

        if (!$order->payment) {
            return back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $validated = $request->validate([
            'method' => 'required|in:CASH,QRIS,TRANSFER',
            'amount_paid' => 'nullable|numeric|min:0',
        ]);

        $totalAmount = $order->total_amount;
        $method = $validated['method'];

        if ($method === 'CASH') {
            $amountPaid = (float) ($validated['amount_paid'] ?? 0);
            if ($amountPaid < $totalAmount) {
                return back()->with('error', 'Jumlah uang tunai kurang dari total tagihan (Rp ' . number_format($totalAmount, 0, ',', '.') . ').');
            }
            $changeAmount = $amountPaid - $totalAmount;
        } else {
            $amountPaid = $totalAmount;
            $changeAmount = 0;
        }

        DB::transaction(function () use ($order, $method, $amountPaid, $changeAmount) {
            $order->payment->update([
                'method' => $method,
                'status' => 'PAID',
                'amount_paid' => $amountPaid,
                'change_amount' => $changeAmount,
                'confirmed_by' => Auth::id(),
                'paid_at' => Carbon::now(),
            ]);

            // Auto issue receipt if not yet issued
            if (!$order->payment->receipt) {
                $today = Carbon::now()->format('Ymd');
                $countToday = Receipt::whereDate('issued_at', Carbon::today())->count() + 1;
                $receiptNumber = 'RCP-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

                Receipt::create([
                    'payment_id' => $order->payment->payment_id,
                    'issued_by' => Auth::id(),
                    'receipt_number' => $receiptNumber,
                    'issued_at' => Carbon::now(),
                ]);
            }
        });

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi dan struk telah diterbitkan.');
    }

    /**
     * DFD 3.0 & Use Case: Issue Receipt
     */
    public function issueReceipt(Request $request, $paymentId)
    {
        $payment = Payment::with('order')->findOrFail($paymentId);

        if ($payment->status !== 'PAID') {
            return back()->with('error', 'Struk hanya dapat diterbitkan untuk pembayaran yang sudah LUNAS (PAID).');
        }

        if ($payment->receipt) {
            return back()->with('info', 'Struk sudah pernah diterbitkan: ' . $payment->receipt->receipt_number);
        }

        $today = Carbon::now()->format('Ymd');
        $countToday = Receipt::whereDate('issued_at', Carbon::today())->count() + 1;
        $receiptNumber = 'RCP-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

        Receipt::create([
            'payment_id' => $payment->payment_id,
            'issued_by' => Auth::id(),
            'receipt_number' => $receiptNumber,
            'issued_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Struk berhasil diterbitkan: ' . $receiptNumber);
    }

    /**
     * Print / Preview Receipt
     */
    public function printReceipt($receiptId)
    {
        $receipt = Receipt::with(['payment.order.items.menu', 'payment.order.table', 'issuedByStaff'])->findOrFail($receiptId);

        return view('cashier.receipt_print', compact('receipt'));
    }

    /**
     * Use Case: Update Order Status (Cashier)
     */
    public function updateOrderStatus(Request $request, $id)
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
            $dataToUpdate = ['status' => $newStatus];

            if ($newStatus === 'COMPLETED') {
                $dataToUpdate['completed_at'] = Carbon::now();
                if ($order->table_id) {
                    CafeTable::where('table_id', $order->table_id)->update(['status' => 'AVAILABLE']);
                }
            } elseif ($newStatus === 'CANCELLED') {
                if ($order->table_id) {
                    CafeTable::where('table_id', $order->table_id)->update(['status' => 'AVAILABLE']);
                }
            }

            $order->update($dataToUpdate);

            OrderStatusLog::create([
                'order_id' => $order->order_id,
                'changed_by' => Auth::id(),
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_at' => Carbon::now(),
            ]);
        });

        return back()->with('success', "Status pesanan #{$order->order_id} berhasil diubah ke {$newStatus}.");
    }

    /**
     * Use Case: View Transaction
     */
    public function transactions(Request $request)
    {
        $method = $request->query('method');
        $status = $request->query('status');

        $query = Payment::with(['order.table', 'confirmedByStaff', 'receipt'])
            ->orderBy('created_at', 'desc');

        if ($method && $method !== 'ALL') {
            $query->where('method', $method);
        }

        if ($status && $status !== 'ALL') {
            $query->where('status', $status);
        }

        $payments = $query->paginate(20);

        $totalCash = Payment::where('method', 'CASH')->where('status', 'PAID')->sum('amount_paid');
        $totalQris = Payment::where('method', 'QRIS')->where('status', 'PAID')->sum('amount_paid');
        $totalTransfer = Payment::where('method', 'TRANSFER')->where('status', 'PAID')->sum('amount_paid');

        return view('cashier.transactions', compact('payments', 'method', 'status', 'totalCash', 'totalQris', 'totalTransfer'));
    }
}

