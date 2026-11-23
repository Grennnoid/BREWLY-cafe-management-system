@extends('layouts.app')

@section('title', 'Menu & Pemesanan')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <!-- Hero Banner with Table Indicator -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-brand-900 via-brand-800 to-stone-900 text-white p-6 sm:p-10 shadow-lg mb-8">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-400/20 text-amber-300 border border-amber-300/30 mb-3">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> Selamat Datang di Brewly Cafe
            </span>
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight">
                Pesan Menu Favorit Anda dari Meja atau Take Away
            </h1>
            <p class="text-stone-300 text-sm sm:text-base mt-2">
                Nikmati racikan espresso artisanal, minuman segar, dan hidangan lezat langsung dipersiapkan oleh barista dan chef kami.
            </p>

            <!-- Table Quick Selector / Badge -->
            <div class="mt-6 flex flex-wrap items-center gap-3">
                <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-xs">
                    <i data-lucide="map-pin" class="w-4 h-4 text-amber-400"></i>
                    <span>Pilihan Meja:</span>
                    <strong class="text-amber-300">{{ $selectedTable ? 'Meja ' . $selectedTable : 'Dine In / Take Away (Pilih saat Checkout)' }}</strong>
                </div>

                <a href="{{ route('customer.cart') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-stone-950 font-bold text-xs tracking-wide transition shadow-md">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                    <span>Lihat Keranjang ({{ $cart->total_count }} item)</span>
                </a>
            </div>
        </div>

        <div class="absolute -right-10 -bottom-10 opacity-15 pointer-events-none hidden md:block">
            <i data-lucide="coffee" class="w-80 h-80 text-amber-100"></i>
        </div>
    </div>

    <!-- Search & Category Filters -->
    <div class="mb-8 space-y-4">
        <!-- Search bar -->
        <form action="{{ route('home') }}" method="GET" class="flex gap-2 max-w-md">
            @if($selectedCategory)
                <input type="hidden" name="category" value="{{ $selectedCategory }}">
            @endif
            <div class="relative flex-grow">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-stone-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama kopi, pastry, makanan..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-stone-200 bg-white focus:outline-none focus:ring-2 focus:ring-brand-600 focus:border-brand-600 text-sm shadow-sm">
            </div>
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-brand-700 text-white font-medium text-sm hover:bg-brand-800 transition shadow-sm">
                Cari
            </button>
            @if($search || $selectedCategory)
                <a href="{{ route('home') }}" class="px-3 py-2.5 rounded-xl bg-stone-200 text-stone-700 font-medium text-sm hover:bg-stone-300 transition" title="Reset filter">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
            @endif
        </form>

        <!-- Category Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
            <a href="{{ route('home', ['search' => $search]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition shadow-sm {{ empty($selectedCategory) ? 'bg-brand-800 text-white ring-2 ring-brand-800' : 'bg-white text-stone-600 hover:bg-stone-100 border border-stone-200' }}">
                Semua Kategori
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('home', ['category' => $cat->category_id, 'search' => $search]) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition shadow-sm {{ $selectedCategory == $cat->category_id ? 'bg-brand-800 text-white ring-2 ring-brand-800' : 'bg-white text-stone-600 hover:bg-stone-100 border border-stone-200' }}">
                    {{ $cat->name }} ({{ $cat->menus_count }})
                </a>
            @endforeach
        </div>
    </div>

    <!-- Menus Grid -->
    @if($menus->isEmpty())
        <div class="bg-white rounded-2xl border border-stone-200 p-12 text-center max-w-md mx-auto my-12">
            <div class="w-16 h-16 rounded-full bg-stone-100 text-stone-400 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="search-x" class="w-8 h-8"></i>
            </div>
            <h3 class="font-bold text-stone-800 text-lg">Menu Tidak Ditemukan</h3>
            <p class="text-sm text-stone-500 mt-1">Coba gunakan kata kunci lain atau pilih kategori yang berbeda.</p>
            <a href="{{ route('home') }}" class="mt-4 inline-block px-4 py-2 rounded-xl bg-brand-700 text-white text-xs font-bold">Lihat Semua Menu</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($menus as $menu)
                <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-sm hover:shadow-md transition flex flex-col group {{ !$menu->is_available ? 'opacity-70 grayscale-[30%]' : '' }}">
                    
                    <!-- Menu Image -->
                    <div class="relative h-48 w-full bg-stone-100 overflow-hidden">
                        <img src="{{ $menu->image_url ?: 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=600' }}"
                             alt="{{ $menu->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        
                        <div class="absolute top-3 left-3">
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold tracking-wide uppercase shadow-sm bg-white/90 backdrop-blur-sm text-brand-800">
                                {{ $menu->category->name }}
                            </span>
                        </div>

                        <div class="absolute top-3 right-3">
                            @if($menu->is_available)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-500 text-white shadow-sm">
                                    Tersedia
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-stone-700 text-stone-200 shadow-sm">
                                    Habis
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Menu Info -->
                    <div class="p-5 flex-grow flex flex-col justify-between">
                        <div>
                            <h3 class="font-bold text-stone-900 text-base group-hover:text-brand-700 transition leading-snug">
                                {{ $menu->name }}
                            </h3>
                            <p class="text-xs text-stone-500 mt-1.5 line-clamp-2 leading-relaxed">
                                {{ $menu->description }}
                            </p>
                        </div>

                        <div class="mt-4 pt-4 border-t border-stone-100 flex items-center justify-between">
                            <div>
                                <span class="text-[11px] text-stone-400 uppercase font-semibold">Harga</span>
                                <p class="text-base font-extrabold text-stone-900">
                                    Rp {{ number_format($menu->price, 0, ',', '.') }}
                                </p>
                            </div>

                            @if($menu->is_available)
                                <!-- Add to Cart Button with Alpine Modal for Note -->
                                <div x-data="{ open: false }">
                                    <button @click="open = true" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold tracking-wide transition shadow-sm active:scale-95">
                                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                        <span>Tambah</span>
                                    </button>

                                    <!-- Modal Add with Note -->
                                    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-sm">
                                        <div @click.outside="open = false" class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 animate-scale-up">
                                            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                                                <h4 class="font-bold text-stone-900 text-base">Tambah ke Keranjang</h4>
                                                <button @click="open = false" class="text-stone-400 hover:text-stone-600">
                                                    <i data-lucide="x" class="w-5 h-5"></i>
                                                </button>
                                            </div>

                                            <form action="{{ route('customer.cart.add') }}" method="POST" class="mt-4 space-y-4">
                                                @csrf
                                                <input type="hidden" name="menu_id" value="{{ $menu->menu_id }}">

                                                <div class="flex items-center gap-3">
                                                    <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" class="w-14 h-14 rounded-xl object-cover">
                                                    <div>
                                                        <h5 class="font-bold text-stone-800 text-sm">{{ $menu->name }}</h5>
                                                        <p class="text-xs font-extrabold text-brand-700">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-stone-700 mb-1">Jumlah</label>
                                                    <input type="number" name="quantity" value="1" min="1" max="50" required
                                                        class="w-24 px-3 py-2 rounded-xl border border-stone-300 text-sm text-center font-bold focus:ring-2 focus:ring-brand-600">
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-stone-700 mb-1">Catatan Khusus (Opsional)</label>
                                                    <input type="text" name="note" placeholder="Contoh: Less ice, gula sedikit, pisah sambal..."
                                                        class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs focus:ring-2 focus:ring-brand-600">
                                                </div>

                                                <div class="flex items-center justify-end gap-2 pt-2">
                                                    <button type="button" @click="open = false" class="px-4 py-2 rounded-xl border border-stone-200 text-stone-600 text-xs font-bold hover:bg-stone-50">
                                                        Batal
                                                    </button>
                                                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold shadow-md">
                                                        Masukkan Keranjang
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="px-3 py-1.5 rounded-xl bg-stone-100 text-stone-400 text-xs font-medium cursor-not-allowed">
                                    Habis
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Floating Bottom Cart Bar (if cart has items) -->
    @if($cart->total_count > 0)
        <div class="fixed bottom-4 left-4 right-4 max-w-2xl mx-auto z-30 no-print animate-bounce-subtle">
            <div class="bg-stone-900 text-white rounded-2xl p-4 shadow-2xl flex items-center justify-between border border-stone-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-stone-950 flex items-center justify-center font-bold text-sm shadow">
                        {{ $cart->total_count }}
                    </div>
                    <div>
                        <p class="text-xs text-stone-400">Total Keranjang</p>
                        <p class="text-base font-black text-white">Rp {{ number_format($cart->total, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('customer.cart') }}" class="px-4 py-2.5 rounded-xl bg-stone-800 hover:bg-stone-700 text-xs font-bold text-stone-200 transition">
                        Lihat Keranjang
                    </a>
                    <a href="{{ route('customer.checkout') }}" class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-stone-950 text-xs font-black tracking-wide transition shadow">
                        Checkout & Bayar &rarr;
                    </a>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

