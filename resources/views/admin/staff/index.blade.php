@extends('layouts.app')

@section('title', 'Admin - Kelola Staf')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ addModal: false, editModal: false, activeStaff: null }">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-stone-500 hover:text-stone-800">Admin Dashboard</a>
                <span class="text-stone-300">/</span>
                <span class="text-xs font-bold text-brand-700">DFD 5.0 Kelola Staf</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-stone-900 tracking-tight mt-1">Daftar Akun Staf Kafe</h1>
        </div>

        <button @click="addModal = true" class="px-4 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold transition shadow flex items-center gap-1.5">
            <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Staf Baru
        </button>
    </div>

    <!-- Staff Table -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 uppercase tracking-wider font-bold">
                    <tr>
                        <th class="py-3.5 px-4">Nama Staf</th>
                        <th class="py-3.5 px-4">Email</th>
                        <th class="py-3.5 px-4">Role Akses</th>
                        <th class="py-3.5 px-4">Status Akun</th>
                        <th class="py-3.5 px-4">Terdaftar</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($staffMembers as $staff)
                        <tr class="hover:bg-stone-50/70 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-brand-100 text-brand-800 flex items-center justify-center font-bold text-xs">
                                        {{ substr($staff->name, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-stone-900 text-sm">{{ $staff->name }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-stone-500 font-mono">
                                {{ $staff->email }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($staff->role === 'ADMIN')
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        ADMIN
                                    </span>
                                @elseif($staff->role === 'CASHIER')
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase bg-amber-100 text-amber-800 border border-amber-200">
                                        CASHIER (Kasir)
                                    </span>
                                @elseif($staff->role === 'KITCHEN')
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase bg-blue-100 text-blue-800 border border-blue-200">
                                        KITCHEN (Dapur)
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <form action="{{ route('admin.staff.toggle', $staff->staff_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase transition {{ $staff->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-100' : 'bg-red-50 text-red-700 border border-red-300 hover:bg-red-100' }}"
                                            title="Klik untuk ubah status aktif">
                                        {{ $staff->is_active ? '● Aktif' : '○ Non-Aktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-3.5 px-4 text-stone-400 text-[11px]">
                                {{ $staff->created_at->format('d M Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <button @click="activeStaff = {{ json_encode($staff) }}; editModal = true"
                                            class="p-1.5 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 transition" title="Edit Staf">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>

                                    @if($staff->staff_id !== auth()->id())
                                        <form action="{{ route('admin.staff.delete', $staff->staff_id) }}" method="POST"
                                              onsubmit="return confirm('Hapus staf ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-stone-100 hover:bg-red-50 text-stone-400 hover:text-red-600 transition" title="Hapus Staf">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-stone-400">Belum ada staf terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Staff Modal -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-sm">
        <div @click.outside="addModal = false" class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 animate-scale-up">
            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                <h3 class="font-black text-stone-900 text-base">Tambah Staf Baru</h3>
                <button @click="addModal = false" class="text-stone-400 hover:text-stone-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('admin.staff.store') }}" method="POST" class="mt-4 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-stone-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Budi Santoso"
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600">
                </div>

                <div>
                    <label class="block font-bold text-stone-700 mb-1">Email Staf <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required placeholder="budi@brewly.com"
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600">
                </div>

                <div>
                    <label class="block font-bold text-stone-700 mb-1">Kata Sandi (Password) <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600">
                </div>

                <div>
                    <label class="block font-bold text-stone-700 mb-1">Role / Peran <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full px-3 py-2 rounded-xl border border-stone-300 font-bold">
                        <option value="ADMIN">ADMIN (Akses Penuh Manajemen)</option>
                        <option value="CASHIER">CASHIER (Kasir, Transaksi, Struk)</option>
                        <option value="KITCHEN">KITCHEN (Dapur, Layar Pesanan)</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active_create" value="1" checked class="accent-brand-700 rounded">
                    <label for="is_active_create" class="font-bold text-stone-700">Akun Langsung Aktif</label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-100">
                    <button type="button" @click="addModal = false" class="px-4 py-2 rounded-xl border border-stone-200 text-stone-600 font-bold hover:bg-stone-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold shadow-md">
                        Simpan Staf
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Staff Modal -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-sm">
        <div @click.outside="editModal = false" class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 animate-scale-up">
            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                <h3 class="font-black text-stone-900 text-base">Edit Data Staf</h3>
                <button @click="editModal = false" class="text-stone-400 hover:text-stone-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <template x-if="activeStaff">
                <form :action="'/admin/staff/' + activeStaff.staff_id" method="POST" class="mt-4 space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" x-model="activeStaff.name" required
                            class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600">
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Email</label>
                        <input type="email" name="email" x-model="activeStaff.email" required
                            class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600">
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Password Baru (Kosongkan jika tidak diganti)</label>
                        <input type="password" name="password" placeholder="••••••••"
                            class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:ring-2 focus:ring-brand-600">
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Role / Peran</label>
                        <select name="role" x-model="activeStaff.role" class="w-full px-3 py-2 rounded-xl border border-stone-300 font-bold">
                            <option value="ADMIN">ADMIN</option>
                            <option value="CASHIER">CASHIER</option>
                            <option value="KITCHEN">KITCHEN</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active_edit" value="1" :checked="activeStaff.is_active" class="accent-brand-700 rounded">
                        <label for="is_active_edit" class="font-bold text-stone-700">Status Akun Aktif</label>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-100">
                        <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl border border-stone-200 text-stone-600 font-bold hover:bg-stone-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold shadow-md">
                            Perbarui Data
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection

