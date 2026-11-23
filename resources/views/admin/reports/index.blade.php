@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-stone-500 hover:text-stone-800">Admin Dashboard</a>
                <span class="text-stone-300">/</span>
                <span class="text-xs font-bold text-brand-700">DFD 6.0 Buat Laporan Penjualan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-stone-900 tracking-tight mt-1">Laporan Penjualan Kafe</h1>
        </div>

        <a href="{{ route('admin.reports.print', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank"
           class="px-4 py-2.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold transition shadow flex items-center gap-1.5">
            <i data-lucide="printer" class="w-4 h-4"></i> Cetak Laporan Resmi
        </a>
    </div>

    <!-- Date Range Filter Card -->
    <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-sm mb-8">
        <form action="{{ route('admin.reports') }}" method="GET" class="flex flex-wrap items-end gap-4 text-xs">
            <div>
                <label class="block font-bold text-stone-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" required
                    class="px-3.5 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600 font-medium">
            </div>

            <div>
                <label class="block font-bold text-stone-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" required
                    class="px-3.5 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600 font-medium">
            </div>

            <button type="submit" class="px-5 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold transition shadow-sm flex items-center gap-1.5">
                <i data-lucide="filter" class="w-3.5 h-3.5"></i> Terapkan Filter
            </button>

            <!-- Quick range shortcuts -->
            <div class="flex items-center gap-1.5 ml-auto">
                <a href="{{ route('admin.reports', ['start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}"
                   class="px-3 py-2 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold transition">
                    Hari Ini
                </a>
                <a href="{{ route('admin.reports', ['start_date' => date('Y-m-01'), 'end_date' => date('Y-m-d')]) }}"
                   class="px-3 py-2 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold transition">
                    Bulan Ini
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-sm">
            <span class="text-xs font-bold text-stone-400 uppercase tracking-wider block">Total Omset Penjualan</span>
            <span class="text-2xl font-black text-emerald-600 mt-1 block">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
            <span class="text-[11px] text-stone-400 mt-1 block">Berdasarkan pesanan LUNAS</span>
        </div>

        <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-sm">
            <span class="text-xs font-bold text-stone-400 uppercase tracking-wider block">Jumlah Transaksi Selesai</span>
            <span class="text-2xl font-black text-blue-600 mt-1 block">{{ $totalOrdersCount }}</span>
            <span class="text-[11px] text-stone-400 mt-1 block">Pesanan terbayar</span>
        </div>

        <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-sm">
            <span class="text-xs font-bold text-stone-400 uppercase tracking-wider block">Rata-rata Transaksi (AOV)</span>
            <span class="text-2xl font-black text-purple-600 mt-1 block">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</span>
            <span class="text-[11px] text-stone-400 mt-1 block">Average Order Value</span>
        </div>

        <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-sm">
            <span class="text-xs font-bold text-stone-400 uppercase tracking-wider block">Total Porsi Terjual</span>
            <span class="text-2xl font-black text-amber-600 mt-1 block">{{ $totalItemsSold ?: 0 }}</span>
            <span class="text-[11px] text-stone-400 mt-1 block">Makanan & Minuman</span>
        </div>
    </div>

    <!-- Breakdown Cards (Payment & Order Type) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        
        <!-- Payment Methods Breakdown -->
        <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-sm">
            <h3 class="font-bold text-stone-900 text-sm uppercase tracking-wider mb-4 flex items-center justify-between">
                <span>Rincian Berdasarkan Metode Bayar</span>
                <i data-lucide="credit-card" class="w-4 h-4 text-stone-400"></i>
            </h3>

            <div class="space-y-3 text-xs">
                <div>
                    <div class="flex justify-between font-bold mb-1">
                        <span class="text-stone-700">Tunai / Cash:</span>
                        <span class="text-stone-900">Rp {{ number_format($cashTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-stone-100 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $totalRevenue > 0 ? ($cashTotal / $totalRevenue) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between font-bold mb-1">
                        <span class="text-stone-700">QRIS:</span>
                        <span class="text-stone-900">Rp {{ number_format($qrisTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-stone-100 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $totalRevenue > 0 ? ($qrisTotal / $totalRevenue) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between font-bold mb-1">
                        <span class="text-stone-700">Bank Transfer:</span>
                        <span class="text-stone-900">Rp {{ number_format($transferTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-stone-100 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $totalRevenue > 0 ? ($transferTotal / $totalRevenue) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dine In vs Take Away Breakdown -->
        <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-sm">
            <h3 class="font-bold text-stone-900 text-sm uppercase tracking-wider mb-4 flex items-center justify-between">
                <span>Rincian Tipe Pemesanan</span>
                <i data-lucide="utensils" class="w-4 h-4 text-stone-400"></i>
            </h3>

            <div class="grid grid-cols-2 gap-4 text-center my-3">
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200">
                    <span class="text-xs font-bold text-amber-800 block">Dine In (Makan di Tempat)</span>
                    <span class="text-2xl font-black text-amber-900 mt-1 block">{{ $dineInCount }}</span>
                    <span class="text-[10px] text-amber-700 font-semibold">{{ $totalOrdersCount > 0 ? round(($dineInCount / $totalOrdersCount) * 100) : 0 }}% dari total</span>
                </div>

                <div class="p-4 rounded-xl bg-stone-100 border border-stone-200">
                    <span class="text-xs font-bold text-stone-700 block">Take Away (Bungkus)</span>
                    <span class="text-2xl font-black text-stone-900 mt-1 block">{{ $takeAwayCount }}</span>
                    <span class="text-[10px] text-stone-500 font-semibold">{{ $totalOrdersCount > 0 ? round(($takeAwayCount / $totalOrdersCount) * 100) : 0 }}% dari total</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Top Selling Menu Ranking Table -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden mb-8">
        <div class="p-5 border-b border-stone-100 flex items-center justify-between">
            <h3 class="font-bold text-stone-900 text-sm uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="award" class="w-4 h-4 text-amber-500"></i>
                <span>Peringkat 10 Menu Paling Laris</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 uppercase tracking-wider font-bold">
                    <tr>
                        <th class="py-3 px-4">Rank</th>
                        <th class="py-3 px-4">Nama Menu</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Harga Satuan</th>
                        <th class="py-3 px-4">Porsi Terjual</th>
                        <th class="py-3 px-4 text-right">Kontribusi Penjualan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($topItems as $index => $item)
                        <tr class="hover:bg-stone-50/70 transition">
                            <td class="py-3 px-4 font-black text-stone-900">#{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-bold text-stone-900">{{ $item->name }}</td>
                            <td class="py-3 px-4 text-stone-500">{{ $item->category_name }}</td>
                            <td class="py-3 px-4">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 font-black text-brand-700">{{ $item->total_quantity }} porsi</td>
                            <td class="py-3 px-4 font-black text-emerald-600 text-right">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-stone-400">Belum ada item terjual pada rentang tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detailed Orders Log Table -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-stone-100">
            <h3 class="font-bold text-stone-900 text-sm uppercase tracking-wider">
                Detail Transaksi Penjualan
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 uppercase tracking-wider font-bold">
                    <tr>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">No. Order</th>
                        <th class="py-3 px-4">No. Struk</th>
                        <th class="py-3 px-4">Pelanggan & Meja</th>
                        <th class="py-3 px-4">Metode Bayar</th>
                        <th class="py-3 px-4">Kasir Konfirmasi</th>
                        <th class="py-3 px-4 text-right">Total Transaksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($orders as $ord)
                        <tr class="hover:bg-stone-50/70 transition">
                            <td class="py-3 px-4 text-stone-400 whitespace-nowrap">{{ $ord->ordered_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 font-bold text-stone-900">#{{ $ord->order_id }}</td>
                            <td class="py-3 px-4 font-mono text-[11px] text-brand-700">
                                {{ $ord->payment?->receipt?->receipt_number ?? '-' }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-stone-800">{{ $ord->customer_name ?: 'Pelanggan' }}</span>
                                <span class="text-[10px] text-stone-400 block">
                                    {{ $ord->order_type === 'DINE_IN' ? 'Meja ' . ($ord->table?->table_number ?? '-') : 'Take Away' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-stone-100 text-stone-800">
                                    {{ $ord->payment?->method ?? 'CASH' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-stone-500">
                                {{ $ord->payment?->confirmedByStaff?->name ?? 'Kasir' }}
                            </td>
                            <td class="py-3 px-4 font-black text-stone-900 text-right whitespace-nowrap">
                                Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-stone-400">
                                Tidak ada transaksi penjualan pada rentang tanggal yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

