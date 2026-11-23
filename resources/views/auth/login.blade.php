@extends('layouts.app')

@section('title', 'Login Staf')

@section('content')
<div class="max-w-md mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-xl border border-stone-200 p-8">
        
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-brand-700 text-amber-200 flex items-center justify-center mx-auto mb-4 shadow-md">
                <i data-lucide="coffee" class="w-8 h-8"></i>
            </div>
            <h2 class="text-2xl font-black text-stone-900 tracking-tight">Portal Staf Brewly</h2>
            <p class="text-sm text-stone-500 mt-1">Sistem Pemesanan & Manajemen Kafe Berbasis Web</p>
        </div>

        @if($errors->any())
            <div class="mb-5 p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Email Staf</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </span>
                    <input type="email" name="email" id="email" required value="{{ old('email') }}"
                        placeholder="staf@brewly.com"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-brand-600 focus:border-brand-600 text-sm">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </span>
                    <input type="password" name="password" id="password" required
                        placeholder="••••••••"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-brand-600 focus:border-brand-600 text-sm">
                </div>
            </div>

            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold text-sm tracking-wide transition shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                <i data-lucide="log-in" class="w-4 h-4"></i>
                <span>Masuk ke Sistem</span>
            </button>
        </form>

        <!-- Quick Demo Login Section for Easy Review -->
        <div class="mt-8 pt-6 border-t border-stone-200">
            <p class="text-xs text-center font-bold text-stone-500 uppercase tracking-wider mb-3">⚡ Quick Demo Login (1-Klik)</p>
            <div class="grid grid-cols-3 gap-2">
                <a href="{{ route('login.quick', 'admin') }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 transition text-center group">
                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600 mb-1 group-hover:scale-110 transition-transform"></i>
                    <span class="text-xs font-bold">Admin</span>
                </a>

                <a href="{{ route('login.quick', 'cashier') }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-800 transition text-center group">
                    <i data-lucide="receipt" class="w-4 h-4 text-amber-600 mb-1 group-hover:scale-110 transition-transform"></i>
                    <span class="text-xs font-bold">Kasir</span>
                </a>

                <a href="{{ route('login.quick', 'kitchen') }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-800 transition text-center group">
                    <i data-lucide="chef-hat" class="w-4 h-4 text-blue-600 mb-1 group-hover:scale-110 transition-transform"></i>
                    <span class="text-xs font-bold">Dapur</span>
                </a>
            </div>
            <p class="text-[11px] text-stone-400 text-center mt-3">Akun bawaan default: password <code>password123</code></p>
        </div>

    </div>
</div>
@endsection

