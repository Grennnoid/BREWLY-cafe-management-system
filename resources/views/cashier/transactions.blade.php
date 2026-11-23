@extends('layouts.app')

@section('title', 'Kasir - Riwayat Transaksi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('cashier.orders') }}" class="text-xs font-bold text-stone-500 hover:text-stone-800 flex items-center gap-1 mb-2">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Pesanan Kasir
            </a>
            <h1 class="text-2xl sm:text-3xl font-black text-stone-900 tracking-tight">Riwayat Transaksi & Pembayaran</h1>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-sm">
            <span class="text-xs font-bold text-stone-400 uppercase tracking-wider block">Total Tunai (Cash)</span>
            <span class="text-xl font-black text-emerald-600 mt-1 block">Rp {{ number_format($totalCash, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-sm">
            <span class="text-xs font-bold text-stone-400 uppercase tracking-wider block">Total QRIS</span>
            <span class="text-xl font-black text-blue-600 mt-1 block">Rp {{ number_format($totalQris, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-sm">
            <span class="text-xs font-bold text-stone-400 uppercase tracking-wider block">Total Transfer Bank</span>
            <span class="text-xl font-black text-purple-600 mt-1 block">Rp {{ number_format($totalTransfer, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Filters & Table -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        
        <div class="p-4 border-b border-stone-100 flex flex-wrap items-center justify-between gap-4 text-xs">
            <form action="{{ route('cashier.transactions') }}" method="GET" class="flex items-center gap-2">
                <select name="method" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-stone-200 bg-stone-50 font-medium">
                    <option value="ALL" {{ $method === 'ALL' ? 'selected' : '' }}>Semua Metode</option>
                    <option value="CASH" {{ $method === 'CASH' ? 'selected' : '' }}>Cash (Tunai)</option>
                    <option value="QRIS" {{ $method === 'QRIS' ? 'selected' : '' }}>QRIS</option>
                    <option value="TRANSFER" {{ $method === 'TRANSFER' ? 'selected' : '' }}>Transfer Bank</option>
                </select>

                <select name="status" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-stone-200 bg-stone-50 font-medium">
                    <option value="ALL" {{ $status === 'ALL' ? 'selected' : '' }}>Semua Status</option>
                    <option value="PAID" {{ $status === 'PAID' ? 'selected' : '' }}>Lunas (PAID)</option>
                    <option value="PENDING" {{ $status === 'PENDING' ? 'selected' : '' }}>Pending</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 uppercase tracking-wider font-bold">
                    <tr>
                        <th class="py-3.5 px-4">ID Bayar</th>
                        <th class="py-3.5 px-4">Order & Meja</th>
                        <th class="py-3.5 px-4">Metode</th>
                        <th class="py-3.5 px-4">Total</th>
                        <th class="py-3.5 px-4">Bayar / Kembalian</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">No. Struk</th>
                        <th class="py-3.5 px-4">Kasir Konfirmasi</th>
                        <th class="py-3.5 px-4">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($payments as $p)
                        <tr class="hover:bg-stone-50/70 transition">
                            <td class="py-3.5 px-4 font-bold text-stone-900">#PAY-{{ $p->payment_id }}</td>
                            <td class="py-3.5 px-4">
                                <a href="{{ route('cashier.orders.detail', $p->order_id) }}" class="font-bold text-brand-700 hover:underline">
                                    #{{ $p->order_id }} ({{ $p->order->customer_name ?: 'Pelanggan' }})
                                </a>
                                <span class="text-[10px] text-stone-400 block">
                                    {{ $p->order->order_type === 'DINE_IN' ? 'Meja ' . ($p->order->table?->table_number ?? '-') : 'Take Away' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-stone-100 text-stone-800">
                                    {{ $p->method }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-black text-stone-900">
                                Rp {{ number_format($p->order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-[11px]">
                                <div>Diterima: Rp {{ number_format($p->amount_paid, 0, ',', '.') }}</div>
                                @if($p->change_amount > 0)
                                    <div class="text-stone-400">Kembali: Rp {{ number_format($p->change_amount, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $p->status === 'PAID' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-[11px]">
                                @if($p->receipt)
                                    <a href="{{ route('cashier.receipt.print', $p->receipt->receipt_id) }}" target="_blank" class="text-brand-700 font-bold hover:underline">
                                        {{ $p->receipt->receipt_number }}
                                    </a>
                                @else
                                    <span class="text-stone-400">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-stone-500">
                                {{ $p->confirmedByStaff?->name ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-stone-400 text-[11px] whitespace-nowrap">
                                {{ $p->paid_at ? $p->paid_at->format('d/m/Y H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-10 text-stone-400">
                                Belum ada data transaksi pembayaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="p-4 border-t border-stone-200">
                {{ $payments->withQueryString()->links() }}
            </div>
        @endif

    </div>

</div>
@endsection

