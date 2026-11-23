@extends('layouts.app')

@section('title', 'Struk Pembayaran #' . ($order->payment->receipt->receipt_number ?? $order->order_id))

@section('content')
<div class="max-w-md mx-auto px-4 py-8">

    <div class="mb-4 flex items-center justify-between no-print">
        <a href="{{ route('customer.track', $order->order_id) }}" class="text-xs font-bold text-stone-600 hover:text-stone-900 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Status Pesanan
        </a>
        <button onclick="window.print()" class="px-3.5 py-1.5 rounded-xl bg-stone-900 text-white text-xs font-bold hover:bg-stone-800 transition flex items-center gap-1.5 shadow-sm">
            <i data-lucide="printer" class="w-3.5 h-3.5"></i> Cetak Struk
        </button>
    </div>

    <!-- Thermal Paper Receipt Card -->
    <div class="bg-white rounded-2xl shadow-xl border border-stone-200 p-6 sm:p-8 font-mono text-stone-900 relative">
        
        <!-- Header -->
        <div class="text-center pb-4 border-b border-dashed border-stone-300">
            <h2 class="text-xl font-black tracking-tight text-stone-950 font-sans">BREWLY CAFE & ROASTERY</h2>
            <p class="text-[11px] text-stone-500 font-sans mt-0.5">Jl. Kopi Harum No. 88, Jakarta Selatan</p>
            <p class="text-[10px] text-stone-400 font-sans">Telp: (021) 7890-1234 • IG: @brewly.cafe</p>
        </div>

        <!-- Receipt Details -->
        <div class="py-3 text-[11px] border-b border-dashed border-stone-300 space-y-1">
            <div class="flex justify-between">
                <span>No. Struk:</span>
                <span class="font-bold">{{ $order->payment->receipt->receipt_number ?? ('RCP-' . $order->order_id) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Waktu Terbit:</span>
                <span>{{ $order->payment->paid_at ? $order->payment->paid_at->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Kasir:</span>
                <span>{{ $order->payment->receipt?->issuedByStaff?->name ?? 'Kasir Brewly' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Pelanggan:</span>
                <span>{{ $order->customer_name ?: 'Pelanggan' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Tipe / Meja:</span>
                <span>{{ $order->order_type === 'DINE_IN' ? 'DINE IN (' . ($order->table?->table_number ?? '-') . ')' : 'TAKE AWAY' }}</span>
            </div>
        </div>

        <!-- Items Table -->
        <div class="py-3 border-b border-dashed border-stone-300 text-xs space-y-2">
            @foreach($order->items as $item)
                <div>
                    <div class="flex justify-between font-bold">
                        <span>{{ $item->menu->name }}</span>
                        <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-[11px] text-stone-500">
                        <span>{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                        @if($item->note)
                            <span class="italic text-[10px] text-stone-400">({{ $item->note }})</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Calculations -->
        <div class="py-3 border-b border-dashed border-stone-300 text-xs space-y-1.5">
            <div class="flex justify-between">
                <span>Total Belanja:</span>
                <span class="font-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-[11px] text-stone-500">
                <span>Metode Bayar:</span>
                <span class="font-bold text-stone-800">{{ $order->payment->method }}</span>
            </div>
            <div class="flex justify-between">
                <span>Jumlah Bayar:</span>
                <span>Rp {{ number_format($order->payment->amount_paid, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold text-stone-900">
                <span>Kembalian:</span>
                <span>Rp {{ number_format($order->payment->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center pt-4 font-sans text-xs text-stone-500">
            <p class="font-bold text-stone-800">Terima Kasih Telah Berkunjung!</p>
            <p class="text-[10px] text-stone-400 mt-0.5">Nikmati hari Anda bersama secangkir kehangatan.</p>
            
            <div class="mt-4 pt-3 border-t border-stone-100 flex items-center justify-center gap-2 text-[10px] text-stone-400">
                <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-500"></i>
                <span>STATUS: LUNAS (PAID)</span>
            </div>
        </div>

    </div>

</div>
@endsection

