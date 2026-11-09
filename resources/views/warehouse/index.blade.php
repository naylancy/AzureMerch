@extends('layouts.app')

@section('title', 'Manajemen Inventaris Gudang')

@section('main-content')
<div x-data="{ showAdjustmentModal: false, selectedProduct: null, actualStock: 0, reason: '', refNo: '' }" 
     class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-navy-900 tracking-tight flex items-center gap-2">
                <i data-lucide="boxes" class="w-6 h-6 text-amber-500"></i>
                Manajemen Inventaris Gudang (Warehouse)
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Pengelolaan katalog merchandise K-Pop, monitoring level stok fisik, dan audit opname.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('warehouse.inbound') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-xs font-bold text-navy-900 bg-white border border-slate-300 hover:bg-slate-50 shadow-sm transition-all">
                <i data-lucide="arrow-down-left" class="w-4 h-4 text-emerald-600"></i>
                Penerimaan Inbound
            </a>

            <a href="{{ route('warehouse.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 shadow-md shadow-cyan-500/20 transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Produk Baru
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Grid (Pastel Ocean Responsive) -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <!-- Total SKU (Pastel Sky) -->
        <div class="bg-gradient-to-br from-white via-white to-sky-50/80 p-4 rounded-3xl border border-sky-200/80 shadow-sm pastel-card-hover">
            <div class="flex items-center justify-between text-slate-400 mb-1">
                <span class="text-xs font-bold uppercase tracking-wider text-sky-700">Total SKU</span>
                <div class="w-7 h-7 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center">
                    <i data-lucide="tag" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-navy-900 font-mono">{{ number_format($kpis['total_sku']) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Item aktif di katalog</div>
        </div>

        <!-- Total Units in Stock (Pastel Lavender) -->
        <div class="bg-gradient-to-br from-white via-white to-indigo-50/80 p-4 rounded-3xl border border-indigo-200/80 shadow-sm pastel-card-hover">
            <div class="flex items-center justify-between text-slate-400 mb-1">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-700">Total Stok Fisik</span>
                <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="layers" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-navy-900 font-mono">{{ number_format($kpis['total_units']) }} <span class="text-xs font-normal text-slate-500">pcs</span></div>
            <div class="text-[11px] text-slate-400 mt-0.5">Semua kategori</div>
        </div>

        <!-- Low Stock Warning (Pastel Peach Amber) -->
        <div class="bg-gradient-to-br from-white via-white to-amber-50/80 p-4 rounded-3xl border border-amber-200/80 shadow-sm pastel-card-hover {{ $kpis['low_stock_count'] > 0 ? 'ring-2 ring-amber-300' : '' }}">
            <div class="flex items-center justify-between text-slate-400 mb-1">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Stok Menipis</span>
                <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-600 font-mono">{{ number_format($kpis['low_stock_count']) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Perlu restock supplier</div>
        </div>

        <!-- Out of Stock (Pastel Rose) -->
        <div class="bg-gradient-to-br from-white via-white to-rose-50/80 p-4 rounded-3xl border border-rose-200/80 shadow-sm pastel-card-hover {{ $kpis['out_of_stock_count'] > 0 ? 'ring-2 ring-rose-300' : '' }}">
            <div class="flex items-center justify-between text-slate-400 mb-1">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-700">Stok Habis</span>
                <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center">
                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-rose-600 font-mono">{{ number_format($kpis['out_of_stock_count']) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Stok 0 di gudang</div>
        </div>

        <!-- Valuation (Pastel Ocean Glow) -->
        <div class="col-span-2 lg:col-span-1 bg-gradient-to-br from-navy-950 via-navy-900 to-navy-850 text-white p-4 rounded-3xl border border-cyan-400/30 shadow-lg pastel-card-hover relative overflow-hidden">
            <div class="flex items-center justify-between text-cyan-300 mb-1">
                <span class="text-xs font-bold uppercase tracking-wider">Valuasi Stok</span>
                <div class="w-7 h-7 rounded-lg bg-white/10 text-cyan-300 flex items-center justify-center">
                    <i data-lucide="coins" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-xl 2xl:text-2xl font-black font-mono text-cyan-200">
                Rp {{ number_format($kpis['total_valuation'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-300 mt-0.5">Berdasarkan modal beli</div>
        </div>
    </div>

    <!-- Filter & Search Toolbar Card -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('warehouse.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            
            <!-- Search Text -->
            <div class="relative sm:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari SKU, Nama Merchandise, Barcode, atau Artis..." 
                       class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
            </div>

            <!-- Category Filter -->
            <div>
                <select name="category_id" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->products_count }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Stock Filter & Buttons -->
            <div class="flex items-center gap-2">
                <select name="stock_filter" class="flex-1 py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    <option value="">Status Stok: Semua</option>
                    <option value="low" {{ request('stock_filter') == 'low' ? 'selected' : '' }}>⚠️ Stok Menipis</option>
                    <option value="out" {{ request('stock_filter') == 'out' ? 'selected' : '' }}>❌ Stok Habis (0)</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-navy-900 hover:bg-navy-800 text-white font-bold text-xs rounded-xl transition-colors">
                    Filter
                </button>
            </div>

        </form>
    </div>

    <!-- Products Responsive Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold text-[10px]">
                        <th class="py-3 px-4">Item & Info</th>
                        <th class="py-3 px-4">Kategori & Artis</th>
                        <th class="py-3 px-4 text-right">Harga Modal</th>
                        <th class="py-3 px-4 text-right">Harga Jual</th>
                        <th class="py-3 px-4 text-center">Stok Fisik</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            
                            <!-- Product Name & Thumbnail -->
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-slate-100 overflow-hidden flex-shrink-0 border border-slate-200">
                                        <img src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?auto=format&fit=crop&w=150&q=80' }}" 
                                             alt="{{ $product->name }}" 
                                             class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 hover:text-cyan-700 leading-tight">
                                            {{ $product->name }}
                                        </div>
                                        <div class="flex items-center gap-2 text-[10px] text-slate-400 font-mono mt-0.5">
                                            <span class="px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 font-semibold">{{ $product->sku }}</span>
                                            <span>•</span>
                                            <span>Barcode: {{ $product->barcode }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Category & Artist -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="font-semibold text-slate-800">{{ $product->category->name }}</div>
                                <div class="text-[10px] text-cyan-700 font-bold uppercase tracking-wider">
                                    {{ $product->group_artist ?: 'Umum' }}
                                </div>
                            </td>

                            <!-- Cost Price -->
                            <td class="py-3 px-4 text-right font-mono text-slate-500 whitespace-nowrap">
                                Rp {{ number_format($product->cost_price, 0, ',', '.') }}
                            </td>

                            <!-- Selling Price -->
                            <td class="py-3 px-4 text-right font-mono font-bold text-navy-900 whitespace-nowrap">
                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                <div class="text-[10px] text-emerald-600 font-bold font-sans">
                                    Margin: {{ $product->margin_percent }}%
                                </div>
                            </td>

                            <!-- Stock Count -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-mono font-black text-xs
                                    {{ $product->stock <= 0 ? 'bg-rose-100 text-rose-800' : ($product->isLowStock() ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">
                                    {{ $product->stock }} pcs
                                </span>
                                <div class="text-[10px] text-slate-400 mt-0.5">Min: {{ $product->min_stock_alert }}</div>
                            </td>

                            <!-- Active Status -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $product->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Opname Adjustment Button -->
                                    <button type="button" 
                                            @click="selectedProduct = {{ $product->toJson() }}; actualStock = {{ $product->stock }}; reason = ''; refNo = ''; showAdjustmentModal = true"
                                            title="Penyesuaian Stok Opname"
                                            class="p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 transition-colors">
                                        <i data-lucide="sliders" class="w-3.5 h-3.5"></i>
                                    </button>

                                    <!-- Edit Button -->
                                    <a href="{{ route('warehouse.edit', $product->id) }}" 
                                       title="Edit Data Produk"
                                       class="p-1.5 rounded-lg bg-slate-100 hover:bg-cyan-50 text-slate-700 hover:text-cyan-700 border border-slate-200 transition-colors">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <form method="POST" action="{{ route('warehouse.destroy', $product->id) }}" class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus atau menonaktifkan produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Produk"
                                                class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-700 border border-slate-200 transition-colors">
                                            <i data-lucide="trash" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i data-lucide="package-x" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                                Tidak ada produk merchandise yang cocok dengan filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- ================= STOCK OPNAME ADJUSTMENT MODAL ================= -->
    <div x-show="showAdjustmentModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-navy-950/70 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="showAdjustmentModal = false" 
             class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
            
            <div class="p-4 bg-navy-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="sliders" class="w-5 h-5 text-amber-400"></i>
                    <h3 class="font-bold text-sm">Penyesuaian Stok Opname (Koreksi)</h3>
                </div>
                <button @click="showAdjustmentModal = false" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('warehouse.adjustment') }}" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="product_id" :value="selectedProduct ? selectedProduct.id : ''">

                <!-- Product Snapshot Info -->
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="text-[10px] text-slate-400 font-mono" x-text="selectedProduct ? selectedProduct.sku : ''"></div>
                    <div class="text-xs font-bold text-navy-900" x-text="selectedProduct ? selectedProduct.name : ''"></div>
                    <div class="flex items-center justify-between mt-1 text-xs">
                        <span class="text-slate-500">Stok Sistem Tercatat:</span>
                        <span class="font-mono font-bold text-slate-900" x-text="(selectedProduct ? selectedProduct.stock : 0) + ' pcs'"></span>
                    </div>
                </div>

                <!-- Actual Count Input -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Jumlah Stok Fisik Riil (Hasil Opname):
                    </label>
                    <input type="number" name="actual_stock" x-model.number="actualStock" min="0" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl font-mono text-base font-bold text-navy-900 focus:bg-white focus:ring-2 focus:ring-cyan-500">
                    
                    <div class="mt-1 text-[11px] font-semibold"
                         :class="(actualStock - (selectedProduct ? selectedProduct.stock : 0)) >= 0 ? 'text-emerald-600' : 'text-rose-600'">
                        Selisih: <span x-text="((actualStock - (selectedProduct ? selectedProduct.stock : 0)) >= 0 ? '+' : '') + (actualStock - (selectedProduct ? selectedProduct.stock : 0)) + ' pcs'"></span>
                    </div>
                </div>

                <!-- Reason / Notes -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Alasan Penyesuaian (Audit Discrepancy):
                    </label>
                    <select name="reason" x-model="reason" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-cyan-500 mb-2">
                        <option value="">Pilih Alasan Koreksi...</option>
                        <option value="Hasil Stock Opname Bulanan Fisik Cocok">Hasil Stock Opname Bulanan</option>
                        <option value="Barang Rusak / Cacat Pabrik (Damaged in Store)">Barang Rusak / Cacat</option>
                        <option value="Selisih Hitung Barang Masuk (Discrepancy)">Selisih Hitung Barang Masuk</option>
                        <option value="Sampel Pajangan Toko (Display / Sample)">Digunakan untuk Display / Sample Toko</option>
                        <option value="Koreksi Salah Input Data Sebelumnya">Koreksi Salah Input Data</option>
                    </select>
                </div>

                <!-- Ref Number -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Referensi Dokumen Opname (Opsional):</label>
                    <input type="text" name="reference_number" x-model="refNo" placeholder="Contoh: OPNAME-OKT-2026-01"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono focus:bg-white focus:ring-2 focus:ring-cyan-500">
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" @click="showAdjustmentModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-500 shadow-md">
                        Simpan Penyesuaian
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
