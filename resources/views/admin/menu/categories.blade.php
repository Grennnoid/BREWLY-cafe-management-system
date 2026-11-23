@extends('layouts.app')

@section('title', 'Admin - Kelola Kategori')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ addModal: false, editModal: false, activeCat: null }">

    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-stone-500 hover:text-stone-800">Admin Dashboard</a>
                <span class="text-stone-300">/</span>
                <a href="{{ route('admin.menus') }}" class="text-xs font-bold text-stone-500 hover:text-stone-800">Kelola Menu</a>
                <span class="text-stone-300">/</span>
                <span class="text-xs font-bold text-brand-700">Kategori</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-stone-900 tracking-tight mt-1">Kategori Menu Kafe</h1>
        </div>

        <button @click="addModal = true" class="px-4 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold transition shadow flex items-center gap-1.5">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kategori
        </button>
    </div>

    <!-- Category Cards List -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 uppercase tracking-wider font-bold">
                    <tr>
                        <th class="py-3.5 px-4">Nama Kategori</th>
                        <th class="py-3.5 px-4">Deskripsi</th>
                        <th class="py-3.5 px-4">Jumlah Menu</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-stone-50/70 transition">
                            <td class="py-3.5 px-4 font-bold text-stone-900 text-sm">
                                {{ $cat->name }}
                            </td>
                            <td class="py-3.5 px-4 text-stone-500">
                                {{ $cat->description ?: '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <a href="{{ route('admin.menus', ['category' => $cat->category_id]) }}" class="px-2.5 py-1 rounded-md bg-stone-100 hover:bg-stone-200 text-brand-700 font-bold transition">
                                    {{ $cat->menus_count }} menu &rarr;
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <button @click="activeCat = {{ json_encode($cat) }}; editModal = true"
                                            class="p-1.5 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 transition" title="Edit">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>

                                    <form action="{{ route('admin.categories.delete', $cat->category_id) }}" method="POST"
                                          onsubmit="return confirm('Menghapus kategori ini juga akan menghapus semua menu di dalamnya. Lanjutkan?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-stone-100 hover:bg-red-50 text-stone-400 hover:text-red-600 transition" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-10 text-stone-400">Belum ada kategori menu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-sm">
        <div @click.outside="addModal = false" class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 animate-scale-up">
            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                <h3 class="font-black text-stone-900 text-base">Tambah Kategori Baru</h3>
                <button @click="addModal = false" class="text-stone-400 hover:text-stone-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('admin.categories.store') }}" method="POST" class="mt-4 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-stone-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Mocktails & Artisan Tea"
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600">
                </div>

                <div>
                    <label class="block font-bold text-stone-700 mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="2" placeholder="Penjelasan jenis hidangan dalam kategori ini..."
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-100">
                    <button type="button" @click="addModal = false" class="px-4 py-2 rounded-xl border border-stone-200 text-stone-600 font-bold hover:bg-stone-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold shadow-md">
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-sm">
        <div @click.outside="editModal = false" class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 animate-scale-up">
            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                <h3 class="font-black text-stone-900 text-base">Edit Kategori</h3>
                <button @click="editModal = false" class="text-stone-400 hover:text-stone-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <template x-if="activeCat">
                <form :action="'/admin/categories/' + activeCat.category_id" method="POST" class="mt-4 space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Nama Kategori</label>
                        <input type="text" name="name" x-model="activeCat.name" required
                            class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600">
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Deskripsi</label>
                        <textarea name="description" x-model="activeCat.description" rows="2"
                            class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-100">
                        <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl border border-stone-200 text-stone-600 font-bold hover:bg-stone-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold shadow-md">
                            Perbarui Kategori
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection

