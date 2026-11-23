@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_id)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('cashier.orders') }}" class="text-xs font-bold text-stone-500 hover:text-stone-800 flex items-center gap-1 mb-2">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Daftar Kasir
            </a>
            <h1 class="text-2xl sm:text-3xl font-black text-stone-900 tracking-tight">Detail Pesanan #{{ $order->order_id }}</h1>
        </div>

        <div class="flex items-center gap-2">
            @if($order->payment && $order->payment->receipt)
                <a href="{{ route('cashier.receipt.print', $order->payment->receipt->receipt_id) }}" target="_blank"
                   class="px-4 py-2 rounded-xl bg-stone-900 text-white text-xs font-bold hover:bg-stone-800 transition flex items-center gap-1.5 shadow-sm">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak Struk
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Order Items & Customer Details -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Items Box -->
            <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm">
                <h3 class="font-bold text-stone-900 text-base pb-3 border-b border-stone-100 flex items-center justify-between">
                    <span>Menu yang Dipesan</span>
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
                    <span class="text-sm font-bold text-stone-700">Total Tagihan</span>
                    <span class="text-xl font-black text-brand-700">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Status Logs Timeline (ERD: order_status_logs) -->
            <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm">
                <h3 class="font-bold text-stone-900 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-brand-700"></i>
                    <span>Riwayat Perubahan Status (Log Status)</span>
                </h3>

                <div class="space-y-4">
                    @forelse($order->statusLogs as $log)
                        <div class="flex items-start gap-3 text-xs">
                            <div class="w-2 h-2 rounded-full bg-brand-600 mt-1.5 flex-shrink-0"></div>
                            <div class="flex-grow">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-stone-800">
                                        Status diubah: <span class="uppercase text-brand-700">{{ $log->new_status }}</span>
                                    </span>
                                    <span class="text-stone-400 text-[11px]">{{ $log->changed_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                                <p class="text-stone-500 text-[11px] mt-0.5">
                                    Oleh: <strong>{{ $log->changedByStaff ? $log->changedByStaff->name . ' (' . $log->changedByStaff->role . ')' : 'Pelanggan / Sistem' }}</strong>
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-stone-400">Belum ada riwayat log status.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right: Status Updater & Payment Details -->
        <div class="space-y-6">
            
            <!-- Update Order Status Box -->
            <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm">
                <h3 class="font-bold text-stone-900 text-xs uppercase tracking-wider mb-3">Ubah Status Pesanan</h3>
                
                <form action="{{ route('cashier.orders.status', $order->order_id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <select name="status" class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs font-bold text-stone-800 focus:ring-2 focus:ring-brand-600">
                            <option value="PENDING" {{ $order->status === 'PENDING' ? 'selected' : '' }}>PENDING (Menunggu)</option>
                            <option value="ACCEPTED" {{ $order->status === 'ACCEPTED' ? 'selected' : '' }}>ACCEPTED (Diterima Dapur)</option>
                            <option value="PREPARING" {{ $order->status === 'PREPARING' ? 'selected' : '' }}>PREPARING (Sedang Dimasak)</option>
                            <option value="READY" {{ $order->status === 'READY' ? 'selected' : '' }}>READY (Siap Saji/Ambil)</option>
                            <option value="COMPLETED" {{ $order->status === 'COMPLETED' ? 'selected' : '' }}>COMPLETED (Selesai)</option>
                            <option value="CANCELLED" {{ $order->status === 'CANCELLED' ? 'selected' : '' }}>CANCELLED (Batal)</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2 px-3 rounded-xl bg-stone-900 text-white font-bold text-xs hover:bg-stone-800 transition">
                        Update Status
                    </button>
                </form>
            </div>

            <!-- Payment Details Box -->
            <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm">
                <h3 class="font-bold text-stone-900 text-xs uppercase tracking-wider mb-3">Informasi Pembayaran</h3>
                
                @if($order->payment)
                    <div class="space-y-2.5 text-xs text-stone-600">
                        <div class="flex justify-between">
                            <span>Status Bayar</span>
                            <span class="font-black {{ $order->payment->status === 'PAID' ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $order->payment->status }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span>Metode</span>
                            <span class="font-bold text-stone-800">{{ $order->payment->method }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Nominal Bayar</span>
                            <span class="font-bold text-stone-800">Rp {{ number_format($order->payment->amount_paid, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Kembalian</span>
                            <span class="font-bold text-stone-800">Rp {{ number_format($order->payment->change_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Dikonfirmasi Oleh</span>
                            <span class="font-bold text-stone-800">{{ $order->payment->confirmedByStaff?->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Waktu Bayar</span>
                            <span class="text-stone-800">{{ $order->payment->paid_at ? $order->payment->paid_at->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-stone-400">Data pembayaran belum tersedia.</p>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection

