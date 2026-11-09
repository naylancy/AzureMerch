@extends('layouts.app')

@section('title', 'Penerimaan Stok Masuk (Inbound)')

@section('main-content')
<div x-data="inboundApp()" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-navy-900 tracking-tight flex items-center gap-2">
                <i data-lucide="arrow-down-left" class="w-6 h-6 text-emerald-600"></i>
                Penerimaan Pengiriman Supplier (Inbound)
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Catat barang masuk dari distributor/supplier K-Pop resmi dan mutasikan stok.</p>
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
                Terdapat kesalahan pengisian data:
            </div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Inbound Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form method="POST" action="{{ route('warehouse.process-inbound') }}" class="p-6 space-y-6">
            @csrf

            <!-- Section 1: Dokumen Pengiriman -->
            <div>
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-emerald-700 border-b border-slate-100 pb-2 mb-4 flex items-center gap-2">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    1. Informasi Dokumen Pengiriman Supplier
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Supplier / Distributor <span class="text-rose-500">*</span></label>
                        <input type="text" name="supplier_name" value="{{ old('supplier_name', 'HYBE Select / Weverse Korea') }}" required 
                               placeholder="Contoh: HYBE MD / YG Select / JYP Official"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">No. Surat Jalan / Invoice Supplier <span class="text-rose-500">*</span></label>
                        <input type="text" name="reference_number" value="{{ old('reference_number', 'INB-' . date('Ymd') . '-01') }}" required 
                               placeholder="Contoh: DO-KR-20261020-009"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Section 2: Pemilihan Produk & Jumlah -->
            <div>
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-emerald-700 border-b border-slate-100 pb-2 mb-4 flex items-center gap-2">
                    <i data-lucide="package" class="w-4 h-4"></i>
                    2. Produk Merchandise Diterima
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Produk Merchandise <span class="text-rose-500">*</span></label>
                        <select name="product_id" x-model="selectedProductId" @change="onProductSelect()" required 
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="">-- Pilih Produk dari Katalog --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" data-stock="{{ $p->stock }}" data-cost="{{ $p->cost_price }}" data-sku="{{ $p->sku }}">
                                    [{{ $p->sku }}] {{ $p->name }} (Stok saat ini: {{ $p->stock }} pcs)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Live Stock Simulation Box -->
                    <div x-show="selectedProduct" class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase font-bold">Stok Sekarang</span>
                                <div class="text-xl font-bold font-mono text-slate-700" x-text="currentStock + ' pcs'"></div>
                            </div>
                            <div class="border-x border-slate-200">
                                <span class="text-[10px] text-emerald-600 uppercase font-bold">+ Barang Masuk</span>
                                <div class="text-xl font-bold font-mono text-emerald-600" x-text="'+ ' + (quantity || 0) + ' pcs'"></div>
                            </div>
                            <div>
                                <span class="text-[10px] text-cyan-600 uppercase font-bold">= Stok Baru</span>
                                <div class="text-xl font-black font-mono text-navy-900" x-text="(parseInt(currentStock || 0) + parseInt(quantity || 0)) + ' pcs'"></div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Masuk (Kuantitas Fisik) <span class="text-rose-500">*</span></label>
                            <input type="number" name="quantity" x-model.number="quantity" min="1" required 
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono font-bold text-navy-900 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Harga Beli Baru per Unit (Opsional, isi jika ada perubahan modal):</label>
                            <input type="number" name="unit_cost" x-model.number="unitCost" min="0" step="1000" 
                                   placeholder="Rp Kosongkan jika tetap sama"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Penerimaan / No. Kontainer / Batch Lot:</label>
                        <textarea name="notes" rows="2" 
                                  placeholder="Contoh: Shipment batch #3 dari Seoul Incheon via Air Cargo, kondisi segel pabrik utuh."
                                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                <a href="{{ route('warehouse.index') }}" 
                   class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-500/20 active:scale-95 transition-all flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    Simpan & Tambah Stok Masuk
                </button>
            </div>

        </form>
    </div>

</div>

@push('scripts')
<script>
    function inboundApp() {
        return {
            selectedProductId: '{{ old('product_id') }}',
            selectedProduct: false,
            currentStock: 0,
            quantity: 10,
            unitCost: '',

            onProductSelect() {
                const select = document.querySelector('select[name="product_id"]');
                const opt = select.options[select.selectedIndex];
                if (opt && opt.value) {
                    this.selectedProduct = true;
                    this.currentStock = parseInt(opt.getAttribute('data-stock') || 0);
                    this.unitCost = parseFloat(opt.getAttribute('data-cost') || 0);
                } else {
                    this.selectedProduct = false;
                    this.currentStock = 0;
                }
            }
        };
    }
</script>
@endpush
@endsection
