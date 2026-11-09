@extends('layouts.app')

@section('title', 'Log Mutasi Stok Gudang')

@section('main-content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-navy-900 tracking-tight flex items-center gap-2">
                <i data-lucide="file-spreadsheet" class="w-6 h-6 text-cyan-600"></i>
                Log Audit Mutasi Stok Gudang
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Rekam jejak setiap pergerakan stok: barang masuk (inbound), penjualan POS kasir, dan penyesuaian opname.
            </p>
        </div>

        <a href="{{ route('warehouse.index') }}" 
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-sm transition-all self-start sm:self-auto">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Inventaris
        </a>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('warehouse.logs') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            
            <!-- Type Filter -->
            <div>
                <select name="type" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    <option value="">Semua Tipe Mutasi</option>
                    <option value="inbound" {{ request('type') == 'inbound' ? 'selected' : '' }}>Inbound (Penerimaan Supplier)</option>
                    <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>Penjualan (Kasir POS)</option>
                    <option value="adjustment_plus" {{ request('type') == 'adjustment_plus' ? 'selected' : '' }}>Penyesuaian (+ Opname Tambah)</option>
                    <option value="adjustment_minus" {{ request('type') == 'adjustment_minus' ? 'selected' : '' }}>Penyesuaian (- Opname Berkurang)</option>
                </select>
            </div>

            <!-- Product Filter -->
            <div>
                <select name="product_id" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    <option value="">Semua Produk Merchandise</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                            [{{ $p->sku }}] {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-5 py-2 bg-navy-900 hover:bg-navy-800 text-white font-bold text-xs rounded-xl transition-colors">
                    Terapkan Filter
                </button>
                <a href="{{ route('warehouse.logs') }}" class="px-3 py-2 text-slate-500 hover:text-slate-800 text-xs font-semibold">
                    Reset
                </a>
            </div>

        </form>
    </div>

    <!-- Logs Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold text-[10px]">
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Tipe Mutasi</th>
                        <th class="py-3 px-4">Merchandise</th>
                        <th class="py-3 px-4">No. Referensi</th>
                        <th class="py-3 px-4 text-center">Perubahan</th>
                        <th class="py-3 px-4 text-center">Sebelum / Sesudah</th>
                        <th class="py-3 px-4">Petugas & Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50 transition-colors">
                            
                            <!-- Timestamp -->
                            <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </td>

                            <!-- Mutation Type Badge -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    {{ $log->type === 'inbound' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $log->type === 'sale' ? 'bg-cyan-100 text-cyan-800' : '' }}
                                    {{ $log->type === 'adjustment_plus' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $log->type === 'adjustment_minus' ? 'bg-rose-100 text-rose-800' : '' }}">
                                    {{ $log->type_label }}
                                </span>
                            </td>

                            <!-- Product Info -->
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 leading-tight">
                                    {{ $log->product->name }}
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono">
                                    {{ $log->product->sku }}
                                </div>
                            </td>

                            <!-- Ref Number -->
                            <td class="py-3 px-4 font-mono font-semibold text-slate-700 whitespace-nowrap">
                                {{ $log->reference_number ?: '-' }}
                            </td>

                            <!-- Quantity Change -->
                            <td class="py-3 px-4 text-center whitespace-nowrap font-mono font-bold text-sm
                                {{ $log->quantity_change > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ ($log->quantity_change > 0 ? '+' : '') . $log->quantity_change }}
                            </td>

                            <!-- Before / After -->
                            <td class="py-3 px-4 text-center whitespace-nowrap font-mono text-slate-600">
                                <span>{{ $log->stock_before }}</span>
                                <span class="text-slate-400 mx-1">➔</span>
                                <span class="font-bold text-navy-900">{{ $log->stock_after }}</span>
                            </td>

                            <!-- User & Notes -->
                            <td class="py-3 px-4 text-xs">
                                <div class="font-bold text-slate-800">{{ $log->user ? $log->user->name : 'Sistem' }}</div>
                                <div class="text-[11px] text-slate-500 truncate max-w-xs">{{ $log->notes }}</div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i data-lucide="file-x" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                Tidak ada catatan mutasi stok yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
