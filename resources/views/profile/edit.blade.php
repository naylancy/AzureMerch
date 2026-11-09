@extends('layouts.app')

@section('title', 'Edit Profil Pengguna')

@section('main-content')
<div x-data="profileEditApp()" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    
    <!-- Top Action / Back Row -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-navy-900 tracking-tight flex items-center gap-2">
                <i data-lucide="user-cog" class="w-6 h-6 text-cyan-600"></i>
                Pengaturan Profil & Akun
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Perbarui data identitas pribadi, foto profil, dan kata sandi login Anda.
            </p>
        </div>

        <a href="{{ auth()->user()->isKasir() ? route('pos.index') : (auth()->user()->isGudang() ? route('warehouse.index') : route('admin.dashboard')) }}" 
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-2xl text-xs font-bold text-slate-700 bg-white border border-sky-200 hover:bg-sky-50 shadow-sm pastel-card-hover transition-all">
            <i data-lucide="arrow-left" class="w-4 h-4 text-cyan-600"></i>
            Kembali ke Dashboard
        </a>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
            <div class="font-bold flex items-center gap-1.5 mb-1.5">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500"></i>
                Terdapat kesalahan dalam pengisian formulir:
            </div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Profile Hero Card with Pastel Ocean Glow -->
    <div class="pastel-glass-card rounded-3xl p-6 border border-sky-200/80 shadow-lg relative overflow-hidden pastel-card-hover">
        <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-gradient-to-tr from-cyan-400/20 to-indigo-400/20 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row items-center gap-5 relative z-10">
            
            <!-- Live Avatar Preview with Ring Glow -->
            <div class="relative group">
                <div class="w-20 h-20 rounded-2xl overflow-hidden bg-gradient-to-tr from-cyan-400 via-sky-500 to-indigo-600 p-1 shadow-lg shadow-cyan-400/25">
                    <img :src="avatarUrl || '{{ $user->avatar ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80' }}'" 
                         alt="{{ $user->name }}" 
                         class="w-full h-full object-cover rounded-xl bg-white">
                </div>
                <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center text-white text-[10px]" title="Akun Aktif">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                </div>
            </div>

            <!-- Profile Info -->
            <div class="text-center sm:text-left flex-1">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-1">
                    <h2 class="text-xl font-extrabold text-navy-900" x-text="name || '{{ $user->name }}'"></h2>
                    
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider
                        {{ $user->role === 'admin' ? 'bg-indigo-100 text-indigo-700 border border-indigo-200' : '' }}
                        {{ $user->role === 'kasir' ? 'bg-cyan-100 text-cyan-700 border border-cyan-200' : '' }}
                        {{ $user->role === 'gudang' ? 'bg-amber-100 text-amber-700 border border-amber-200' : '' }}">
                        {{ strtoupper($user->role) }}
                    </span>
                </div>

                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-4 text-xs text-slate-500 font-mono">
                    <span class="flex items-center gap-1">
                        <i data-lucide="at-sign" class="w-3.5 h-3.5 text-cyan-600"></i>
                        <span x-text="username || '{{ $user->username }}'"></span>
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1">
                        <i data-lucide="mail" class="w-3.5 h-3.5 text-cyan-600"></i>
                        <span x-text="email || '{{ $user->email }}'"></span>
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-cyan-600"></i>
                        Bergabung {{ $user->created_at->format('M Y') }}
                    </span>
                </div>
            </div>

        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-sky-100 overflow-hidden">
        <form method="POST" action="{{ route('profile.update') }}" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Informasi Biodata -->
            <div>
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-cyan-700 border-b border-sky-50 pb-2 mb-4 flex items-center gap-2">
                    <i data-lucide="user" class="w-4 h-4 text-cyan-500"></i>
                    1. Informasi Biodata Pengguna
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Full Name -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" x-model="name" required 
                               class="w-full px-3.5 py-2.5 bg-sky-50/40 border border-sky-200 rounded-2xl text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-sky-400 focus:outline-none transition-all">
                    </div>

                    <!-- Username -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Username Login <span class="text-rose-500">*</span></label>
                        <input type="text" name="username" x-model="username" required 
                               class="w-full px-3.5 py-2.5 bg-sky-50/40 border border-sky-200 rounded-2xl text-xs font-mono font-bold focus:bg-white focus:ring-2 focus:ring-sky-400 focus:outline-none transition-all">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" x-model="email" required 
                               class="w-full px-3.5 py-2.5 bg-sky-50/40 border border-sky-200 rounded-2xl text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-sky-400 focus:outline-none transition-all">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp / Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 0812-3456-7890" 
                               class="w-full px-3.5 py-2.5 bg-sky-50/40 border border-sky-200 rounded-2xl text-xs font-mono focus:bg-white focus:ring-2 focus:ring-sky-400 focus:outline-none transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 2: Avatar & Foto Profil -->
            <div>
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-cyan-700 border-b border-sky-50 pb-2 mb-4 flex items-center gap-2">
                    <i data-lucide="image" class="w-4 h-4 text-cyan-500"></i>
                    2. Foto Profil & Avatar K-Pop
                </h3>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Avatar Idol / Karakter Favorit (1-Klik):</label>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <!-- Preset 1 -->
                            <button type="button" @click="setAvatar('https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80')" 
                                    class="w-12 h-12 rounded-2xl overflow-hidden border-2 hover:scale-110 transition-transform"
                                    :class="avatarUrl === 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80' ? 'border-cyan-500 shadow-md ring-2 ring-cyan-300' : 'border-slate-200'">
                                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80" class="w-full h-full object-cover">
                            </button>

                            <!-- Preset 2 -->
                            <button type="button" @click="setAvatar('https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80')" 
                                    class="w-12 h-12 rounded-2xl overflow-hidden border-2 hover:scale-110 transition-transform"
                                    :class="avatarUrl === 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80' ? 'border-cyan-500 shadow-md ring-2 ring-cyan-300' : 'border-slate-200'">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80" class="w-full h-full object-cover">
                            </button>

                            <!-- Preset 3 -->
                            <button type="button" @click="setAvatar('https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=200&q=80')" 
                                    class="w-12 h-12 rounded-2xl overflow-hidden border-2 hover:scale-110 transition-transform"
                                    :class="avatarUrl === 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=200&q=80' ? 'border-cyan-500 shadow-md ring-2 ring-cyan-300' : 'border-slate-200'">
                                <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=200&q=80" class="w-full h-full object-cover">
                            </button>

                            <!-- Preset 4 -->
                            <button type="button" @click="setAvatar('https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=200&q=80')" 
                                    class="w-12 h-12 rounded-2xl overflow-hidden border-2 hover:scale-110 transition-transform"
                                    :class="avatarUrl === 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=200&q=80' ? 'border-cyan-500 shadow-md ring-2 ring-cyan-300' : 'border-slate-200'">
                                <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=200&q=80" class="w-full h-full object-cover">
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Atau Masukkan Custom URL Foto Profil:</label>
                        <input type="url" name="avatar" x-model="avatarUrl" placeholder="https://..." 
                               class="w-full px-3.5 py-2.5 bg-sky-50/40 border border-sky-200 rounded-2xl text-xs focus:bg-white focus:ring-2 focus:ring-sky-400 focus:outline-none transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 3: Keamanan & Ganti Kata Sandi -->
            <div x-data="{ changePass: false }">
                <div class="flex items-center justify-between border-b border-sky-50 pb-2 mb-4">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-cyan-700 flex items-center gap-2">
                        <i data-lucide="lock" class="w-4 h-4 text-cyan-500"></i>
                        3. Keamanan Akun & Ganti Kata Sandi
                    </h3>

                    <button type="button" @click="changePass = !changePass; window.soundboard.playTick(true)" 
                            class="text-xs font-bold text-cyan-600 hover:text-cyan-700 flex items-center gap-1">
                        <span x-text="changePass ? 'Tutup Ganti Password' : '+ Ganti Kata Sandi'"></span>
                    </button>
                </div>

                <div x-show="changePass" x-cloak class="p-4 rounded-2xl bg-sky-50/50 border border-sky-200/80 space-y-3">
                    <p class="text-[11px] text-slate-500 mb-2">
                        Kosongkan bagian ini jika Anda tidak ingin mengganti kata sandi login.
                    </p>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi Saat Ini</label>
                        <input type="password" name="current_password" placeholder="••••••••" 
                               class="w-full px-3.5 py-2 bg-white border border-sky-200 rounded-xl text-xs font-mono focus:ring-2 focus:ring-sky-400 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi Baru</label>
                            <input type="password" name="new_password" placeholder="Minimal 8 karakter" 
                                   class="w-full px-3.5 py-2 bg-white border border-sky-200 rounded-xl text-xs font-mono focus:ring-2 focus:ring-sky-400 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Ulangi Kata Sandi Baru</label>
                            <input type="password" name="new_password_confirmation" placeholder="Konfirmasi sandi baru" 
                                   class="w-full px-3.5 py-2 bg-white border border-sky-200 rounded-xl text-xs font-mono focus:ring-2 focus:ring-sky-400 focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button Row -->
            <div class="pt-4 border-t border-sky-100 flex items-center justify-end gap-3">
                <button type="submit" 
                        class="px-6 py-3 rounded-2xl font-bold text-xs text-white bg-gradient-to-r from-cyan-500 to-sky-600 hover:from-cyan-400 hover:to-sky-500 shadow-lg shadow-cyan-500/25 active:scale-95 transition-all flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Perubahan Profil</span>
                </button>
            </div>

        </form>
    </div>

</div>

@push('scripts')
<script>
    function profileEditApp() {
        return {
            name: @json(old('name', $user->name)),
            username: @json(old('username', $user->username)),
            email: @json(old('email', $user->email)),
            avatarUrl: @json(old('avatar', $user->avatar)),

            setAvatar(url) {
                this.avatarUrl = url;
                if (window.soundboard) {
                    window.soundboard.playProductChime();
                }
            }
        };
    }
</script>
@endpush
@endsection
