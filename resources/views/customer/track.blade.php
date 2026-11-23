@extends('layouts.app')

@section('title', 'Lacak Pesanan #' . $order->order_id)

@push('styles')
<meta http-equiv="refresh" content="15">
@endpush

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs uppercase font-extrabold tracking-wider px-2.5 py-1 rounded-md bg-stone-200 text-stone-700">
                    Order #{{ $order->order_id }}
                </span>
                <span class="text-xs text-stone-400">
                    {{ $order->ordered_at->format('d M Y, H:i') }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-stone-900 tracking-tight mt-1">Status Pesanan Anda</h1>
        </div>

        <a href="{{ route('home') }}" class="text-xs font-bold text-brand-700 hover:text-brand-800 flex items-center gap-1">
            <i data-lucide="plus" class="w-4 h-4"></i> Pesan Lagi
        </a>
    </div>

    <!-- Live Status Tracker Card -->
    <div class="bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 shadow-sm mb-6">
        
        <!-- Current Status Banner -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-2xl mb-8 {{ $order->status_badge_class }}">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-white/80 shadow-sm flex items-center justify-center">
                    @if($order->status === 'PENDING')
                        <i data-lucide="clock" class="w-6 h-6 text-yellow-600 animate-spin"></i>
                    @elseif($order->status === 'ACCEPTED')
                        <i data-lucide="check" class="w-6 h-6 text-blue-600"></i>
                    @elseif($order->status === 'PREPARING')
                        <i data-lucide="flame" class="w-6 h-6 text-indigo-600 animate-pulse"></i>
                    @elseif($order->status === 'READY')
                        <i data-lucide="bell-ring" class="w-6 h-6 text-purple-600 animate-bounce"></i>
                    @elseif($order->status === 'COMPLETED')
                        <i data-lucide="check-circle-2" class="w-6 h-6 text-green-600"></i>
                    @elseif($order->status === 'CANCELLED')
                        <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
                    @endif
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold tracking-wider opacity-75">Status Terkini</span>
                    <h2 class="text-xl font-black tracking-tight">
                        @if($order->status === 'PENDING')
                            Menunggu Konfirmasi & Pembayaran
                        @elseif($order->status === 'ACCEPTED')
                            Pesanan Diterima oleh Dapur
                        @elseif($order->status === 'PREPARING')
                            Sedang Diramu & Dimasak di Dapur
                        @elseif($order->status === 'READY')
                            Pesanan Siap Disajikan / Diambil!
                        @elseif($order->status === 'COMPLETED')
                            Pesanan Selesai Dinikmati
                        @elseif($order->status === 'CANCELLED')
                            Pesanan Dibatalkan
                        @endif
                    </h2>
                </div>
            </div>

            <!-- Auto-refresh pill -->
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] bg-white/70 font-semibold shadow-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <span>Live update otomatis</span>
            </span>
        </div>

        @if($order->status !== 'CANCELLED')
            <!-- Step Progress Bar -->
            @php
                $steps = [
                    'PENDING' => ['title' => 'Menunggu', 'icon' => 'clock', 'step' => 1],
                    'ACCEPTED' => ['title' => 'Diterima', 'icon' => 'check', 'step' => 2],
                    'PREPARING' => ['title' => 'Disiapkan', 'icon' => 'utensils', 'step' => 3],
                    'READY' => ['title' => 'Siap', 'icon' => 'bell', 'step' => 4],
                    'COMPLETED' => ['title' => 'Selesai', 'icon' => 'smile', 'step' => 5],
                ];

                $currentStepNum = match($order->status) {
                    'PENDING' => 1,
                    'ACCEPTED' => 2,
                    'PREPARING' => 3,
                    'READY' => 4,
                    'COMPLETED' => 5,
                    default => 0,
                };
            @endphp

            <div class="relative py-4">
                <div class="overflow-hidden h-1.5 mb-6 text-xs flex rounded-full bg-stone-100">
                    <div style="width: {{ (($currentStepNum - 1) / 4) * 100 }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-brand-700 transition-all duration-700"></div>
                </div>

                <div class="grid grid-cols-5 text-center">
                    @foreach($steps as $key => $s)
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-sm {{ $currentStepNum >= $s['step'] ? 'bg-brand-700 text-white scale-110' : 'bg-stone-100 text-stone-400' }}">
                                <i data-lucide="{{ $s['icon'] }}" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="text-[11px] font-bold mt-2 {{ $currentStepNum >= $s['step'] ? 'text-stone-900' : 'text-stone-400' }}">
                                {{ $s['title'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Order Information Summary -->
        <div class="mt-8 pt-6 border-t border-stone-100 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div class="p-3 rounded-xl bg-stone-50">
                <span class="text-stone-400 block mb-0.5">Pemesan</span>
                <strong class="text-stone-800 text-sm">{{ $order->customer_name ?: 'Pelanggan' }}</strong>
            </div>
            <div class="p-3 rounded-xl bg-stone-50">
                <span class="text-stone-400 block mb-0.5">Tipe Pesanan</span>
                <strong class="text-stone-800 text-sm">
                    {{ $order->order_type === 'DINE_IN' ? 'Dine In (Meja ' . ($order->table?->table_number ?? '-') . ')' : 'Take Away' }}
                </strong>
            </div>
            <div class="p-3 rounded-xl bg-stone-50">
                <span class="text-stone-400 block mb-0.5">Metode Bayar</span>
                <strong class="text-stone-800 text-sm">{{ $order->payment?->method ?? 'CASH' }}</strong>
            </div>
            <div class="p-3 rounded-xl bg-stone-50">
                <span class="text-stone-400 block mb-0.5">Status Bayar</span>
                <strong class="{{ $order->payment?->status === 'PAID' ? 'text-emerald-600' : 'text-amber-600' }} text-sm font-extrabold">
                    {{ $order->payment?->status === 'PAID' ? 'LUNAS (PAID)' : 'MENUNGGU KASIR' }}
                </strong>
            </div>
        </div>

        @if($order->note)
            <div class="mt-4 p-3 rounded-xl bg-amber-50/70 border border-amber-200 text-xs text-amber-900">
                <strong>Catatan Anda:</strong> "{{ $order->note }}"
            </div>
        @endif

    </div>

    <!-- Ordered Items Card -->
    <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-sm mb-6">
        <h3 class="font-bold text-stone-900 text-base pb-3 border-b border-stone-100 flex items-center justify-between">
            <span>Daftar Menu yang Dipesan</span>
            <span class="text-xs text-stone-400">{{ $order->items->count() }} macam item</span>
        </h3>

        <div class="divide-y divide-stone-100">
            @foreach($order->items as $item)
                <div class="py-3 flex items-center justify-between text-xs sm:text-sm">
                    <div class="flex items-center gap-3">
                        <img src="{{ $item->menu->image_url ?: 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=200' }}"
                             alt="{{ $item->menu->name }}" class="w-12 h-12 rounded-xl object-cover border border-stone-100">
                        <div>
                            <p class="font-bold text-stone-900">{{ $item->menu->name }}</p>
                            <p class="text-stone-400 text-xs">{{ $item->quantity }}x @ Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                            @if($item->note)
                                <p class="text-[11px] text-amber-700 italic">"{{ $item->note }}"</p>
                            @endif
                        </div>
                    </div>
                    <span class="font-black text-stone-900">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </span>
                </div>
            @endforeach
        </div>

        <div class="mt-4 pt-4 border-t border-stone-100 flex items-center justify-between">
            <span class="text-sm font-bold text-stone-700">Total Pembayaran</span>
            <span class="text-xl font-black text-brand-700">
                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
            </span>
        </div>
    </div>

    <!-- Actions Section: Cancel or View Receipt -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        
        <!-- Cancel button (allowed only if status is PENDING) -->
        @if($order->status === 'PENDING')
            <form action="{{ route('customer.order.cancel', $order->order_id) }}" method="POST"
                  onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                @csrf
                <button type="submit" class="px-4 py-2.5 rounded-xl border border-red-300 text-red-600 hover:bg-red-50 text-xs font-bold transition flex items-center gap-1.5">
                    <i data-lucide="x" class="w-4 h-4"></i> Batalkan Pesanan
                </button>
            </form>
        @else
            <div></div>
        @endif

        <!-- View Receipt Button (if payment PAID) -->
        @if($order->payment && $order->payment->status === 'PAID')
            <a href="{{ route('customer.receipt', $order->order_id) }}" class="px-5 py-2.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold tracking-wide transition shadow flex items-center gap-2">
                <i data-lucide="receipt" class="w-4 h-4"></i>
                <span>Lihat Struk Digital (Digital Receipt)</span>
            </a>
        @else
            <span class="text-xs text-stone-400 italic">
                * Struk dapat dilihat setelah kasir mengonfirmasi pembayaran
            </span>
        @endif

    </div>

</div>
@endsection

