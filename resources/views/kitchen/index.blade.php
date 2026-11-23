@extends('layouts.app')

@section('title', 'Kitchen Display System (KDS)')

@push('styles')
<meta http-equiv="refresh" content="15">
@endpush

@section('content')
<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <!-- Kitchen Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-md bg-blue-100 text-blue-900 text-xs font-black uppercase tracking-wider">
                    Kitchen Display System (KDS)
                </span>
                <span class="text-xs text-stone-500">Koki / Barista: {{ auth()->user()->name }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-stone-900 tracking-tight mt-1">
                Layar Antrian Dapur & Barista
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                <span>Live Refresh (15s)</span>
            </span>
            <a href="{{ route('kitchen.index') }}" class="px-3.5 py-2 rounded-xl bg-stone-900 text-white text-xs font-bold hover:bg-stone-800 transition shadow flex items-center gap-1.5">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i> Segarkan Layar
            </a>
        </div>
    </div>

    <!-- 4 Kanban Columns: PENDING, ACCEPTED, PREPARING, READY -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        
        <!-- 1. PENDING (Pesanan Baru Masuk) -->
        <div class="bg-stone-100/70 rounded-3xl p-4 border border-stone-200 flex flex-col min-h-[600px]">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-stone-200">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                    <h2 class="font-black text-stone-900 text-sm uppercase tracking-wide">1. Pesanan Baru</h2>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-yellow-100 text-yellow-800 text-xs font-black">
                    {{ $pendingOrders->count() }}
                </span>
            </div>

            <div class="space-y-4 flex-grow overflow-y-auto">
                @forelse($pendingOrders as $order)
                    <div class="bg-white rounded-2xl border-2 border-yellow-300 p-4 shadow-sm animate-pulse-subtle">
                        <div class="flex items-center justify-between pb-2 border-b border-stone-100 text-xs">
                            <span class="font-black text-stone-900 text-sm">#{{ $order->order_id }}</span>
                            <span class="px-2 py-0.5 rounded font-black text-[10px] {{ $order->order_type === 'DINE_IN' ? 'bg-amber-100 text-amber-900' : 'bg-stone-800 text-white' }}">
                                {{ $order->order_type === 'DINE_IN' ? 'Meja ' . ($order->table?->table_number ?? '-') : 'Take Away' }}
                            </span>
                        </div>

                        <div class="py-2 text-xs text-stone-500 flex justify-between">
                            <span>Pemesan: <strong>{{ $order->customer_name ?: 'Pelanggan' }}</strong></span>
                            <span>{{ $order->ordered_at->diffForHumans() }}</span>
                        </div>

                        <!-- Items List -->
                        <div class="divide-y divide-stone-100 my-2">
                            @foreach($order->items as $item)
                                <div class="py-2 text-xs">
                                    <div class="flex justify-between font-bold text-stone-900">
                                        <span>{{ $item->quantity }}x {{ $item->menu->name }}</span>
                                    </div>
                                    @if($item->note)
                                        <p class="text-[11px] font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 mt-1">
                                            ⚠️ "{{ $item->note }}"
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if($order->note)
                            <div class="text-[11px] bg-stone-50 p-2 rounded text-stone-600 italic border border-stone-200 mb-3">
                                Catatan: "{{ $order->note }}"
                            </div>
                        @endif

                        <form action="{{ route('kitchen.orders.status', $order->order_id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="ACCEPTED">
                            <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs tracking-wide transition shadow flex items-center justify-center gap-1.5">
                                <i data-lucide="check" class="w-4 h-4"></i> Terima Pesanan (Accept)
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="text-center py-12 text-stone-400 text-xs">
                        <i data-lucide="check-check" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                        Tidak ada pesanan baru.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 2. ACCEPTED (Diterima / Antrian) -->
        <div class="bg-stone-100/70 rounded-3xl p-4 border border-stone-200 flex flex-col min-h-[600px]">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-stone-200">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <h2 class="font-black text-stone-900 text-sm uppercase tracking-wide">2. Diterima (Antrian)</h2>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-xs font-black">
                    {{ $acceptedOrders->count() }}
                </span>
            </div>

            <div class="space-y-4 flex-grow overflow-y-auto">
                @forelse($acceptedOrders as $order)
                    <div class="bg-white rounded-2xl border-2 border-blue-200 p-4 shadow-sm">
                        <div class="flex items-center justify-between pb-2 border-b border-stone-100 text-xs">
                            <span class="font-black text-stone-900 text-sm">#{{ $order->order_id }}</span>
                            <span class="px-2 py-0.5 rounded font-black text-[10px] {{ $order->order_type === 'DINE_IN' ? 'bg-amber-100 text-amber-900' : 'bg-stone-800 text-white' }}">
                                {{ $order->order_type === 'DINE_IN' ? 'Meja ' . ($order->table?->table_number ?? '-') : 'Take Away' }}
                            </span>
                        </div>

                        <div class="py-2 text-xs text-stone-500 flex justify-between">
                            <span>Pemesan: <strong>{{ $order->customer_name ?: 'Pelanggan' }}</strong></span>
                            <span>{{ $order->ordered_at->diffForHumans() }}</span>
                        </div>

                        <div class="divide-y divide-stone-100 my-2">
                            @foreach($order->items as $item)
                                <div class="py-2 text-xs">
                                    <div class="flex justify-between font-bold text-stone-900">
                                        <span>{{ $item->quantity }}x {{ $item->menu->name }}</span>
                                    </div>
                                    @if($item->note)
                                        <p class="text-[11px] font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 mt-1">
                                            ⚠️ "{{ $item->note }}"
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <form action="{{ route('kitchen.orders.status', $order->order_id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="PREPARING">
                            <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs tracking-wide transition shadow flex items-center justify-center gap-1.5">
                                <i data-lucide="flame" class="w-4 h-4"></i> Mulai Racik / Masak
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="text-center py-12 text-stone-400 text-xs">
                        <i data-lucide="coffee" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                        Antrian kosong.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 3. PREPARING (Sedang Dimasak / Diramu) -->
        <div class="bg-stone-100/70 rounded-3xl p-4 border border-stone-200 flex flex-col min-h-[600px]">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-stone-200">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                    <h2 class="font-black text-stone-900 text-sm uppercase tracking-wide">3. Sedang Dimasak</h2>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-800 text-xs font-black">
                    {{ $preparingOrders->count() }}
                </span>
            </div>

            <div class="space-y-4 flex-grow overflow-y-auto">
                @forelse($preparingOrders as $order)
                    <div class="bg-white rounded-2xl border-2 border-indigo-300 p-4 shadow-sm">
                        <div class="flex items-center justify-between pb-2 border-b border-stone-100 text-xs">
                            <span class="font-black text-stone-900 text-sm">#{{ $order->order_id }}</span>
                            <span class="px-2 py-0.5 rounded font-black text-[10px] {{ $order->order_type === 'DINE_IN' ? 'bg-amber-100 text-amber-900' : 'bg-stone-800 text-white' }}">
                                {{ $order->order_type === 'DINE_IN' ? 'Meja ' . ($order->table?->table_number ?? '-') : 'Take Away' }}
                            </span>
                        </div>

                        <div class="py-2 text-xs text-stone-500 flex justify-between">
                            <span>Pemesan: <strong>{{ $order->customer_name ?: 'Pelanggan' }}</strong></span>
                            <span class="font-bold text-indigo-600">{{ $order->ordered_at->diffForHumans() }}</span>
                        </div>

                        <div class="divide-y divide-stone-100 my-2">
                            @foreach($order->items as $item)
                                <div class="py-2 text-xs">
                                    <div class="flex justify-between font-bold text-stone-900">
                                        <span>{{ $item->quantity }}x {{ $item->menu->name }}</span>
                                    </div>
                                    @if($item->note)
                                        <p class="text-[11px] font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 mt-1">
                                            ⚠️ "{{ $item->note }}"
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <form action="{{ route('kitchen.orders.status', $order->order_id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="READY">
                            <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs tracking-wide transition shadow flex items-center justify-center gap-1.5">
                                <i data-lucide="bell" class="w-4 h-4"></i> Siap Disajikan (Ready!)
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="text-center py-12 text-stone-400 text-xs">
                        <i data-lucide="utensils" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                        Tidak ada yang sedang dimasak.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 4. READY (Siap Saji / Ambil) -->
        <div class="bg-stone-100/70 rounded-3xl p-4 border border-stone-200 flex flex-col min-h-[600px]">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-stone-200">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                    <h2 class="font-black text-stone-900 text-sm uppercase tracking-wide">4. Siap Disajikan</h2>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-xs font-black">
                    {{ $readyOrders->count() }}
                </span>
            </div>

            <div class="space-y-4 flex-grow overflow-y-auto">
                @forelse($readyOrders as $order)
                    <div class="bg-white rounded-2xl border-2 border-purple-300 p-4 shadow-sm">
                        <div class="flex items-center justify-between pb-2 border-b border-stone-100 text-xs">
                            <span class="font-black text-stone-900 text-sm">#{{ $order->order_id }}</span>
                            <span class="px-2 py-0.5 rounded font-black text-[10px] {{ $order->order_type === 'DINE_IN' ? 'bg-amber-100 text-amber-900' : 'bg-stone-800 text-white' }}">
                                {{ $order->order_type === 'DINE_IN' ? 'Meja ' . ($order->table?->table_number ?? '-') : 'Take Away' }}
                            </span>
                        </div>

                        <div class="py-2 text-xs text-stone-500 flex justify-between">
                            <span>Pemesan: <strong>{{ $order->customer_name ?: 'Pelanggan' }}</strong></span>
                            <span class="text-purple-600 font-bold">Siap diantar/pikap</span>
                        </div>

                        <div class="divide-y divide-stone-100 my-2">
                            @foreach($order->items as $item)
                                <div class="py-1 text-xs text-stone-700">
                                    {{ $item->quantity }}x {{ $item->menu->name }}
                                </div>
                            @endforeach
                        </div>

                        <form action="{{ route('kitchen.orders.status', $order->order_id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="COMPLETED">
                            <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs tracking-wide transition shadow flex items-center justify-center gap-1.5">
                                <i data-lucide="check-circle" class="w-4 h-4"></i> Konfirmasi Selesai
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="text-center py-12 text-stone-400 text-xs">
                        <i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                        Tidak ada pesanan menunggu diantar.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

