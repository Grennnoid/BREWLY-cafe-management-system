@extends('layouts.app')

@section('title', 'Admin - Kelola Menu')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ createModal: false, editModal: false, activeMenu: null }">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-stone-500 hover:text-stone-800">Admin Dashboard</a>
                <span class="text-stone-300">/</span>
                <span class="text-xs font-bold text-brand-700">Kelola Menu</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-stone-900 tracking-tight mt-1">Daftar Menu Kafe</h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.categories') }}" class="px-4 py-2.5 rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 text-xs font-bold transition shadow-sm flex items-center gap-1.5">
                <i data-lucide="tag" class="w-4 h-4 text-brand-700"></i> Kelola Kategori
            </a>
            <button @click="createModal = true" class="px-4 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold transition shadow flex items-center gap-1.5">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Menu Baru
            </button>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-sm mb-6 flex flex-wrap items-center justify-between gap-4">
        <form action="{{ route('admin.menus') }}" method="GET" class="flex flex-wrap items-center gap-2 text-xs w-full sm:w-auto">
            <select name="category" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-stone-200 bg-stone-50 font-medium">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->category_id }}" {{ $selectedCategory == $cat->category_id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama menu..."
                    class="pl-8 pr-3 py-2 rounded-xl border border-stone-200 bg-stone-50 focus:ring-1 focus:ring-brand-600 w-48 sm:w-64">
                <i data-lucide="search" class="w-3.5 h-3.5 text-stone-400 absolute left-2.5 top-2.5"></i>
            </div>
            
            <button type="submit" class="px-3.5 py-2 rounded-xl bg-stone-800 text-white font-bold hover:bg-stone-700 transition">
                Cari
            </button>
        </form>

        <span class="text-xs text-stone-500 font-medium">
            Total <strong>{{ $menus->total() }}</strong> menu terdaftar
        </span>
    </div>

    <!-- Menus Table -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 uppercase tracking-wider font-bold">
                    <tr>
                        <th class="py-3.5 px-4">Menu</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4">Harga</th>
                        <th class="py-3.5 px-4">Status Ketersediaan</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($menus as $menu)
                        <tr class="hover:bg-stone-50/70 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $menu->image_url ?: 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=200' }}"
                                         alt="{{ $menu->name }}" class="w-12 h-12 rounded-xl object-cover border border-stone-100 flex-shrink-0">
                                    <div>
                                        <p class="font-bold text-stone-900 text-sm">{{ $menu->name }}</p>
                                        <p class="text-[11px] text-stone-400 line-clamp-1 max-w-sm">{{ $menu->description }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-stone-100 text-stone-800">
                                    {{ $menu->category->name }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-black text-stone-900 text-sm whitespace-nowrap">
                                Rp {{ number_format($menu->price, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <form action="{{ route('admin.menus.toggle', $menu->menu_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase transition {{ $menu->is_available ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-stone-200 text-stone-600 hover:bg-stone-300' }}"
                                            title="Klik untuk ubah ketersediaan">
                                        {{ $menu->is_available ? '● Tersedia' : '○ Habis' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <button @click="activeMenu = {{ json_encode($menu) }}; editModal = true"
                                            class="p-1.5 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 transition" title="Edit Menu">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>

                                    <form action="{{ route('admin.menus.delete', $menu->menu_id) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-stone-100 hover:bg-red-50 text-stone-400 hover:text-red-600 transition" title="Hapus Menu">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-stone-400">
                                Tidak ada data menu yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($menus->hasPages())
            <div class="p-4 border-t border-stone-200">
                {{ $menus->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- Create Menu Modal -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-sm">
        <div @click.outside="createModal = false" class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 animate-scale-up">
            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                <h3 class="font-black text-stone-900 text-base">Tambah Menu Baru</h3>
                <button @click="createModal = false" class="text-stone-400 hover:text-stone-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('admin.menus.store') }}" method="POST" class="mt-4 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-stone-700 mb-1">Nama Menu <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Vanilla Cloud Latte"
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_id" required class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->category_id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="price" required min="0" step="500" placeholder="25000"
                            class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600 font-bold">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-stone-700 mb-1">URL Gambar (Opsional)</label>
                    <input type="url" name="image_url" placeholder="https://images.unsplash.com/..."
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600">
                </div>

                <div>
                    <label class="block font-bold text-stone-700 mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="2" placeholder="Komposisi, rasa, atau keunikan menu..."
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_available" id="is_available_create" value="1" checked class="accent-brand-700 rounded">
                    <label for="is_available_create" class="font-bold text-stone-700">Tersedia untuk Dipesan (Available)</label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-100">
                    <button type="button" @click="createModal = false" class="px-4 py-2 rounded-xl border border-stone-200 text-stone-600 font-bold hover:bg-stone-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold shadow-md">
                        Simpan Menu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Menu Modal -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-sm">
        <div @click.outside="editModal = false" class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 animate-scale-up">
            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                <h3 class="font-black text-stone-900 text-base">Edit Data Menu</h3>
                <button @click="editModal = false" class="text-stone-400 hover:text-stone-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <template x-if="activeMenu">
                <form :action="'/admin/menus/' + activeMenu.menu_id" method="POST" class="mt-4 space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Nama Menu</label>
                        <input type="text" name="name" x-model="activeMenu.name" required
                            class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">Kategori</label>
                            <select name="category_id" x-model="activeMenu.category_id" required class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->category_id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-stone-700 mb-1">Harga (Rp)</label>
                            <input type="number" name="price" x-model="activeMenu.price" required min="0"
                                class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600 font-bold">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">URL Gambar</label>
                        <input type="url" name="image_url" x-model="activeMenu.image_url"
                            class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600">
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Deskripsi</label>
                        <textarea name="description" x-model="activeMenu.description" rows="2"
                            class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600"></textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_available" id="is_available_edit" value="1" :checked="activeMenu.is_available" class="accent-brand-700 rounded">
                        <label for="is_available_edit" class="font-bold text-stone-700">Tersedia untuk Dipesan</label>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-100">
                        <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl border border-stone-200 text-stone-600 font-bold hover:bg-stone-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold shadow-md">
                            Perbarui Menu
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection

