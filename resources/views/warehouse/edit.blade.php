@extends('layouts.app')

@section('title', 'Edit Produk - ' . $product->name)

@section('main-content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Top Bar -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-navy-900 tracking-tight flex items-center gap-2">
                <i data-lucide="edit-3" class="w-6 h-6 text-cyan-600"></i>
                Edit Data Merchandise
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Memperbarui rincian produk SKU: <strong class="font-mono text-cyan-700">{{ $product->sku }}</strong></p>
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
        <form method="POST" action="{{ route('warehouse.update', $product->id) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

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
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Barang <span class="text-rose-500">*</span></label>
                        <select name="category_id" required 
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }} ({{ $cat->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Group / Idol Artist -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Grup Idol / Artis</label>
                        <input type="text" name="group_artist" value="{{ old('group_artist', $product->group_artist) }}" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <!-- SKU -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kode SKU <span class="text-rose-500">*</span></label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono uppercase focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <!-- Barcode / QR -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kode Barcode / QR Scanner <span class="text-rose-500">*</span></label>
                        <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" required 
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
                        <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" min="0" step="1000" required 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <!-- Selling Price -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga Jual Ritel (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" min="0" step="1000" required 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold text-navy-900 focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Section 3: Monitoring Stok -->
            <div>
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-cyan-700 border-b border-slate-100 pb-2 mb-4 flex items-center gap-2">
                    <i data-lucide="layers" class="w-4 h-4"></i>
                    3. Pengaturan Minimum Stok & Status
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Batas Minimum Peringatan Stok <span class="text-rose-500">*</span></label>
                        <input type="number" name="min_stock_alert" value="{{ old('min_stock_alert', $product->min_stock_alert) }}" min="1" required 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <div class="pt-5">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} 
                                   class="w-4 h-4 rounded text-cyan-600 focus:ring-cyan-500 border-slate-300">
                            <span class="text-xs font-bold text-slate-700">Produk Aktif untuk Transaksi Kasir POS</span>
                        </label>
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
                        <label class="block text-xs font-bold text-slate-700 mb-1">URL Gambar Produk</label>
                        <input type="url" name="image_url" value="{{ old('image_url', $product->image_url) }}" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi & Kelengkapan Merchandise</label>
                        <textarea name="description" rows="3" 
                                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">{{ old('description', $product->description) }}</textarea>
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
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
