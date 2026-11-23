@extends('layouts.app')

@section('title', 'Checkout Pesanan')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ orderType: 'DINE_IN', paymentMethod: 'CASH' }">
    
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-black text-stone-900 tracking-tight">Checkout Pesanan</h1>
        <p class="text-sm text-stone-500 mt-1">Lengkapi data meja dan metode pembayaran untuk memproses pesanan</p>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('customer.order.place') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Checkout Options -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- 1. Order Type (Dine In vs Take Away) -->
                <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm">
                    <h3 class="font-bold text-stone-900 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i data-lucide="utensils" class="w-4 h-4 text-brand-700"></i>
                        <span>1. Tipe Pemesanan</span>
                    </h3>

                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer border-2 rounded-xl p-4 flex flex-col items-center justify-center transition"
                            :class="orderType === 'DINE_IN' ? 'border-brand-700 bg-brand-50 text-brand-900' : 'border-stone-200 hover:bg-stone-50 text-stone-600'">
                            <input type="radio" name="order_type" value="DINE_IN" x-model="orderType" class="sr-only">
                            <i data-lucide="coffee" class="w-6 h-6 mb-2" :class="orderType === 'DINE_IN' ? 'text-brand-700' : 'text-stone-400'"></i>
                            <span class="font-bold text-sm">Dine In (Makan di Tempat)</span>
                            <span class="text-[11px] text-stone-400 mt-0.5">Pilih nomor meja kafe</span>
                        </label>

                        <label class="cursor-pointer border-2 rounded-xl p-4 flex flex-col items-center justify-center transition"
                            :class="orderType === 'TAKE_AWAY' ? 'border-brand-700 bg-brand-50 text-brand-900' : 'border-stone-200 hover:bg-stone-50 text-stone-600'">
                            <input type="radio" name="order_type" value="TAKE_AWAY" x-model="orderType" class="sr-only">
                            <i data-lucide="shopping-bag" class="w-6 h-6 mb-2" :class="orderType === 'TAKE_AWAY' ? 'text-brand-700' : 'text-stone-400'"></i>
                            <span class="font-bold text-sm">Take Away (Bungkus)</span>
                            <span class="text-[11px] text-stone-400 mt-0.5">Bawa pulang / pikap</span>
                        </label>
                    </div>

                    <!-- Table Selection (Only for Dine In) -->
                    <div x-show="orderType === 'DINE_IN'" x-cloak class="mt-5 pt-5 border-t border-stone-100">
                        <label for="table_id" class="block text-xs font-bold text-stone-700 mb-2">
                            Pilih Nomor Meja Kafe <span class="text-red-500">*</span>
                        </label>
                        
                        @if($tables->isEmpty())
                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800">
                                Saat ini semua meja sedang penuh (Occupied). Anda dapat memesan sebagai Take Away atau menunggu meja tersedia.
                            </div>
                        @else
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                @foreach($tables as $t)
                                    <label class="relative flex flex-col items-center p-3 rounded-xl border border-stone-200 cursor-pointer hover:border-brand-600 hover:bg-brand-50/50 transition">
                                        <input type="radio" name="table_id" value="{{ $t->table_id }}" {{ (old('table_id') == $t->table_id || $preselectedTable == $t->table_number) ? 'checked' : '' }} required class="accent-brand-700">
                                        <span class="font-black text-stone-800 text-sm mt-1">{{ $t->table_number }}</span>
                                        <span class="text-[10px] text-stone-400">Kapasitas {{ $t->capacity }} orang</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 2. Customer Information & Notes -->
                <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm">
                    <h3 class="font-bold text-stone-900 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i data-lucide="user" class="w-4 h-4 text-brand-700"></i>
                        <span>2. Informasi Pemesan</span>
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label for="customer_name" class="block text-xs font-bold text-stone-700 mb-1">
                                Nama Pemesan (Untuk Dipanggil Barista/Kasir)
                            </label>
                            <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}"
                                placeholder="Contoh: Dimas, Kak Clarissa, dll."
                                class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-brand-600 text-sm">
                        </div>

                        <div>
                            <label for="note" class="block text-xs font-bold text-stone-700 mb-1">
                                Catatan Pesanan Keseluruhan (Opsional)
                            </label>
                            <textarea name="note" id="note" rows="2"
                                placeholder="Contoh: Tolong disajikan bersamaan, jangan terlalu manis, dll."
                                class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-brand-600 text-sm">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- 3. Payment Method -->
                <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm">
                    <h3 class="font-bold text-stone-900 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i data-lucide="credit-card" class="w-4 h-4 text-brand-700"></i>
                        <span>3. Metode Pembayaran</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="cursor-pointer border-2 rounded-xl p-4 flex flex-col items-center justify-center transition text-center"
                            :class="paymentMethod === 'CASH' ? 'border-brand-700 bg-brand-50 text-brand-900' : 'border-stone-200 hover:bg-stone-50 text-stone-600'">
                            <input type="radio" name="payment_method" value="CASH" x-model="paymentMethod" class="sr-only">
                            <i data-lucide="banknote" class="w-6 h-6 mb-1 text-emerald-600"></i>
                            <span class="font-bold text-sm">Tunai di Kasir</span>
                            <span class="text-[10px] text-stone-400 mt-0.5">Bayar tunai di meja kasir</span>
                        </label>

                        <label class="cursor-pointer border-2 rounded-xl p-4 flex flex-col items-center justify-center transition text-center"
                            :class="paymentMethod === 'QRIS' ? 'border-brand-700 bg-brand-50 text-brand-900' : 'border-stone-200 hover:bg-stone-50 text-stone-600'">
                            <input type="radio" name="payment_method" value="QRIS" x-model="paymentMethod" class="sr-only">
                            <i data-lucide="qr-code" class="w-6 h-6 mb-1 text-blue-600"></i>
                            <span class="font-bold text-sm">QRIS Statis/Dinamis</span>
                            <span class="text-[10px] text-stone-400 mt-0.5">Gopay, OVO, Shopee, BCA</span>
                        </label>

                        <label class="cursor-pointer border-2 rounded-xl p-4 flex flex-col items-center justify-center transition text-center"
                            :class="paymentMethod === 'TRANSFER' ? 'border-brand-700 bg-brand-50 text-brand-900' : 'border-stone-200 hover:bg-stone-50 text-stone-600'">
                            <input type="radio" name="payment_method" value="TRANSFER" x-model="paymentMethod" class="sr-only">
                            <i data-lucide="smartphone" class="w-6 h-6 mb-1 text-purple-600"></i>
                            <span class="font-bold text-sm">Bank Transfer</span>
                            <span class="text-[10px] text-stone-400 mt-0.5">BCA / Mandiri / BRI</span>
                        </label>
                    </div>

                    <div class="mt-4 p-3 rounded-xl bg-stone-50 border border-stone-200 text-xs text-stone-600 flex items-center gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-stone-400 flex-shrink-0"></i>
                        <span>Setelah checkout, kasir akan memverifikasi pembayaran Anda dan pesanan langsung diproses di dapur.</span>
                    </div>
                </div>

            </div>

            <!-- Right Column: Order Items Summary & Confirm Button -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-sm sticky top-24">
                    <h3 class="text-base font-black text-stone-900 tracking-tight pb-3 border-b border-stone-100 flex items-center justify-between">
                        <span>Ringkasan Item</span>
                        <a href="{{ route('customer.cart') }}" class="text-xs font-semibold text-brand-700 hover:underline">Ubah</a>
                    </h3>

                    <!-- Mini items list -->
                    <div class="divide-y divide-stone-100 max-h-60 overflow-y-auto my-3 pr-1">
                        @foreach($cart->items as $item)
                            <div class="py-2.5 flex items-center justify-between text-xs">
                                <div>
                                    <p class="font-bold text-stone-800">{{ $item->menu->name }}</p>
                                    <p class="text-stone-400 text-[11px]">{{ $item->quantity }}x @ Rp {{ number_format($item->menu->price, 0, ',', '.') }}</p>
                                    @if($item->note)
                                        <p class="text-[10px] text-amber-700 italic">"{{ $item->note }}"</p>
                                    @endif
                                </div>
                                <span class="font-extrabold text-stone-900">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-3 border-t border-stone-100 space-y-2 text-xs text-stone-600">
                        <div class="flex justify-between">
                            <span>Subtotal Menu</span>
                            <span class="font-bold text-stone-800">Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Biaya Layanan</span>
                            <span class="font-bold text-emerald-600">Gratis (Rp 0)</span>
                        </div>
                        <div class="border-t border-stone-100 pt-3 flex justify-between items-center text-stone-900">
                            <span class="text-sm font-bold">Total Tagihan</span>
                            <span class="text-xl font-black text-brand-700">Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-6 py-4 px-4 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold text-sm tracking-wide transition shadow-lg hover:shadow-xl flex items-center justify-center gap-2 active:scale-95">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        <span>Konfirmasi Pesanan Sekarang</span>
                    </button>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection

