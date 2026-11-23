@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-stone-900 tracking-tight">Keranjang Pesanan</h1>
            <p class="text-sm text-stone-500 mt-1">Periksa kembali pesanan Anda sebelum melakukan checkout</p>
        </div>
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-700 hover:text-brand-800">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Tambah Menu Lain
        </a>
    </div>

    @if($cart->items->isEmpty())
        <div class="bg-white rounded-3xl border border-stone-200 p-12 text-center my-8 shadow-sm">
            <div class="w-20 h-20 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="shopping-bag" class="w-10 h-10"></i>
            </div>
            <h3 class="text-xl font-bold text-stone-900">Keranjang Masih Kosong</h3>
            <p class="text-sm text-stone-500 max-w-sm mx-auto mt-2">
                Anda belum memilih menu lezat apa pun. Jelajahi menu kami dan tambahkan minuman atau makanan favorit Anda.
            </p>
            <a href="{{ route('home') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold text-sm transition shadow-md">
                <i data-lucide="coffee" class="w-4 h-4"></i>
                <span>Lihat Daftar Menu</span>
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Items List -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($cart->items as $item)
                    <div class="bg-white rounded-2xl border border-stone-200 p-4 sm:p-5 shadow-sm flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                        
                        <div class="flex items-center gap-4">
                            <img src="{{ $item->menu->image_url ?: 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=300' }}"
                                 alt="{{ $item->menu->name }}"
                                 class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl object-cover border border-stone-100 flex-shrink-0">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-brand-600 bg-brand-50 px-2 py-0.5 rounded-md">
                                    {{ $item->menu->category->name }}
                                </span>
                                <h3 class="font-bold text-stone-900 text-base mt-1">{{ $item->menu->name }}</h3>
                                <p class="text-xs text-stone-500 font-semibold mt-0.5">
                                    Rp {{ number_format($item->menu->price, 0, ',', '.') }}
                                </p>
                                @if($item->note)
                                    <p class="text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 mt-1.5 inline-block">
                                        <i data-lucide="sticky-note" class="w-3 h-3 inline mr-1"></i>{{ $item->note }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Controls -->
                        <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-4 pt-3 sm:pt-0 border-t sm:border-t-0 border-stone-100">
                            
                            <!-- Quantity +/- Form -->
                            <div class="flex items-center border border-stone-200 rounded-xl overflow-hidden bg-stone-50">
                                <form action="{{ route('customer.cart.update', $item->cart_item_id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="decrease">
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center text-stone-600 hover:bg-stone-200 font-bold transition">
                                        -
                                    </button>
                                </form>
                                <span class="w-9 text-center text-xs font-bold text-stone-800">
                                    {{ $item->quantity }}
                                </span>
                                <form action="{{ route('customer.cart.update', $item->cart_item_id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="increase">
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center text-stone-600 hover:bg-stone-200 font-bold transition">
                                        +
                                    </button>
                                </form>
                            </div>

                            <!-- Subtotal -->
                            <div class="text-right min-w-[90px]">
                                <span class="text-[10px] text-stone-400 font-semibold block uppercase">Subtotal</span>
                                <span class="text-sm font-black text-stone-900">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- Delete button -->
                            <form action="{{ route('customer.cart.remove', $item->cart_item_id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-stone-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>

                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Summary & Checkout Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-sm sticky top-24">
                    <h3 class="text-lg font-black text-stone-900 tracking-tight pb-3 border-b border-stone-100">
                        Ringkasan Pesanan
                    </h3>

                    <div class="py-4 space-y-2.5 text-xs text-stone-600">
                        <div class="flex justify-between">
                            <span>Total Item</span>
                            <span class="font-bold text-stone-800">{{ $cart->total_count }} item</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Subtotal Menu</span>
                            <span class="font-bold text-stone-800">Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-emerald-600 font-semibold">
                            <span>Pajak Restoran & Biaya Layanan</span>
                            <span>Termasuk (Rp 0)</span>
                        </div>
                        <div class="border-t border-stone-100 pt-3 flex justify-between items-center text-stone-900">
                            <span class="text-sm font-bold">Total Pembayaran</span>
                            <span class="text-xl font-black text-brand-700">Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('customer.checkout') }}" class="w-full mt-4 py-3.5 px-4 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold text-sm tracking-wide transition shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <span>Lanjut ke Pembayaran</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>

                    <div class="mt-4 pt-4 border-t border-stone-100 text-[11px] text-stone-400 text-center flex items-center justify-center gap-1.5">
                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-500"></i>
                        <span>Pesanan terintegrasi langsung dengan Kasir & Dapur</span>
                    </div>
                </div>
            </div>

        </div>
    @endif

</div>
@endsection

