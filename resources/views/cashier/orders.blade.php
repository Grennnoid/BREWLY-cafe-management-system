@extends('layouts.app')

@section('title', 'Kasir - Daftar Pesanan')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ payModal: false, selectedOrder: null, cashGiven: 0 }">

    <!-- Header & Cashier Stats -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-md bg-amber-100 text-amber-800 text-xs font-black uppercase tracking-wider">
                    Portal Kasir
                </span>
                <span class="text-xs text-stone-500">Staf: {{ auth()->user()->name }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-stone-900 tracking-tight mt-1">
                Kelola Pesanan & Pembayaran
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('cashier.transactions') }}" class="px-4 py-2.5 rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                <i data-lucide="history" class="w-4 h-4 text-stone-400"></i> Riwayat Transaksi
            </a>
            <a href="{{ route('cashier.orders') }}" class="px-3.5 py-2.5 rounded-xl bg-stone-900 text-white text-xs font-bold hover:bg-stone-800 transition flex items-center gap-1.5 shadow-sm">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i> Refresh
            </a>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-sm mb-6 flex flex-wrap items-center justify-between gap-4">
        
        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
            <a href="{{ route('cashier.orders') }}"
               class="px-3 py-1.5 rounded-lg font-bold transition {{ empty($status) || $status === 'ALL' ? 'bg-stone-900 text-white' : 'text-stone-600 hover:bg-stone-100' }}">
                Semua
            </a>
            <a href="{{ route('cashier.orders', ['status' => 'PENDING', 'payment_status' => $paymentStatus]) }}"
               class="px-3 py-1.5 rounded-lg font-bold transition {{ $status === 'PENDING' ? 'bg-yellow-500 text-white' : 'text-stone-600 hover:bg-stone-100' }}">
                Pending
            </a>
            <a href="{{ route('cashier.orders', ['status' => 'ACCEPTED', 'payment_status' => $paymentStatus]) }}"
               class="px-3 py-1.5 rounded-lg font-bold transition {{ $status === 'ACCEPTED' ? 'bg-blue-600 text-white' : 'text-stone-600 hover:bg-stone-100' }}">
                Accepted
            </a>
            <a href="{{ route('cashier.orders', ['status' => 'PREPARING', 'payment_status' => $paymentStatus]) }}"
               class="px-3 py-1.5 rounded-lg font-bold transition {{ $status === 'PREPARING' ? 'bg-indigo-600 text-white' : 'text-stone-600 hover:bg-stone-100' }}">
                Preparing
            </a>
            <a href="{{ route('cashier.orders', ['status' => 'READY', 'payment_status' => $paymentStatus]) }}"
               class="px-3 py-1.5 rounded-lg font-bold transition {{ $status === 'READY' ? 'bg-purple-600 text-white' : 'text-stone-600 hover:bg-stone-100' }}">
                Ready
            </a>
            <a href="{{ route('cashier.orders', ['status' => 'COMPLETED', 'payment_status' => $paymentStatus]) }}"
               class="px-3 py-1.5 rounded-lg font-bold transition {{ $status === 'COMPLETED' ? 'bg-emerald-600 text-white' : 'text-stone-600 hover:bg-stone-100' }}">
                Completed
            </a>
        </div>

        <!-- Search & Payment Status Filter -->
        <form action="{{ route('cashier.orders') }}" method="GET" class="flex items-center gap-2 text-xs">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif

            <select name="payment_status" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-stone-200 bg-stone-50 font-medium">
                <option value="ALL" {{ $paymentStatus === 'ALL' ? 'selected' : '' }}>Semua Pembayaran</option>
                <option value="PENDING" {{ $paymentStatus === 'PENDING' ? 'selected' : '' }}>Belum Bayar (Pending)</option>
                <option value="PAID" {{ $paymentStatus === 'PAID' ? 'selected' : '' }}>Lunas (Paid)</option>
            </select>

            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari ID / Nama..."
                    class="pl-8 pr-3 py-2 rounded-xl border border-stone-200 bg-stone-50 focus:ring-1 focus:ring-brand-600 w-40 sm:w-56">
                <i data-lucide="search" class="w-3.5 h-3.5 text-stone-400 absolute left-2.5 top-2.5"></i>
            </div>
            
            <button type="submit" class="px-3 py-2 rounded-xl bg-stone-800 text-white font-bold hover:bg-stone-700 transition">
                Cari
            </button>
        </form>

    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 uppercase tracking-wider font-bold">
                    <tr>
                        <th class="py-3.5 px-4">No. Order</th>
                        <th class="py-3.5 px-4">Waktu</th>
                        <th class="py-3.5 px-4">Pelanggan & Meja</th>
                        <th class="py-3.5 px-4">Item Pesanan</th>
                        <th class="py-3.5 px-4">Total</th>
                        <th class="py-3.5 px-4">Status Pesanan</th>
                        <th class="py-3.5 px-4">Pembayaran</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($orders as $o)
                        <tr class="hover:bg-stone-50/70 transition">
                            <!-- No Order -->
                            <td class="py-3.5 px-4 font-black text-stone-900">
                                #{{ $o->order_id }}
                            </td>

                            <!-- Time -->
                            <td class="py-3.5 px-4 text-stone-500 whitespace-nowrap">
                                {{ $o->ordered_at->format('H:i') }}
                                <span class="block text-[10px] text-stone-400">{{ $o->ordered_at->format('d/m/Y') }}</span>
                            </td>

                            <!-- Customer & Table -->
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-stone-900 block">{{ $o->customer_name ?: 'Pelanggan' }}</span>
                                <span class="text-[11px] px-2 py-0.5 rounded font-semibold {{ $o->order_type === 'DINE_IN' ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-stone-100 text-stone-600' }}">
                                    {{ $o->order_type === 'DINE_IN' ? ('Meja ' . ($o->table?->table_number ?? '-')) : 'Take Away' }}
                                </span>
                            </td>

                            <!-- Items -->
                            <td class="py-3.5 px-4 max-w-xs">
                                <p class="line-clamp-2 text-[11px] text-stone-600">
                                    @foreach($o->items as $item)
                                        {{ $item->quantity }}x {{ $item->menu->name }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </p>
                            </td>

                            <!-- Total -->
                            <td class="py-3.5 px-4 font-black text-stone-900 whitespace-nowrap">
                                Rp {{ number_format($o->total_amount, 0, ',', '.') }}
                            </td>

                            <!-- Order Status Badge -->
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase border {{ $o->status_badge_class }}">
                                    {{ $o->status }}
                                </span>
                            </td>

                            <!-- Payment Badge & Method -->
                            <td class="py-3.5 px-4">
                                @if($o->payment && $o->payment->status === 'PAID')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        <i data-lucide="check" class="w-3 h-3"></i> Lunas ({{ $o->payment->method }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800 border border-amber-300">
                                        <i data-lucide="clock" class="w-3 h-3"></i> Belum Bayar ({{ $o->payment?->method ?? 'CASH' }})
                                    </span>
                                @endif
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    
                                    <!-- If not paid yet, show Process Payment button -->
                                    @if(!$o->payment || $o->payment->status !== 'PAID')
                                        <button @click="selectedOrder = {{ json_encode($o) }}; cashGiven = {{ $o->total_amount }}; payModal = true"
                                                class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm flex items-center gap-1">
                                            <i data-lucide="credit-card" class="w-3.5 h-3.5"></i> Bayar
                                        </button>
                                    @else
                                        <!-- Struk view / print -->
                                        @if($o->payment->receipt)
                                            <a href="{{ route('cashier.receipt.print', $o->payment->receipt->receipt_id) }}" target="_blank"
                                               class="p-1.5 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 transition" title="Cetak Struk">
                                                <i data-lucide="printer" class="w-4 h-4"></i>
                                            </a>
                                        @else
                                            <form action="{{ route('cashier.receipt.issue', $o->payment->payment_id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 rounded-lg bg-stone-800 text-white text-[11px] font-bold">
                                                    Terbitkan Struk
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                    <!-- Detail Link -->
                                    <a href="{{ route('cashier.orders.detail', $o->order_id) }}" class="p-1.5 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 transition" title="Lihat Detail">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-stone-400">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                Tidak ada data pesanan yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-stone-200">
                {{ $orders->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- Process Payment Modal (Alpine.js) -->
    <div x-show="payModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-sm">
        <div @click.outside="payModal = false" class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 animate-scale-up" x-data="{ method: 'CASH' }">
            
            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                <div>
                    <h3 class="font-black text-stone-900 text-base">Proses Pembayaran</h3>
                    <p class="text-xs text-stone-400">Order <strong x-text="'#' + (selectedOrder ? selectedOrder.order_id : '')"></strong> - <span x-text="selectedOrder ? selectedOrder.customer_name : ''"></span></p>
                </div>
                <button @click="payModal = false" class="text-stone-400 hover:text-stone-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <template x-if="selectedOrder">
                <form :action="'/cashier/orders/' + selectedOrder.order_id + '/pay'" method="POST" class="mt-4 space-y-4">
                    @csrf

                    <!-- Total Bill -->
                    <div class="p-4 rounded-2xl bg-brand-50 border border-brand-200 text-center">
                        <span class="text-xs uppercase font-bold text-brand-700">Total Tagihan Pesanan</span>
                        <p class="text-3xl font-black text-brand-900 mt-1" x-text="'Rp ' + Number(selectedOrder.total_amount).toLocaleString('id-ID')"></p>
                    </div>

                    <!-- Payment Method Selector -->
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1.5">Pilih Metode Pembayaran</label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="p-2.5 rounded-xl border text-center cursor-pointer text-xs font-bold transition"
                                   :class="method === 'CASH' ? 'border-brand-700 bg-brand-50 text-brand-900' : 'border-stone-200 text-stone-600'">
                                <input type="radio" name="method" value="CASH" x-model="method" class="sr-only">
                                Tunai (Cash)
                            </label>
                            <label class="p-2.5 rounded-xl border text-center cursor-pointer text-xs font-bold transition"
                                   :class="method === 'QRIS' ? 'border-brand-700 bg-brand-50 text-brand-900' : 'border-stone-200 text-stone-600'">
                                <input type="radio" name="method" value="QRIS" x-model="method" class="sr-only">
                                QRIS
                            </label>
                            <label class="p-2.5 rounded-xl border text-center cursor-pointer text-xs font-bold transition"
                                   :class="method === 'TRANSFER' ? 'border-brand-700 bg-brand-50 text-brand-900' : 'border-stone-200 text-stone-600'">
                                <input type="radio" name="method" value="TRANSFER" x-model="method" class="sr-only">
                                Transfer
                            </label>
                        </div>
                    </div>

                    <!-- Cash Calculation inputs -->
                    <div x-show="method === 'CASH'" class="space-y-3 pt-2">
                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Uang Diterima dari Pelanggan (Rp)</label>
                            <input type="number" name="amount_paid" x-model.number="cashGiven" :min="selectedOrder.total_amount" required
                                class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-base font-bold focus:ring-2 focus:ring-brand-600">
                        </div>

                        <!-- Quick Cash Shortcuts -->
                        <div class="flex items-center gap-1.5 text-[11px]">
                            <button type="button" @click="cashGiven = Number(selectedOrder.total_amount)" class="px-2.5 py-1 rounded bg-stone-100 font-bold hover:bg-stone-200">Uang Pas</button>
                            <button type="button" @click="cashGiven = 50000" class="px-2.5 py-1 rounded bg-stone-100 font-bold hover:bg-stone-200">50k</button>
                            <button type="button" @click="cashGiven = 100000" class="px-2.5 py-1 rounded bg-stone-100 font-bold hover:bg-stone-200">100k</button>
                        </div>

                        <!-- Change calculation -->
                        <div class="p-3 rounded-xl bg-stone-50 border border-stone-200 flex justify-between items-center text-xs">
                            <span class="font-bold text-stone-600">Kembalian:</span>
                            <span class="text-base font-black text-emerald-600"
                                  x-text="'Rp ' + (cashGiven >= selectedOrder.total_amount ? Number(cashGiven - selectedOrder.total_amount).toLocaleString('id-ID') : '0')">
                            </span>
                        </div>
                    </div>

                    <!-- QRIS / Transfer Notice -->
                    <div x-show="method !== 'CASH'" class="p-3 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-800">
                        Pastikan pembayaran via QRIS atau Transfer bank telah masuk ke rekening / mutasi kafe sebelum mengonfirmasi.
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-stone-100">
                        <button type="button" @click="payModal = false" class="px-4 py-2.5 rounded-xl border border-stone-200 text-stone-600 text-xs font-bold hover:bg-stone-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs tracking-wide shadow-md flex items-center gap-1.5">
                            <i data-lucide="check" class="w-4 h-4"></i> Konfirmasi Pembayaran & Cetak Struk
                        </button>
                    </div>

                </form>
            </template>

        </div>
    </div>

</div>
@endsection

