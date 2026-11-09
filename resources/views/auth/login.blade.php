@extends('layouts.base')

@section('title', 'Login - AzureMerch POS & Inventory')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-navy-950 via-navy-900 to-navy-850 relative overflow-hidden">
    <!-- Ambient Blue Ocean Glows -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 shadow-xl shadow-cyan-500/25 mb-4 transform hover:scale-105 transition-transform duration-300">
                <i data-lucide="sparkles" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-white">
                Azure<span class="text-cyan-400">Merch</span>
            </h1>
            <p class="text-slate-400 text-sm mt-1">
                K-Pop Retail Point of Sale & Inventory Management
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-navy-900/80 backdrop-blur-xl border border-navy-700/60 rounded-2xl shadow-2xl p-6 sm:p-8">
            <h2 class="text-lg font-bold text-white mb-1">Masuk ke Sistem</h2>
            <p class="text-xs text-slate-400 mb-6">Gunakan kredensial akun kasir, gudang, atau administrator Anda.</p>

            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs flex items-start gap-2.5">
                    <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0 text-rose-400 mt-0.5"></i>
                    <div>
                        <span class="font-semibold block mb-0.5">Gagal Masuk:</span>
                        <ul class="list-disc list-inside space-y-0.5 text-rose-200">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <!-- Username or Email -->
                <div>
                    <label for="login" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Username atau Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </div>
                        <input type="text" id="login" name="login" value="{{ old('login', 'kasir') }}" required autofocus
                               class="w-full pl-10 pr-4 py-2.5 bg-navy-950/70 border border-navy-700 text-white placeholder-slate-500 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all font-mono"
                               placeholder="Contoh: kasir atau admin">
                    </div>
                </div>

                <!-- Password -->
                <div x-data="{ showPass: false }">
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Kata Sandi
                        </label>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input :type="showPass ? 'text' : 'password'" id="password" name="password" value="password" required
                               class="w-full pl-10 pr-10 py-2.5 bg-navy-950/70 border border-navy-700 text-white placeholder-slate-500 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all font-mono"
                               placeholder="••••••••">
                        <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-white">
                            <i :data-lucide="showPass ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-400 hover:text-slate-200">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-navy-950 border-navy-700 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-navy-900">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-3 px-4 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 shadow-lg shadow-cyan-500/25 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    <span>Masuk ke Dashboard</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <!-- Quick Login Demo Buttons (Fast Testing) -->
            <div class="mt-8 pt-6 border-t border-navy-700/60">
                <p class="text-[11px] font-bold text-cyan-400 uppercase tracking-widest text-center mb-3">
                    ⚡ Akses Cepat Demo Role (1-Klik)
                </p>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick="fillCredential('kasir', 'password')"
                            class="p-2.5 bg-navy-800/80 hover:bg-sky-500/25 hover:border-sky-400 border border-navy-700 rounded-2xl text-center transition-all group pastel-card-hover">
                        <div class="w-8 h-8 mx-auto rounded-xl bg-sky-500/20 text-sky-300 flex items-center justify-center mb-1 group-hover:scale-115 transition-transform">
                            <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                        </div>
                        <div class="text-[11px] font-bold text-white">Kasir</div>
                        <div class="text-[9px] text-sky-300 font-mono">Desktop POS</div>
                    </button>

                    <button type="button" onclick="fillCredential('gudang', 'password')"
                            class="p-2.5 bg-navy-800/80 hover:bg-amber-500/25 hover:border-amber-400 border border-navy-700 rounded-2xl text-center transition-all group pastel-card-hover">
                        <div class="w-8 h-8 mx-auto rounded-xl bg-amber-500/20 text-amber-300 flex items-center justify-center mb-1 group-hover:scale-115 transition-transform">
                            <i data-lucide="boxes" class="w-4 h-4"></i>
                        </div>
                        <div class="text-[11px] font-bold text-white">Gudang</div>
                        <div class="text-[9px] text-amber-300 font-mono">Stok Inbound</div>
                    </button>

                    <button type="button" onclick="fillCredential('admin', 'password')"
                            class="p-2.5 bg-navy-800/80 hover:bg-indigo-500/25 hover:border-indigo-400 border border-navy-700 rounded-2xl text-center transition-all group pastel-card-hover">
                        <div class="w-8 h-8 mx-auto rounded-xl bg-indigo-500/20 text-indigo-300 flex items-center justify-center mb-1 group-hover:scale-115 transition-transform">
                            <i data-lucide="pie-chart" class="w-4 h-4"></i>
                        </div>
                        <div class="text-[11px] font-bold text-white">Admin</div>
                        <div class="text-[9px] text-indigo-300 font-mono">Laporan Laba</div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer Info -->
        <p class="text-center text-xs text-slate-500 mt-6 font-mono">
            AzureMerch POS System • Default Password: <span class="text-slate-300">password</span>
        </p>
    </div>
</div>

<script>
    function fillCredential(user, pass) {
        if (window.soundboard) {
            window.soundboard.playProductChime();
        }
        document.getElementById('login').value = user;
        document.getElementById('password').value = pass;
    }
</script>
@endsection
