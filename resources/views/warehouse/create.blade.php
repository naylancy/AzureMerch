@extends('layouts.app')

@section('title', 'Tambah Produk Merchandise Baru')

@section('main-content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Top Bar -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-navy-900 tracking-tight flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-6 h-6 text-cyan-600"></i>
                Tambah Produk Merchandise K-Pop
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftarkan item baru ke database inventaris AzureMerch.</p>
        </div>

        <a href="{{ route('warehouse.index') }}" 
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-sm transition-all">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
            <div class="font-bold flex items-center gap-1.5 mb-1">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                Perhatian: Terdapat kesalahan input data:
            </div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form method="POST" action="{{ route('warehouse.store') }}" class="p-6 space-y-6">
            @csrf

            <!-- Section 1: Identitas Produk -->
            <div>
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-cyan-700 border-b border-slate-100 pb-2 mb-4 flex items-center gap-2">
                    <i data-lucide="info" class="w-4 h-4"></i>
                    1. Identitas & Kategori Merchandise
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Product Name -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Produk Merchandise <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required 
                               placeholder="Contoh: BTS - Proof (Standard Edition Album)"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Barang <span class="text-rose-500">*</span></label>
                        <select name="category_id" required 
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                            <option value="">Pilih Kategori...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }} ({{ $cat->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Group / Idol Artist -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Grup Idol / Artis</label>
                        <input type="text" name="group_artist" value="{{ old('group_artist') }}" 
                               placeholder="Contoh: BTS, BLACKPINK, NewJeans, Stray Kids"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <!-- SKU -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kode SKU <span class="text-rose-500">*</span></label>
                        <input type="text" name="sku" value="{{ old('sku') }}" required 
                               placeholder="Contoh: ALB-BTS-001"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono uppercase focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <!-- Barcode / QR -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kode Barcode / QR Scanner <span class="text-rose-500">*</span></label>
                        <input type="text" name="barcode" value="{{ old('barcode') }}" required 
                               placeholder="Contoh: 8809755508891"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Section 2: Finansial & Harga -->
            <div>
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-cyan-700 border-b border-slate-100 pb-2 mb-4 flex items-center gap-2">
                    <i data-lucide="tag" class="w-4 h-4"></i>
                    2. Penetapan Harga & Profit Margin
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Cost Price -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga Beli / Modal (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="cost_price" value="{{ old('cost_price') }}" min="0" step="1000" required 
                               placeholder="Contoh: 250000"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <!-- Selling Price -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga Jual Ritel (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="selling_price" value="{{ old('selling_price') }}" min="0" step="1000" required 
                               placeholder="Contoh: 380000"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold text-navy-900 focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Section 3: Manajemen Stok Awal -->
            <div>
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-cyan-700 border-b border-slate-100 pb-2 mb-4 flex items-center gap-2">
                    <i data-lucide="layers" class="w-4 h-4"></i>
                    3. Kuantitas Stok Awal & Peringatan
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Stok Fisik Awal (pcs) <span class="text-rose-500">*</span></label>
                        <input type="number" name="stock" value="{{ old('stock', 10) }}" min="0" required 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Batas Minimum Peringatan Stok <span class="text-rose-500">*</span></label>
                        <input type="number" name="min_stock_alert" value="{{ old('min_stock_alert', 5) }}" min="1" required 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">No. Invoice / Surat Jalan Supplier</label>
                        <input type="text" name="supplier_reference" value="{{ old('supplier_reference', 'SUPP-NEW-' . date('Ymd')) }}" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Section 4: Gambar & Deskripsi -->
            <div>
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-cyan-700 border-b border-slate-100 pb-2 mb-4 flex items-center gap-2">
                    <i data-lucide="image" class="w-4 h-4"></i>
                    4. Media & Deskripsi Produk
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">URL Gambar Produk (Image URL)</label>
                        <input type="url" name="image_url" value="{{ old('image_url') }}" 
                               placeholder="https://images.unsplash.com/..."
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi & Kelengkapan Merchandise</label>
                        <textarea name="description" rows="3" 
                                  placeholder="Contoh: Official photobook 80 halaman, 1 CD audio, 2 random photocard, dan poster lipat."
                                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                <a href="{{ route('warehouse.index') }}" 
                   class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 shadow-md shadow-cyan-500/20 active:scale-95 transition-all flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    Simpan Produk ke Gudang
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
