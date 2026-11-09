@extends('layouts.base')

@section('content')
<div class="min-h-screen flex flex-col pastel-ocean-aura">
    
    <!-- Top Blue Ocean Navbar with Pastel Accents -->
    <header class="bg-navy-900/95 backdrop-blur-md text-white shadow-lg border-b border-cyan-500/20 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Left: Logo & Links -->
                <div class="flex items-center gap-6">
                    <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-cyan-400 via-sky-500 to-indigo-500 flex items-center justify-center shadow-lg shadow-cyan-400/30 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                            <i data-lucide="sparkles" class="w-5 h-5 text-white animate-pulse"></i>
                        </div>
                        <div>
                            <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-white via-sky-200 to-cyan-300 bg-clip-text text-transparent">
                                Azure<span class="text-cyan-400 font-black">Merch</span>
                            </span>
                            <span class="block text-[9px] uppercase font-bold tracking-widest text-sky-300 -mt-0.5">
                                K-Pop Retail & Inventory
                            </span>
                        </div>
                    </a>

                    <!-- Nav Links according to Role -->
                    <nav class="hidden md:flex items-center space-x-1.5 ml-4">
                        @if(auth()->user()->isKasir())
                            <a href="{{ route('pos.index') }}" 
                               class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('pos.index') ? 'bg-gradient-to-r from-cyan-500 to-sky-600 text-white shadow-md shadow-cyan-500/25 scale-105' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i data-lucide="layout-grid" class="w-4 h-4 text-cyan-300"></i>
                                POS Terminal Kasir
                            </a>
                            <a href="{{ route('pos.history') }}" 
                               class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('pos.history') ? 'bg-gradient-to-r from-cyan-500 to-sky-600 text-white shadow-md shadow-cyan-500/25 scale-105' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i data-lucide="history" class="w-4 h-4 text-sky-300"></i>
                                Riwayat Shift
                            </a>
                        @endif

                        @if(auth()->user()->isGudang())
                            <a href="{{ route('warehouse.index') }}" 
                               class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('warehouse.index') ? 'bg-gradient-to-r from-cyan-500 to-sky-600 text-white shadow-md shadow-cyan-500/25 scale-105' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i data-lucide="boxes" class="w-4 h-4 text-amber-300"></i>
                                Inventaris Produk
                            </a>
                            <a href="{{ route('warehouse.inbound') }}" 
                               class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('warehouse.inbound') ? 'bg-gradient-to-r from-cyan-500 to-sky-600 text-white shadow-md shadow-cyan-500/25 scale-105' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i data-lucide="arrow-down-left" class="w-4 h-4 text-teal-300"></i>
                                Penerimaan Inbound
                            </a>
                            <a href="{{ route('warehouse.logs') }}" 
                               class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('warehouse.logs') ? 'bg-gradient-to-r from-cyan-500 to-sky-600 text-white shadow-md shadow-cyan-500/25 scale-105' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-indigo-300"></i>
                                Log Mutasi Stok
                            </a>
                        @endif

                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" 
                               class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-cyan-500 to-sky-600 text-white shadow-md shadow-cyan-500/25 scale-105' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i data-lucide="pie-chart" class="w-4 h-4 text-pink-300"></i>
                                Dashboard Eksekutif
                            </a>
                            <a href="{{ route('admin.reports') }}" 
                               class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.reports') ? 'bg-gradient-to-r from-cyan-500 to-sky-600 text-white shadow-md shadow-cyan-500/25 scale-105' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i data-lucide="trending-up" class="w-4 h-4 text-cyan-300"></i>
                                Laporan Keuangan
                            </a>
                            <a href="{{ route('admin.audit-logs') }}" 
                               class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.audit-logs') ? 'bg-gradient-to-r from-cyan-500 to-sky-600 text-white shadow-md shadow-cyan-500/25 scale-105' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-300"></i>
                                Log Audit
                            </a>
                        @endif
                    </nav>
                </div>

                <!-- Right: Active Role Badge & Profile -->
                <div class="flex items-center gap-3">
                    
                    <!-- Soundboard Quick Trigger in Navbar -->
                    <button type="button" 
                            onclick="window.soundboard.playProductChime()"
                            title="Test Soundboard Chime"
                            class="p-2 rounded-xl bg-white/10 hover:bg-white/20 text-cyan-300 transition-colors flex items-center gap-1.5 text-xs font-semibold">
                        <i data-lucide="volume-2" class="w-4 h-4"></i>
                        <span class="hidden sm:inline text-[11px]">SFX</span>
                    </button>

                    <!-- Role Pill Indicator with Pastel Glow -->
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold tracking-wide shadow-sm
                        {{ auth()->user()->role === 'admin' ? 'bg-indigo-500/20 text-indigo-200 border border-indigo-400/40' : '' }}
                        {{ auth()->user()->role === 'kasir' ? 'bg-cyan-500/20 text-cyan-200 border border-cyan-400/40' : '' }}
                        {{ auth()->user()->role === 'gudang' ? 'bg-amber-500/20 text-amber-200 border border-amber-400/40' : '' }}">
                        <span class="w-2 h-2 rounded-full 
                            {{ auth()->user()->role === 'admin' ? 'bg-indigo-400 animate-ping' : '' }}
                            {{ auth()->user()->role === 'kasir' ? 'bg-cyan-400 animate-ping' : '' }}
                            {{ auth()->user()->role === 'gudang' ? 'bg-amber-400 animate-ping' : '' }}"></span>
                        {{ strtoupper(auth()->user()->role) }}
                    </span>

                    <!-- User Profile Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" 
                                class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-white/10 transition-colors text-left focus:outline-none">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-cyan-400 to-blue-600 border border-cyan-300/40 flex items-center justify-center font-bold text-xs text-white shadow-sm">
                                {{ substr(auth()->user()->name, 0, 2) }}
                            </div>
                            <div class="hidden sm:block text-xs">
                                <div class="font-bold text-slate-100 leading-tight">{{ auth()->user()->name }}</div>
                                <div class="text-[10px] text-cyan-300 font-mono">{{ '@' . auth()->user()->username }}</div>
                            </div>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                        </button>

                        <!-- Menu -->
                        <div x-show="open" @click.away="open = false" x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-56 rounded-2xl shadow-2xl bg-navy-900 border border-navy-700 py-1.5 z-50 text-slate-200">
                            
                            <div class="px-4 py-2.5 border-b border-navy-800">
                                <p class="text-[11px] text-slate-400">Masuk sebagai:</p>
                                <p class="text-sm font-bold text-white truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-cyan-400 font-mono">{{ auth()->user()->email }}</p>
                            </div>

                            <div class="p-1 border-b border-navy-800">
                                <a href="{{ route('profile.edit') }}" 
                                   class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-bold text-slate-200 hover:text-white hover:bg-white/10 rounded-xl transition-colors">
                                    <i data-lucide="user-cog" class="w-4 h-4 text-cyan-400"></i>
                                    Edit Profil Saya
                                </a>
                            </div>

                            <form method="POST" action="{{ route('logout') }}" class="p-1">
                                @csrf
                                <button type="submit" 
                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-bold text-rose-400 hover:bg-rose-500/10 rounded-xl transition-colors">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    Keluar (Logout)
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- Mobile Navigation Strip -->
        <div class="md:hidden border-t border-navy-800 bg-navy-950/90 px-4 py-2.5 flex items-center justify-between text-xs overflow-x-auto space-x-2">
            @if(auth()->user()->isKasir())
                <a href="{{ route('pos.index') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('pos.index') ? 'bg-cyan-500 text-white font-bold' : 'text-slate-300' }}">POS Kasir</a>
                <a href="{{ route('pos.history') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('pos.history') ? 'bg-cyan-500 text-white font-bold' : 'text-slate-300' }}">Riwayat</a>
            @endif
            @if(auth()->user()->isGudang())
                <a href="{{ route('warehouse.index') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('warehouse.index') ? 'bg-cyan-500 text-white font-bold' : 'text-slate-300' }}">Inventaris</a>
                <a href="{{ route('warehouse.inbound') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('warehouse.inbound') ? 'bg-cyan-500 text-white font-bold' : 'text-slate-300' }}">Inbound</a>
                <a href="{{ route('warehouse.logs') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('warehouse.logs') ? 'bg-cyan-500 text-white font-bold' : 'text-slate-300' }}">Log Mutasi</a>
            @endif
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-cyan-500 text-white font-bold' : 'text-slate-300' }}">Dashboard</a>
                <a href="{{ route('admin.reports') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.reports') ? 'bg-cyan-500 text-white font-bold' : 'text-slate-300' }}">Laporan</a>
                <a href="{{ route('admin.audit-logs') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.audit-logs') ? 'bg-cyan-500 text-white font-bold' : 'text-slate-300' }}">Audit</a>
            @endif
        </div>
    </header>

    <!-- Main Content Container with bottom padding so Fixed Footer never obscures content -->
    <main class="flex-1 pb-24">
        @yield('main-content')
    </main>

    <!-- FIXED FOOTER (Persisten di bawah layar dengan efek glassmorphism pastel) -->
    <footer class="fixed bottom-0 left-0 right-0 z-40 bg-white/90 backdrop-blur-md border-t border-sky-200/80 shadow-[0_-4px_25px_rgba(56,189,248,0.12)] py-2.5 px-4 sm:px-6 no-print">
        <div class="max-w-7xl mx-auto flex items-center justify-between text-xs text-slate-600">
            
            <!-- Left: Brand & Store Status -->
            <div class="flex items-center gap-3">
                <span class="font-extrabold text-navy-900 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Azure<span class="text-cyan-600">Merch</span> POS
                </span>
                <span class="hidden sm:inline text-slate-300">•</span>
                <span class="hidden sm:inline text-[11px] text-slate-500">
                    Sistem Kasir & Manajemen Stok Merchandise K-Pop
                </span>
            </div>

            <!-- Center: Quick Soundboard Audio Status & Tester -->
            <div class="flex items-center gap-2 bg-sky-50/80 border border-sky-200/70 px-3 py-1 rounded-full text-[11px] font-semibold text-sky-800">
                <span class="flex items-center gap-1 text-cyan-600">
                    <i data-lucide="volume-2" class="w-3.5 h-3.5"></i>
                    Soundboard Active:
                </span>
                <button type="button" onclick="window.soundboard.playProductChime()" class="hover:text-cyan-600 hover:scale-105 transition-transform" title="Test Chime">🛍️ Chime</button>
                <span>|</span>
                <button type="button" onclick="window.soundboard.playChaChing()" class="hover:text-emerald-600 hover:scale-105 transition-transform" title="Test Cha-ching">💰 Bayar</button>
            </div>

            <!-- Right: System Version & Pastel Theme Badge -->
            <div class="flex items-center gap-2.5 text-[11px]">
                <span class="hidden md:inline-flex items-center gap-1 font-semibold text-cyan-700 bg-cyan-100/70 px-2 py-0.5 rounded-full">
                    <i data-lucide="sparkles" class="w-3 h-3 text-cyan-600"></i> Pastel Ocean Edition
                </span>
                <span class="font-mono text-slate-400">&copy; {{ date('Y') }}</span>
            </div>

        </div>
    </footer>

</div>
@endsection
