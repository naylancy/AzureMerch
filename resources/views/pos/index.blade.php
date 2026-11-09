@extends('layouts.base')

@section('title', 'POS Kasir Terminal - Pastel Ocean Edition')

@section('content')
<div x-data="posApp()" x-init="init()" class="h-screen flex flex-col bg-slate-50 overflow-hidden select-none font-sans">

    <!-- Top High-Density Cashier Bar (Navy with Pastel Ocean Accents) -->
    <header class="bg-gradient-to-r from-navy-950 via-navy-900 to-navy-850 text-white px-4 py-2 flex items-center justify-between border-b border-cyan-500/20 shadow-md flex-shrink-0 z-30">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-cyan-400 via-sky-500 to-indigo-500 flex items-center justify-center shadow-lg shadow-cyan-400/30">
                    <i data-lucide="sparkles" class="w-4 h-4 text-white animate-pulse"></i>
                </div>
                <div>
                    <span class="font-extrabold text-base tracking-tight bg-gradient-to-r from-white via-sky-200 to-cyan-300 bg-clip-text text-transparent">
                        Azure<span class="text-cyan-400 font-black">Merch</span> POS
                    </span>
                    <span class="text-[9px] text-sky-300 font-mono ml-1.5 uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-white/10">Terminal #01</span>
                </div>
            </div>

            <!-- Cashier & Live Time -->
            <div class="hidden lg:flex items-center gap-3 pl-4 border-l border-navy-700 text-xs text-slate-300">
                <div class="flex items-center gap-1.5">
                    <i data-lucide="user-check" class="w-3.5 h-3.5 text-cyan-400"></i>
                    <span>Kasir: <strong class="text-white">{{ auth()->user()->name }}</strong></span>
                </div>
                <span>•</span>
                <div class="flex items-center gap-1.5 font-mono text-cyan-300" x-text="currentTime"></div>
            </div>
        </div>

        <!-- Shift Metrics, Soundboard Controller & Actions -->
        <div class="flex items-center gap-2">
            
            <!-- Soundboard Live Indicator & Dropdown -->
            <div x-data="{ openSound: false }" class="relative">
                <button @click="openSound = !openSound" 
                        title="Soundboard Efek Suara Produk"
                        class="px-2.5 py-1 rounded-xl bg-gradient-to-r from-sky-500/20 to-indigo-500/20 hover:from-sky-500/30 hover:to-indigo-500/30 border border-sky-400/40 text-sky-200 text-xs flex items-center gap-1.5 transition-all">
                    <!-- Equalizer Animation Icon -->
                    <span class="flex items-end gap-0.5 h-3.5 w-3.5">
                        <span class="w-1 bg-cyan-400 rounded-full h-full animate-bounce"></span>
                        <span class="w-1 bg-sky-300 rounded-full h-2/3 animate-pulse"></span>
                        <span class="w-1 bg-indigo-400 rounded-full h-full animate-bounce delay-75"></span>
                    </span>
                    <span class="hidden sm:inline text-[11px] font-bold">Soundboard SFX</span>
                    <i data-lucide="chevron-down" class="w-3 h-3 text-sky-300"></i>
                </button>

                <!-- Soundboard Test Pad Dropdown -->
                <div x-show="openSound" @click.away="openSound = false" x-cloak
                     class="absolute right-0 mt-2 w-64 rounded-2xl shadow-2xl bg-navy-900 border border-navy-700 p-3 z-50 text-slate-200">
                    <div class="flex items-center justify-between pb-2 border-b border-navy-800 mb-2">
                        <span class="text-xs font-bold text-sky-300 flex items-center gap-1.5">
                            <i data-lucide="music" class="w-3.5 h-3.5"></i>
                            Soundboard Kasir
                        </span>
                        <label class="flex items-center gap-1 text-[10px] text-slate-400 cursor-pointer">
                            <input type="checkbox" x-model="isMuted" @change="toggleMute()" class="rounded bg-navy-950 border-navy-700 text-cyan-500">
                            Mute
                        </label>
                    </div>

                    <p class="text-[10px] text-slate-400 mb-2">Suara otomatis berbunyi ketika memilih produk atau scan barcode. Uji coba tombol:</p>

                    <div class="grid grid-cols-2 gap-1.5">
                        <button type="button" @click="testSound('chime')" 
                                class="p-1.5 rounded-lg bg-navy-800 hover:bg-sky-500/20 border border-navy-700 text-[11px] text-sky-200 font-semibold flex items-center gap-1">
                            🛍️ Chime Produk
                        </button>
                        <button type="button" @click="testSound('beep')" 
                                class="p-1.5 rounded-lg bg-navy-800 hover:bg-sky-500/20 border border-navy-700 text-[11px] text-cyan-200 font-semibold flex items-center gap-1">
                            📷 Scan Barcode
                        </button>
                        <button type="button" @click="testSound('chaching')" 
                                class="p-1.5 rounded-lg bg-navy-800 hover:bg-emerald-500/20 border border-navy-700 text-[11px] text-emerald-200 font-semibold flex items-center gap-1">
                            💰 Cha-ching Bayar
                        </button>
                        <button type="button" @click="testSound('error')" 
                                class="p-1.5 rounded-lg bg-navy-800 hover:bg-rose-500/20 border border-navy-700 text-[11px] text-rose-200 font-semibold flex items-center gap-1">
                            ⚠️ Peringatan Stok
                        </button>
                    </div>
                </div>
            </div>

            <!-- Today Shift Sales Chip with Pastel Border -->
            <div class="hidden sm:flex items-center gap-2 px-3 py-1 bg-navy-800/80 rounded-xl border border-sky-500/20 text-xs">
                <i data-lucide="wallet" class="w-3.5 h-3.5 text-teal-400"></i>
                <span class="text-slate-400">Shift:</span>
                <span class="font-bold text-teal-300 font-mono">Rp {{ number_format($shiftStats['today_sales'], 0, ',', '.') }}</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full bg-navy-700 text-sky-300 font-bold">({{ $shiftStats['today_count'] }} trx)</span>
            </div>

            <!-- History Link -->
            <a href="{{ route('pos.history') }}" 
               class="p-1.5 rounded-xl bg-navy-800/80 hover:bg-navy-700 text-slate-300 hover:text-white border border-navy-700 text-xs flex items-center gap-1">
                <i data-lucide="history" class="w-4 h-4 text-sky-400"></i>
                <span class="hidden md:inline text-[11px] font-semibold">Riwayat</span>
            </a>

            <!-- Profile Link -->
            <a href="{{ route('profile.edit') }}" 
               title="Edit Profil Kasir"
               class="p-1.5 rounded-xl bg-navy-800/80 hover:bg-navy-700 text-slate-300 hover:text-white border border-navy-700 text-xs flex items-center gap-1">
                <i data-lucide="user-cog" class="w-4 h-4 text-cyan-400"></i>
                <span class="hidden md:inline text-[11px] font-semibold">Profil</span>
            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" 
                        class="p-1.5 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/30 text-xs flex items-center gap-1 transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span class="hidden md:inline text-[11px] font-semibold">Keluar</span>
                </button>
            </form>
        </div>
    </header>

    <!-- Main Split-Screen Work Area -->
    <div class="flex-1 flex overflow-hidden relative">
        
        <!-- ================= LEFT: PRODUCT CATALOG (65% Width Desktop) ================= -->
        <section class="flex-1 flex flex-col border-r border-sky-100 bg-gradient-to-br from-slate-50 via-sky-50/30 to-indigo-50/20 min-w-0">
            
            <!-- Search & Barcode Scan Bar -->
            <div class="p-3 bg-white/90 backdrop-blur-sm border-b border-sky-100 shadow-sm flex flex-col sm:flex-row items-center gap-2.5 flex-shrink-0">
                <!-- Barcode / Search Input -->
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-sky-500">
                        <i data-lucide="scan-barcode" class="w-5 h-5 animate-pulse"></i>
                    </div>
                    <input type="text" 
                           x-ref="searchInput"
                           x-model="searchQuery" 
                           @keydown.enter.prevent="handleBarcodeScan()"
                           placeholder="Scan Barcode / Tekan [F2] untuk cari nama, SKU, artis..." 
                           class="w-full pl-11 pr-24 py-2.5 bg-sky-50/50 border border-sky-200 focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-400/20 rounded-2xl text-xs font-semibold text-slate-900 placeholder-slate-400 transition-all">
                    
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1.5">
                        <span class="text-[10px] font-mono px-2 py-0.5 bg-sky-100 text-sky-800 rounded-md font-bold">F2</span>
                        <button x-show="searchQuery" @click="searchQuery = ''; filterProducts()" class="text-slate-400 hover:text-slate-600">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Reset Category Filter -->
                <button @click="selectedCategory = 'all'; filterProducts()" 
                        :class="selectedCategory === 'all' ? 'bg-gradient-to-r from-navy-900 to-navy-800 text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-sky-50 border border-sky-200'"
                        class="px-3.5 py-2.5 rounded-2xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-1.5">
                    <i data-lucide="grid" class="w-3.5 h-3.5 text-cyan-400"></i>
                    Semua Kategori ({{ count($products) }})
                </button>
            </div>

            <!-- Colorful Pastel Category Pills Filter Bar -->
            <div class="px-3 py-2 bg-white/70 backdrop-blur-sm border-b border-sky-100 flex items-center gap-2 overflow-x-auto flex-shrink-0 no-scrollbar">
                @foreach($categories as $cat)
                    @php
                        // Harmonious Pastel Category Accents
                        $pastelStyles = match($cat->code) {
                            'ALB' => ['bg' => 'bg-indigo-50 hover:bg-indigo-100 text-indigo-800 border-indigo-200', 'active' => 'bg-gradient-to-r from-indigo-500 to-blue-600 text-white shadow-indigo-400/30'],
                            'PC'  => ['bg' => 'bg-pink-50 hover:bg-pink-100 text-pink-800 border-pink-200', 'active' => 'bg-gradient-to-r from-pink-500 to-rose-500 text-white shadow-pink-400/30'],
                            'DLL' => ['bg' => 'bg-teal-50 hover:bg-teal-100 text-teal-800 border-teal-200', 'active' => 'bg-gradient-to-r from-teal-500 to-emerald-500 text-white shadow-teal-400/30'],
                            'KCH' => ['bg' => 'bg-sky-50 hover:bg-sky-100 text-sky-800 border-sky-200', 'active' => 'bg-gradient-to-r from-sky-400 to-cyan-500 text-white shadow-sky-400/30'],
                            'APP' => ['bg' => 'bg-amber-50 hover:bg-amber-100 text-amber-800 border-amber-200', 'active' => 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-amber-400/30'],
                            'FTW' => ['bg' => 'bg-blue-50 hover:bg-blue-100 text-blue-800 border-blue-200', 'active' => 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-blue-400/30'],
                            'COL' => ['bg' => 'bg-purple-50 hover:bg-purple-100 text-purple-800 border-purple-200', 'active' => 'bg-gradient-to-r from-purple-500 to-indigo-500 text-white shadow-purple-400/30'],
                            default => ['bg' => 'bg-slate-50 hover:bg-slate-100 text-slate-800 border-slate-200', 'active' => 'bg-cyan-500 text-white shadow-cyan-400/30'],
                        };
                    @endphp
                    <button @click="selectedCategory = {{ $cat->id }}; filterProducts(); window.soundboard.playTick(true)"
                            :class="selectedCategory === {{ $cat->id }} ? '{{ $pastelStyles['active'] }} shadow-md font-extrabold scale-105' : '{{ $pastelStyles['bg'] }} font-semibold border'"
                            class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all duration-200 flex items-center gap-1.5 flex-shrink-0">
                        <span>{{ $cat->name }}</span>
                        <span :class="selectedCategory === {{ $cat->id }} ? 'bg-white/30 text-white' : 'bg-white/70 text-slate-700'" 
                              class="text-[10px] px-1.5 py-0.2 rounded-full font-mono font-bold">
                            {{ $cat->products_count }}
                        </span>
                    </button>
                @endforeach
            </div>

            <!-- Product Grid with Pastel Cards & Soundboard Click Interaction -->
            <div class="flex-1 overflow-y-auto p-3 sm:p-4 pb-16">
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 sm:gap-4">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div @click="addToCartWithSound(product, $event)"
                             class="group pastel-glass-card rounded-2xl border border-sky-100 p-3 hover:border-sky-400 hover:shadow-[0_12px_28px_rgba(56,189,248,0.22)] pastel-card-hover cursor-pointer flex flex-col justify-between relative overflow-hidden"
                             :class="product.stock <= 0 ? 'opacity-55 pointer-events-none' : ''">
                            
                            <!-- Artist / Category Badge with Dynamic Pastel Accents -->
                            <div class="flex items-center justify-between mb-2 text-[10px]">
                                <span class="px-2 py-0.5 rounded-lg font-bold uppercase tracking-wider"
                                      :class="getPastelCategoryClass(product.category.code)"
                                      x-text="product.group_artist || product.category.code"></span>
                                
                                <span class="px-2 py-0.5 rounded-lg font-mono font-bold"
                                      :class="product.stock <= product.min_stock_alert ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-teal-50 text-teal-700 border border-teal-200'"
                                      x-text="'Stok: ' + product.stock"></span>
                            </div>

                            <!-- Product Thumbnail Image with Soft Glow -->
                            <div class="w-full h-32 rounded-xl bg-sky-50/50 mb-2.5 overflow-hidden relative flex items-center justify-center border border-sky-100/80 group-hover:border-sky-300 transition-colors">
                                <img :src="product.image_url || 'https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?auto=format&fit=crop&w=300&q=80'" 
                                     :alt="product.name" 
                                     class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-300">
                                
                                <!-- Hover Quick Add Audio Overlay -->
                                <div class="absolute inset-0 bg-navy-900/35 backdrop-blur-[2px] opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                                    <div class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-sky-400 to-cyan-500 shadow-lg font-bold text-xs flex items-center gap-1.5 transform group-hover:scale-105 transition-transform">
                                        <i data-lucide="plus" class="w-3.5 h-3.5 text-white"></i>
                                        <span>+ Pilih Produk</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div>
                                <h3 class="text-xs font-bold text-slate-800 line-clamp-2 leading-snug mb-1 group-hover:text-cyan-700 transition-colors" x-text="product.name"></h3>
                                <div class="text-[10px] text-slate-400 font-mono mb-1.5 truncate" x-text="product.sku"></div>
                                <div class="text-sm font-extrabold text-navy-900 font-mono bg-gradient-to-r from-navy-900 via-sky-800 to-indigo-900 bg-clip-text text-transparent">
                                    Rp <span x-text="formatNumber(product.selling_price)"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="filteredProducts.length === 0" class="text-center py-20">
                    <div class="w-16 h-16 mx-auto rounded-3xl bg-sky-100/80 flex items-center justify-center text-sky-400 mb-3 animate-soft-float">
                        <i data-lucide="search-x" class="w-8 h-8"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-700">Produk tidak ditemukan</h4>
                    <p class="text-xs text-slate-500 mt-1">Coba kata kunci pencarian lain atau ganti filter kategori pastel.</p>
                </div>
            </div>

        </section>

        <!-- ================= RIGHT: HIGH-DENSITY CART (35% Width Desktop) ================= -->
        <aside class="w-full lg:w-[420px] 2xl:w-[460px] bg-white flex flex-col flex-shrink-0 shadow-2xl border-l border-sky-100 z-20 pb-12">
            
            <!-- Cart Header with Soundboard Badge -->
            <div class="p-3.5 bg-gradient-to-r from-navy-950 via-navy-900 to-navy-850 text-white flex items-center justify-between border-b border-navy-800 flex-shrink-0">
                <div class="flex items-center gap-2">
                    <i data-lucide="shopping-cart" class="w-4 h-4 text-cyan-400"></i>
                    <h2 class="font-extrabold text-sm tracking-wide">Keranjang Belanja</h2>
                    <span class="px-2 py-0.5 rounded-full bg-gradient-to-r from-sky-400 to-cyan-500 text-white font-mono text-[11px] font-bold shadow-sm" x-text="cartItemCount"></span>
                </div>
                <button @click="clearCart()" 
                        x-show="cart.length > 0"
                        class="text-[11px] text-rose-300 hover:text-white flex items-center gap-1 font-semibold transition-colors">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    Kosongkan
                </button>
            </div>

            <!-- Customer Name & Notes Bar -->
            <div class="p-2.5 bg-sky-50/50 border-b border-sky-100 flex items-center gap-2 flex-shrink-0">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-sky-500">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i>
                    </div>
                    <input type="text" x-model="customerName" placeholder="Nama Pelanggan / Fandom (Opsional)" 
                           class="w-full pl-8 pr-2 py-1.5 bg-white border border-sky-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-400">
                </div>
            </div>

            <!-- Cart Items List (Scrollable) -->
            <div class="flex-1 overflow-y-auto divide-y divide-sky-50 p-2">
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="p-2.5 hover:bg-sky-50/60 rounded-xl transition-all flex items-start gap-2.5 group">
                        
                        <!-- Item Details -->
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 truncate" x-text="item.name"></h4>
                            <div class="flex items-center gap-2 text-[10px] text-slate-400 font-mono mt-0.5">
                                <span x-text="item.sku"></span>
                                <span>•</span>
                                <span class="text-cyan-700 font-semibold" x-text="'@ Rp ' + formatNumber(item.price)"></span>
                            </div>

                            <!-- Quantity Stepper & Price -->
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex items-center border border-sky-200 rounded-lg overflow-hidden bg-white shadow-sm">
                                    <button @click="decrementQty(index)" 
                                            class="px-2.5 py-1 bg-sky-50 hover:bg-sky-100 text-sky-900 text-xs font-bold transition-colors">
                                        -
                                    </button>
                                    <span class="px-3 py-1 font-mono text-xs font-bold text-navy-900" x-text="item.quantity"></span>
                                    <button @click="incrementQty(index)" 
                                            class="px-2.5 py-1 bg-sky-50 hover:bg-sky-100 text-sky-900 text-xs font-bold transition-colors">
                                        +
                                    </button>
                                </div>

                                <div class="text-xs font-extrabold text-navy-900 font-mono">
                                    Rp <span x-text="formatNumber(item.price * item.quantity)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Remove Item Button -->
                        <button @click="removeFromCart(index)" class="text-slate-300 hover:text-rose-500 p-1 transition-colors">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </template>

                <!-- Empty Cart State -->
                <div x-show="cart.length === 0" class="h-full flex flex-col items-center justify-center text-center p-6 text-slate-400">
                    <div class="w-14 h-14 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center mb-3">
                        <i data-lucide="shopping-bag" class="w-7 h-7 text-sky-300"></i>
                    </div>
                    <h4 class="text-xs font-bold text-slate-700">Keranjang Masih Kosong</h4>
                    <p class="text-[11px] text-slate-400 mt-1 max-w-[200px]">
                        Pilih produk merchandise di katalog sebelah kiri atau scan barcode untuk menambahkan.
                    </p>
                </div>
            </div>

            <!-- Cart Summary & Calculation Panel -->
            <div class="p-3.5 bg-gradient-to-b from-sky-50/40 to-white border-t border-sky-100 space-y-2 flex-shrink-0">
                <div class="flex justify-between text-xs text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-mono font-semibold" x-text="'Rp ' + formatNumber(subtotal)"></span>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-600">
                    <span class="flex items-center gap-1">Diskon Fandom (Rp):</span>
                    <input type="number" x-model.number="discount" min="0" 
                           class="w-24 px-2 py-0.5 text-right font-mono text-xs bg-white border border-sky-200 rounded-lg focus:ring-2 focus:ring-sky-400">
                </div>

                <!-- Grand Total Display (Hero Pastel Cyan Callout) -->
                <div class="pt-2 border-t border-sky-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase tracking-wider text-sky-700 font-extrabold block">Total Tagihan:</span>
                        <div class="text-xl 2xl:text-2xl font-black text-navy-900 font-mono tracking-tight">
                            Rp <span x-text="formatNumber(grandTotal)"></span>
                        </div>
                    </div>

                    <!-- Checkout Trigger Button with Shimmer Animation -->
                    <button @click="openPaymentModal()" 
                            :disabled="cart.length === 0"
                            class="relative overflow-hidden px-5 py-3 rounded-2xl font-extrabold text-sm text-white bg-gradient-to-r from-sky-500 via-cyan-500 to-blue-600 hover:from-sky-400 hover:to-blue-500 shadow-xl shadow-cyan-500/25 active:scale-95 disabled:opacity-50 disabled:pointer-events-none transition-all flex items-center gap-2 group">
                        <span class="relative z-10">Bayar [F4]</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 relative z-10 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </div>

        </aside>

    </div>

    <!-- ================= FIXED BOTTOM CASHIER STATUS FOOTER ================= -->
    <footer class="fixed bottom-0 left-0 right-0 z-30 h-11 bg-white/95 backdrop-blur-md border-t border-sky-200/80 shadow-[0_-4px_20px_rgba(56,189,248,0.08)] px-4 flex items-center justify-between text-xs text-slate-600 no-print">
        
        <!-- Left: Quick Keyboard Shortcuts Pill -->
        <div class="flex items-center gap-2 text-[11px]">
            <span class="font-bold text-navy-900">Pintasan POS:</span>
            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-sky-100 text-sky-800 font-mono font-bold">[F2] Cari/Scan</span>
            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-sky-100 text-sky-800 font-mono font-bold">[F4] Bayar</span>
            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-mono font-bold">[ESC] Batal</span>
        </div>

        <!-- Center: Soundboard Audio Live Pulse -->
        <div class="hidden md:flex items-center gap-2 text-[11px] font-semibold text-cyan-800 bg-sky-50 px-3 py-1 rounded-full border border-sky-200/70">
            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
            <span>Soundboard Aktif (Web Audio Synthesizer)</span>
        </div>

        <!-- Right: AzureMerch Copyright -->
        <div class="flex items-center gap-2 text-[11px] text-slate-400 font-mono">
            <span class="font-bold text-navy-900">AzureMerch POS</span> &copy; {{ date('Y') }}
        </div>
    </footer>

    <!-- ================= PAYMENT PROCESSING MODAL ================= -->
    <div x-show="showPaymentModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-navy-950/75 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="showPaymentModal = false" 
             class="bg-white rounded-3xl shadow-2xl border border-sky-100 w-full max-w-lg overflow-hidden flex flex-col">
            
            <!-- Modal Header -->
            <div class="p-4 bg-gradient-to-r from-navy-950 via-navy-900 to-navy-850 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="credit-card" class="w-5 h-5 text-cyan-400"></i>
                    <h3 class="font-bold text-base">Pembayaran Transaksi</h3>
                </div>
                <button @click="showPaymentModal = false" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="p-5 space-y-4">
                
                <!-- Total Amount Banner -->
                <div class="p-4 rounded-2xl bg-gradient-to-r from-sky-50 via-cyan-50 to-indigo-50 border border-sky-200 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-sky-700 font-bold">Total yang harus dibayar:</span>
                        <div class="text-2xl font-black text-navy-900 font-mono">
                            Rp <span x-text="formatNumber(grandTotal)"></span>
                        </div>
                    </div>
                    <span class="text-xs px-2.5 py-1 bg-white text-cyan-700 font-bold rounded-xl shadow-sm font-mono border border-sky-200" 
                          x-text="cartItemCount + ' Item'"></span>
                </div>

                <!-- Payment Method Tabs -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Pilih Metode Pembayaran:</label>
                    <div class="grid grid-cols-4 gap-2">
                        <button type="button" @click="setPaymentMethod('cash')"
                                :class="paymentMethod === 'cash' ? 'bg-gradient-to-r from-sky-500 to-cyan-500 text-white shadow-md shadow-sky-500/20' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                class="p-2.5 border rounded-2xl font-bold text-xs flex flex-col items-center gap-1 transition-all">
                            <i data-lucide="banknote" class="w-4 h-4"></i>
                            <span>Tunai</span>
                        </button>

                        <button type="button" @click="setPaymentMethod('qris')"
                                :class="paymentMethod === 'qris' ? 'bg-gradient-to-r from-sky-500 to-cyan-500 text-white shadow-md shadow-sky-500/20' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                class="p-2.5 border rounded-2xl font-bold text-xs flex flex-col items-center gap-1 transition-all">
                            <i data-lucide="qr-code" class="w-4 h-4"></i>
                            <span>QRIS</span>
                        </button>

                        <button type="button" @click="setPaymentMethod('transfer')"
                                :class="paymentMethod === 'transfer' ? 'bg-gradient-to-r from-sky-500 to-cyan-500 text-white shadow-md shadow-sky-500/20' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                class="p-2.5 border rounded-2xl font-bold text-xs flex flex-col items-center gap-1 transition-all">
                            <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                            <span>Transfer</span>
                        </button>

                        <button type="button" @click="setPaymentMethod('debit')"
                                :class="paymentMethod === 'debit' ? 'bg-gradient-to-r from-sky-500 to-cyan-500 text-white shadow-md shadow-sky-500/20' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                class="p-2.5 border rounded-2xl font-bold text-xs flex flex-col items-center gap-1 transition-all">
                            <i data-lucide="credit-card" class="w-4 h-4"></i>
                            <span>Debit/Kartu</span>
                        </button>
                    </div>
                </div>

                <!-- Cash Payment Fields -->
                <div x-show="paymentMethod === 'cash'" class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Uang Diterima (Rp):</label>
                        <input type="number" x-model.number="paidAmount" @input="updateChange()"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-2xl text-base font-bold font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-sky-500">
                    </div>

                    <!-- Cash Preset Quick Buttons -->
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <button type="button" @click="paidAmount = grandTotal; updateChange(); window.soundboard.playTick(true)" 
                                class="px-3 py-1 text-xs font-bold bg-sky-100 hover:bg-sky-200 text-sky-800 rounded-xl">
                            Uang Pas
                        </button>
                        <button type="button" @click="addCashPreset(50000)" 
                                class="px-3 py-1 text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-mono">
                            +50rb
                        </button>
                        <button type="button" @click="addCashPreset(100000)" 
                                class="px-3 py-1 text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-mono">
                            +100rb
                        </button>
                        <button type="button" @click="addCashPreset(500000)" 
                                class="px-3 py-1 text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-mono">
                            +500rb
                        </button>
                    </div>

                    <!-- Change / Kembalian Calculation Callout -->
                    <div class="p-3.5 rounded-2xl border"
                         :class="changeAmount >= 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800'">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold">Kembalian:</span>
                            <span class="text-lg font-extrabold font-mono" 
                                  x-text="changeAmount >= 0 ? 'Rp ' + formatNumber(changeAmount) : 'Kurang Rp ' + formatNumber(Math.abs(changeAmount))"></span>
                        </div>
                    </div>
                </div>

                <!-- Non-Cash Reference Field -->
                <div x-show="paymentMethod !== 'cash'">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Referensi / Approval Code:</label>
                    <input type="text" x-model="paymentReference" placeholder="Contoh: QRIS-89471928"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-2xl text-sm font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-sky-500">
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2.5">
                <button type="button" @click="showPaymentModal = false"
                        class="px-4 py-2.5 rounded-2xl text-xs font-bold text-slate-600 hover:bg-slate-200 transition-colors">
                    Batal (ESC)
                </button>
                <button type="button" @click="submitCheckout()"
                        :disabled="isSubmitting || (paymentMethod === 'cash' && changeAmount < 0)"
                        class="px-6 py-2.5 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 shadow-md shadow-emerald-500/20 disabled:opacity-50 transition-all flex items-center gap-1.5">
                    <span x-show="!isSubmitting">Selesaikan & Cetak Struk</span>
                    <span x-show="isSubmitting">Memproses...</span>
                    <i data-lucide="check" class="w-4 h-4"></i>
                </button>
            </div>

        </div>
    </div>

    <!-- ================= RECEIPT PREVIEW MODAL ================= -->
    <div x-show="showReceiptModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-navy-950/75 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 w-full max-w-sm overflow-hidden flex flex-col">
            <div class="p-3 bg-gradient-to-r from-navy-950 to-navy-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="printer" class="w-4 h-4 text-cyan-400"></i>
                    <h3 class="font-bold text-xs uppercase tracking-wider">Struk Penjualan Kasir</h3>
                </div>
                <button @click="showReceiptModal = false" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Thermal Receipt Frame -->
            <div class="p-3 bg-slate-100 flex justify-center">
                <iframe id="receiptIframe" :src="receiptUrl" class="w-full h-96 bg-white rounded-xl shadow border border-slate-200"></iframe>
            </div>

            <div class="p-3 bg-white border-t border-slate-200 flex items-center justify-between gap-2">
                <button type="button" @click="showReceiptModal = false" 
                        class="px-3 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">
                    Tutup
                </button>
                <button type="button" @click="printReceiptIframe()"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-sky-500 to-cyan-600 hover:from-sky-400 hover:to-cyan-500 shadow flex items-center gap-1.5">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                    Cetak Struk Thermal
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function posApp() {
        return {
            products: @json($products),
            filteredProducts: [],
            searchQuery: '',
            selectedCategory: 'all',
            cart: [],
            customerName: '',
            discount: 0,
            paymentMethod: 'cash',
            paidAmount: 0,
            changeAmount: 0,
            paymentReference: '',
            showPaymentModal: false,
            showReceiptModal: false,
            receiptUrl: '',
            isSubmitting: false,
            currentTime: '',
            isMuted: false,

            init() {
                this.filteredProducts = this.products;
                this.updateTime();
                setInterval(() => this.updateTime(), 1000);

                // Keyboard listeners
                window.addEventListener('keydown', (e) => {
                    if (e.key === 'F2') {
                        e.preventDefault();
                        this.$refs.searchInput.focus();
                        this.$refs.searchInput.select();
                    } else if (e.key === 'F4') {
                        e.preventDefault();
                        if (this.cart.length > 0) {
                            this.openPaymentModal();
                        }
                    } else if (e.key === 'Escape') {
                        this.showPaymentModal = false;
                        this.showReceiptModal = false;
                    }
                });

                this.$nextTick(() => {
                    this.$refs.searchInput.focus();
                });
            },

            updateTime() {
                const now = new Date();
                this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            },

            toggleMute() {
                window.soundboard.muted = this.isMuted;
            },

            testSound(type) {
                if (type === 'chime') window.soundboard.playProductChime();
                else if (type === 'beep') window.soundboard.playBarcodeBeep();
                else if (type === 'chaching') window.soundboard.playChaChing();
                else if (type === 'error') window.soundboard.playError();
            },

            getPastelCategoryClass(code) {
                const map = {
                    'ALB': 'bg-indigo-50 text-indigo-700 border border-indigo-200',
                    'PC': 'bg-pink-50 text-pink-700 border border-pink-200',
                    'DLL': 'bg-teal-50 text-teal-700 border border-teal-200',
                    'KCH': 'bg-sky-50 text-sky-700 border border-sky-200',
                    'APP': 'bg-amber-50 text-amber-700 border border-amber-200',
                    'FTW': 'bg-blue-50 text-blue-700 border border-blue-200',
                    'COL': 'bg-purple-50 text-purple-700 border border-purple-200',
                };
                return map[code] || 'bg-slate-100 text-slate-700 border border-slate-200';
            },

            filterProducts() {
                const q = this.searchQuery.trim().toLowerCase();
                this.filteredProducts = this.products.filter(p => {
                    const matchCategory = (this.selectedCategory === 'all') || (p.category_id == this.selectedCategory);
                    const matchQuery = !q || 
                        p.name.toLowerCase().includes(q) || 
                        p.barcode.toLowerCase().includes(q) || 
                        p.sku.toLowerCase().includes(q) ||
                        (p.group_artist && p.group_artist.toLowerCase().includes(q));
                    return matchCategory && matchQuery;
                });
            },

            handleBarcodeScan() {
                const code = this.searchQuery.trim();
                if (!code) return;

                // Play scanner beep
                window.soundboard.playBarcodeBeep();

                // Match exact barcode or SKU first
                const exactMatch = this.products.find(p => p.barcode === code || p.sku.toLowerCase() === code.toLowerCase());
                if (exactMatch) {
                    this.addToCartWithSound(exactMatch);
                    this.searchQuery = '';
                    this.filterProducts();
                    return;
                }

                if (this.filteredProducts.length === 1) {
                    this.addToCartWithSound(this.filteredProducts[0]);
                    this.searchQuery = '';
                    this.filterProducts();
                }
            },

            // Soundboard Triggered on Product Selection!
            addToCartWithSound(product, evt = null) {
                if (product.stock <= 0) {
                    window.soundboard.playError();
                    alert('Maaf, stok merchandise ini habis!');
                    return;
                }

                // Play pleasant two-tone chime from soundboard
                window.soundboard.playProductChime();

                // Click animation bounce
                if (evt && evt.currentTarget) {
                    evt.currentTarget.classList.add('click-pop');
                    setTimeout(() => evt.currentTarget.classList.remove('click-pop'), 250);
                }

                const existing = this.cart.find(i => i.id === product.id);
                if (existing) {
                    if (existing.quantity >= product.stock) {
                        window.soundboard.playError();
                        alert('Jumlah pesanan sudah mencapai batas maksimum stok tersedia (' + product.stock + ').');
                        return;
                    }
                    existing.quantity++;
                } else {
                    this.cart.push({
                        id: product.id,
                        name: product.name,
                        sku: product.sku,
                        price: parseFloat(product.selling_price),
                        stock: product.stock,
                        quantity: 1,
                    });
                }
            },

            incrementQty(index) {
                const item = this.cart[index];
                if (item.quantity >= item.stock) {
                    window.soundboard.playError();
                    alert('Stok maksimum tersedia: ' + item.stock);
                    return;
                }
                window.soundboard.playTick(true);
                item.quantity++;
            },

            decrementQty(index) {
                window.soundboard.playTick(false);
                if (this.cart[index].quantity > 1) {
                    this.cart[index].quantity--;
                } else {
                    this.removeFromCart(index);
                }
            },

            removeFromCart(index) {
                window.soundboard.playTick(false);
                this.cart.splice(index, 1);
            },

            clearCart() {
                if (confirm('Yakin ingin mengosongkan seluruh item di keranjang?')) {
                    this.cart = [];
                    this.discount = 0;
                    window.soundboard.playTick(false);
                }
            },

            get cartItemCount() {
                return this.cart.reduce((total, i) => total + i.quantity, 0);
            },

            get subtotal() {
                return this.cart.reduce((total, i) => total + (i.price * i.quantity), 0);
            },

            get grandTotal() {
                return Math.max(0, this.subtotal - (parseFloat(this.discount) || 0));
            },

            openPaymentModal() {
                this.paidAmount = this.grandTotal;
                this.updateChange();
                this.showPaymentModal = true;
                window.soundboard.playTick(true);
            },

            setPaymentMethod(method) {
                this.paymentMethod = method;
                if (method !== 'cash') {
                    this.paidAmount = this.grandTotal;
                }
                this.updateChange();
                window.soundboard.playTick(true);
            },

            addCashPreset(amount) {
                this.paidAmount = (parseFloat(this.paidAmount) || 0) + amount;
                this.updateChange();
                window.soundboard.playTick(true);
            },

            updateChange() {
                const paid = parseFloat(this.paidAmount) || 0;
                this.changeAmount = paid - this.grandTotal;
            },

            async submitCheckout() {
                if (this.cart.length === 0) return;
                if (this.paymentMethod === 'cash' && this.changeAmount < 0) {
                    window.soundboard.playError();
                    alert('Nominal uang tunai kurang dari total tagihan.');
                    return;
                }

                this.isSubmitting = true;

                const payload = {
                    items: this.cart.map(i => ({ product_id: i.id, quantity: i.quantity })),
                    payment_method: this.paymentMethod,
                    paid_amount: this.paidAmount,
                    discount: this.discount,
                    customer_name: this.customerName,
                    payment_reference: this.paymentReference,
                };

                try {
                    const response = await fetch("{{ route('pos.checkout') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    const res = await response.json();

                    if (res.success) {
                        // Play celebratory Cha-Ching bell arpeggio!
                        window.soundboard.playChaChing();

                        // Deduct local product stock
                        this.cart.forEach(cartItem => {
                            const p = this.products.find(prod => prod.id === cartItem.id);
                            if (p) p.stock -= cartItem.quantity;
                        });

                        // Clear cart
                        this.cart = [];
                        this.customerName = '';
                        this.discount = 0;
                        this.showPaymentModal = false;

                        // Show receipt modal
                        this.receiptUrl = res.data.receipt_url;
                        this.showReceiptModal = true;
                    } else {
                        window.soundboard.playError();
                        alert('Gagal memproses transaksi: ' + res.message);
                    }
                } catch (err) {
                    window.soundboard.playError();
                    alert('Terjadi kesalahan jaringan atau server.');
                } finally {
                    this.isSubmitting = false;
                }
            },

            printReceiptIframe() {
                const iframe = document.getElementById('receiptIframe');
                if (iframe && iframe.contentWindow) {
                    iframe.contentWindow.print();
                }
            },

            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num || 0);
            }
        };
    }
</script>
@endpush
@endsection
