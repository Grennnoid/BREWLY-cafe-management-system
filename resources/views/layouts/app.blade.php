<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Brewly') - Cafe Ordering & Management System</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fdf8f6',
                            100: '#f2e8e5',
                            200: '#eaddd7',
                            500: '#a27053',
                            600: '#8c583f',
                            700: '#73442f',
                            800: '#5c3523',
                            900: '#432619',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-stone-50 text-stone-800 min-h-screen flex flex-col font-sans antialiased">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-stone-200 sticky top-0 z-40 shadow-sm no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <div class="w-10 h-10 rounded-xl bg-brand-700 text-amber-200 flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                            <i data-lucide="coffee" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="text-xl font-black tracking-tight text-stone-900 group-hover:text-brand-700 transition">Brewly</span>
                            <span class="hidden sm:inline-block text-xs uppercase tracking-wider text-stone-400 font-semibold ml-1.5">Cafe & Lounge</span>
                        </div>
                    </a>
                </div>

                <!-- Center / Role Navigation Links -->
                <nav class="hidden md:flex items-center gap-1 font-medium text-sm">
                    <a href="{{ route('home') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('home') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' }}">
                        Menu Pelanggan
                    </a>

                    @auth
                        @if(auth()->user()->role === 'CASHIER' || auth()->user()->role === 'ADMIN')
                            <a href="{{ route('cashier.orders') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('cashier.*') ? 'bg-amber-100 text-amber-900 font-semibold' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' }}">
                                <span class="inline-flex items-center gap-1.5">
                                    <i data-lucide="receipt" class="w-4 h-4"></i> Kasir
                                </span>
                            </a>
                        @endif

                        @if(auth()->user()->role === 'KITCHEN' || auth()->user()->role === 'ADMIN')
                            <a href="{{ route('kitchen.index') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('kitchen.*') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' }}">
                                <span class="inline-flex items-center gap-1.5">
                                    <i data-lucide="utensils-crossed" class="w-4 h-4"></i> Dapur (KDS)
                                </span>
                            </a>
                        @endif

                        @if(auth()->user()->role === 'ADMIN')
                            <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('admin.*') ? 'bg-emerald-100 text-emerald-900 font-semibold' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' }}">
                                <span class="inline-flex items-center gap-1.5">
                                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Admin Panel
                                </span>
                            </a>
                        @endif
                    @endauth
                </nav>

                <!-- Right Action Buttons -->
                <div class="flex items-center gap-3">
                    <!-- Customer Cart Icon -->
                    @php
                        $cartCount = \App\Services\CartService::getCart()->total_count ?? 0;
                    @endphp
                    <a href="{{ route('customer.cart') }}" class="relative p-2.5 rounded-xl text-stone-700 bg-stone-100 hover:bg-stone-200 transition">
                        <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 bg-amber-500 text-white font-bold text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Auth status or Login button -->
                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-xl border border-stone-200 hover:bg-stone-50 transition">
                                <div class="w-7 h-7 rounded-lg bg-brand-600 text-white flex items-center justify-center text-xs font-bold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <div class="text-left hidden lg:block">
                                    <p class="text-xs font-bold text-stone-800 leading-tight">{{ auth()->user()->name }}</p>
                                    <p class="text-[10px] text-stone-400 font-medium">{{ auth()->user()->role }}</p>
                                </div>
                                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-stone-400"></i>
                            </button>

                            <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-stone-100 py-1.5 z-50">
                                <div class="px-3 py-2 border-b border-stone-100 text-xs text-stone-500">
                                    Signed in as <strong class="text-stone-800">{{ auth()->user()->role }}</strong>
                                </div>
                                @if(auth()->user()->role === 'ADMIN')
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs text-stone-700 hover:bg-stone-50">
                                        <i data-lucide="layout-dashboard" class="w-4 h-4 text-stone-400"></i> Admin Dashboard
                                    </a>
                                @endif
                                @if(auth()->user()->role === 'CASHIER' || auth()->user()->role === 'ADMIN')
                                    <a href="{{ route('cashier.orders') }}" class="flex items-center gap-2 px-3 py-2 text-xs text-stone-700 hover:bg-stone-50">
                                        <i data-lucide="receipt" class="w-4 h-4 text-stone-400"></i> Kasir Orders
                                    </a>
                                @endif
                                @if(auth()->user()->role === 'KITCHEN' || auth()->user()->role === 'ADMIN')
                                    <a href="{{ route('kitchen.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs text-stone-700 hover:bg-stone-50">
                                        <i data-lucide="utensils" class="w-4 h-4 text-stone-400"></i> Kitchen Screen
                                    </a>
                                @endif
                                <div class="border-t border-stone-100 my-1"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 hover:bg-red-50 text-left">
                                        <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-stone-900 text-white hover:bg-stone-800 text-sm font-medium transition shadow-sm">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                            <span>Staf Login</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Messages (Alerts) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 no-print w-full">
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between mb-4 shadow-sm animate-fade-in" x-data="{ show: true }" x-show="show">
                <div class="flex items-center gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center justify-between mb-4 shadow-sm" x-data="{ show: true }" x-show="show">
                <div class="flex items-center gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 flex-shrink-0"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="text-red-500 hover:text-red-700">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 flex items-center justify-between mb-4 shadow-sm" x-data="{ show: true }" x-show="show">
                <div class="flex items-center gap-3">
                    <i data-lucide="info" class="w-5 h-5 text-blue-600 flex-shrink-0"></i>
                    <span class="text-sm font-medium">{{ session('info') }}</span>
                </div>
                <button @click="show = false" class="text-blue-500 hover:text-blue-700">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif
    </div>

    <!-- Main Content Body -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-stone-200 py-6 mt-12 no-print text-center text-xs text-stone-500">
        <div class="max-w-7xl mx-auto px-4">
            <p class="font-medium text-stone-700">Brewly Cafe Ordering & Management System</p>
            <p class="mt-1 text-stone-400">Berdasarkan Diagram DFD Level 0, DFD Level 1, ERD & Use Case Diagram</p>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>

