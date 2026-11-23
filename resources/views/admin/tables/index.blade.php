@extends('layouts.app')

@section('title', 'Admin - Kelola Meja Kafe')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ addModal: false, editModal: false, activeTable: null }">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-stone-500 hover:text-stone-800">Admin Dashboard</a>
                <span class="text-stone-300">/</span>
                <span class="text-xs font-bold text-brand-700">D7 Meja Kafe</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-stone-900 tracking-tight mt-1">Denah & Manajemen Meja Kafe</h1>
        </div>

        <button @click="addModal = true" class="px-4 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold transition shadow flex items-center gap-1.5">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Meja Baru
        </button>
    </div>

    <!-- Visual Table Floor Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-5 mb-8">
        @forelse($tables as $table)
            <div class="rounded-3xl border-2 p-5 transition flex flex-col justify-between shadow-sm {{ $table->status === 'AVAILABLE' ? 'bg-white border-emerald-300 hover:border-emerald-400' : 'bg-amber-50/50 border-amber-300 hover:border-amber-400' }}">
                
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-2xl font-black text-stone-900">{{ $table->table_number }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $table->status === 'AVAILABLE' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $table->status === 'AVAILABLE' ? 'Tersedia' : 'Terisi' }}
                        </span>
                    </div>

                    <div class="text-xs text-stone-500 space-y-1">
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="users" class="w-3.5 h-3.5 text-stone-400"></i>
                            <span>Kapasitas: <strong>{{ $table->capacity }} Orang</strong></span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="shopping-bag" class="w-3.5 h-3.5 text-stone-400"></i>
                            <span>Pesanan Aktif: <strong>{{ $table->orders_count }}</strong></span>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-3 border-t border-stone-100 flex items-center justify-between gap-2">
                    <!-- Toggle Status -->
                    <form action="{{ route('admin.tables.toggle', $table->table_id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-2.5 py-1 rounded-lg text-[10px] font-bold border transition {{ $table->status === 'AVAILABLE' ? 'bg-stone-50 border-stone-200 text-stone-700 hover:bg-stone-100' : 'bg-emerald-50 border-emerald-200 text-emerald-800 hover:bg-emerald-100' }}"
                                title="Ubah status meja">
                            {{ $table->status === 'AVAILABLE' ? 'Set Terisi' : 'Set Tersedia' }}
                        </button>
                    </form>

                    <div class="flex items-center gap-1">
                        <button @click="activeTable = {{ json_encode($table) }}; editModal = true" class="p-1 rounded text-stone-400 hover:text-stone-700 hover:bg-stone-100">
                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                        </button>
                        <form action="{{ route('admin.tables.delete', $table->table_id) }}" method="POST" onsubmit="return confirm('Hapus meja ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1 rounded text-stone-400 hover:text-red-600 hover:bg-red-50">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full text-center py-12 text-stone-400">
                Belum ada data meja kafe.
            </div>
        @endforelse
    </div>

    <!-- Add Table Modal -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-sm">
        <div @click.outside="addModal = false" class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 animate-scale-up">
            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                <h3 class="font-black text-stone-900 text-base">Tambah Meja Kafe</h3>
                <button @click="addModal = false" class="text-stone-400 hover:text-stone-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('admin.tables.store') }}" method="POST" class="mt-4 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-stone-700 mb-1">Nomor / Kode Meja <span class="text-red-500">*</span></label>
                    <input type="text" name="table_number" required placeholder="Contoh: T-09, VIP-1"
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600 uppercase font-bold">
                </div>

                <div>
                    <label class="block font-bold text-stone-700 mb-1">Kapasitas (Orang) <span class="text-red-500">*</span></label>
                    <input type="number" name="capacity" value="4" min="1" max="50" required
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600 font-bold">
                </div>

                <div>
                    <label class="block font-bold text-stone-700 mb-1">Status Awal</label>
                    <select name="status" class="w-full px-3 py-2 rounded-xl border border-stone-300 font-medium">
                        <option value="AVAILABLE">AVAILABLE (Tersedia)</option>
                        <option value="OCCUPIED">OCCUPIED (Terisi)</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-100">
                    <button type="button" @click="addModal = false" class="px-4 py-2 rounded-xl border border-stone-200 text-stone-600 font-bold hover:bg-stone-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold shadow-md">
                        Simpan Meja
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Table Modal -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-sm">
        <div @click.outside="editModal = false" class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 animate-scale-up">
            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                <h3 class="font-black text-stone-900 text-base">Edit Meja Kafe</h3>
                <button @click="editModal = false" class="text-stone-400 hover:text-stone-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <template x-if="activeTable">
                <form :action="'/admin/tables/' + activeTable.table_id" method="POST" class="mt-4 space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Nomor / Kode Meja</label>
                        <input type="text" name="table_number" x-model="activeTable.table_number" required
                            class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600 uppercase font-bold">
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Kapasitas (Orang)</label>
                        <input type="number" name="capacity" x-model="activeTable.capacity" min="1" max="50" required
                            class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600 font-bold">
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Status</label>
                        <select name="status" x-model="activeTable.status" class="w-full px-3 py-2 rounded-xl border border-stone-300 font-medium">
                            <option value="AVAILABLE">AVAILABLE (Tersedia)</option>
                            <option value="OCCUPIED">OCCUPIED (Terisi)</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-100">
                        <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl border border-stone-200 text-stone-600 font-bold hover:bg-stone-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold shadow-md">
                            Perbarui
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection

