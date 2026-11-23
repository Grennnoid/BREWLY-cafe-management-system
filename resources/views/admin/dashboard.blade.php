@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header & Admin Quick Nav -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-xs font-black uppercase tracking-wider">
                    Administrator Panel
                </span>
                <span class="text-xs text-stone-500">Selamat datang, {{ auth()->user()->name }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-stone-900 tracking-tight mt-1">
                Dashboard Manajemen Kafe
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-2 text-xs font-bold">
            <a href="{{ route('admin.menus') }}" class="px-3.5 py-2 rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 transition flex items-center gap-1.5 shadow-sm">
                <i data-lucide="coffee" class="w-4 h-4 text-brand-700"></i> Kelola Menu
            </a>
            <a href="{{ route('admin.tables') }}" class="px-3.5 py-2 rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 transition flex items-center gap-1.5 shadow-sm">
                <i data-lucide="layout-grid" class="w-4 h-4 text-amber-700"></i> Meja Kafe
            </a>
            <a href="{{ route('admin.staff') }}" class="px-3.5 py-2 rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 transition flex items-center gap-1.5 shadow-sm">
                <i data-lucide="users" class="w-4 h-4 text-blue-700"></i> Kelola Staf
            </a>
            <a href="{{ route('admin.reports') }}" class="px-4 py-2 rounded-xl bg-brand-800 text-white hover:bg-brand-900 transition flex items-center gap-1.5 shadow-sm">
                <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Laporan Penjualan
            </a>
        </div>
    </div>

    <!-- 4 Key Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        
        <!-- Total Gross Revenue -->
        <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-sm">
            <div class="flex items-center justify-between text-stone-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Total Pendapatan</span>
                <div class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-stone-900">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-stone-400 mt-1">
                Hari ini: <strong class="text-emerald-700">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</strong>
            </p>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-sm">
            <div class="flex items-center justify-between text-stone-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Total Pesanan</span>
                <div class="p-2 rounded-xl bg-blue-50 text-blue-600">
                    <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-stone-900">
                {{ $totalOrders }} <span class="text-xs font-normal text-stone-400">pesanan</span>
            </p>
            <p class="text-[11px] text-stone-400 mt-1">
                Hari ini: <strong class="text-blue-700">{{ $todayOrders }} pesanan</strong>
            </p>
        </div>

        <!-- Active Orders in Kitchen/Bar -->
        <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-sm">
            <div class="flex items-center justify-between text-stone-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Pesanan Aktif</span>
                <div class="p-2 rounded-xl bg-amber-50 text-amber-600">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-stone-900">
                {{ $activeOrders }} <span class="text-xs font-normal text-stone-400">sedang diproses</span>
            </p>
            <p class="text-[11px] text-stone-400 mt-1">
                Selesai: <strong class="text-stone-700">{{ $completedOrders }}</strong>
            </p>
        </div>

        <!-- Tables & Staff Info -->
        <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-sm">
            <div class="flex items-center justify-between text-stone-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Kapasitas & Meja</span>
                <div class="p-2 rounded-xl bg-purple-50 text-purple-600">
                    <i data-lucide="layout-grid" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-stone-900">
                {{ $occupiedTables }} / {{ $totalTables }} <span class="text-xs font-normal text-stone-400">meja terisi</span>
            </p>
            <p class="text-[11px] text-stone-400 mt-1">
                Total Staf Aktif: <strong class="text-purple-700">{{ $totalStaff }} orang</strong>
            </p>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Recent Orders Table (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-stone-900 text-base">Pesanan Terbaru</h3>
                    <p class="text-xs text-stone-400">8 pesanan terakhir yang masuk ke sistem</p>
                </div>
                <a href="{{ route('cashier.orders') }}" class="text-xs font-bold text-brand-700 hover:underline">
                    Lihat Semua di Kasir &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 uppercase tracking-wider font-bold">
                        <tr>
                            <th class="py-3 px-4">Order</th>
                            <th class="py-3 px-4">Pelanggan</th>
                            <th class="py-3 px-4">Tipe</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-stone-700">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-stone-50/70 transition">
                                <td class="py-3 px-4 font-black text-stone-900">#{{ $order->order_id }}</td>
                                <td class="py-3 px-4 font-bold text-stone-800">{{ $order->customer_name ?: 'Pelanggan' }}</td>
                                <td class="py-3 px-4">
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded {{ $order->order_type === 'DINE_IN' ? 'bg-amber-50 text-amber-800' : 'bg-stone-100 text-stone-600' }}">
                                        {{ $order->order_type === 'DINE_IN' ? 'Meja ' . ($order->table?->table_number ?? '-') : 'Take Away' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-black text-stone-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase border {{ $order->status_badge_class }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-bold text-[10px] {{ $order->payment?->status === 'PAID' ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ $order->payment?->status ?? 'PENDING' }} ({{ $order->payment?->method ?? 'CASH' }})
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-stone-400">Belum ada pesanan terbaru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Top 5 Best Sellers (1 Col) -->
        <div class="lg:col-span-1 space-y-6">
            
            <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-sm">
                <h3 class="font-bold text-stone-900 text-base pb-3 border-b border-stone-100 flex items-center justify-between">
                    <span>Menu Terlaris</span>
                    <i data-lucide="flame" class="w-4 h-4 text-amber-500"></i>
                </h3>

                <div class="divide-y divide-stone-100 mt-2">
                    @forelse($topMenus as $index => $item)
                        <div class="py-3 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-brand-50 text-brand-800 flex items-center justify-center font-black text-xs">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <p class="font-bold text-stone-900">{{ $item->name }}</p>
                                    <p class="text-stone-400 text-[11px]">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="font-black text-emerald-600 block">{{ $item->total_sold }} terjual</span>
                                <span class="text-[10px] text-stone-400">Rp {{ number_format($item->total_sales, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-stone-400 py-4 text-center">Belum ada data penjualan.</p>
                    @endforelse
                </div>

                <div class="mt-4 pt-3 border-t border-stone-100 text-center">
                    <a href="{{ route('admin.reports') }}" class="text-xs font-bold text-brand-700 hover:underline">
                        Lihat Laporan Lengkap &rarr;
                    </a>
                </div>
            </div>

            <!-- System Info Widget -->
            <div class="bg-gradient-to-br from-stone-900 to-brand-950 text-white rounded-2xl p-5 shadow-sm">
                <h4 class="font-bold text-sm tracking-tight text-amber-300">Brewly Cafe System v2.0</h4>
                <p class="text-xs text-stone-300 mt-1 leading-relaxed">
                    Arsitektur sistem dibangun sesuai spesifikasi DFD Level 0 & Level 1, ERD Relasional, dan Use Case Diagram.
                </p>
                <div class="mt-4 pt-3 border-t border-stone-800 grid grid-cols-2 gap-2 text-[11px]">
                    <div>
                        <span class="text-stone-400 block">Total Menu:</span>
                        <strong class="text-white">{{ $totalMenus }} items</strong>
                    </div>
                    <div>
                        <span class="text-stone-400 block">Koneksi DB:</span>
                        <strong class="text-emerald-400">SQLite (Active)</strong>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

