@extends('layouts.app')

@section('title', 'Log Audit Sistem & Mutasi Stok')

@section('main-content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-navy-900 tracking-tight flex items-center gap-2">
                <i data-lucide="shield-check" class="w-6 h-6 text-indigo-600"></i>
                Log Audit Sistem & Pengawasan Mutasi
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Pengawasan integritas inventaris (Read-only): Catatan lengkap setiap perubahan stok barang di toko & gudang.
            </p>
        </div>
    </div>

    <!-- Audit Logs Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-extrabold text-sm text-navy-900">Rekam Jejak Mutasi Inventaris</h3>
            <span class="text-xs text-slate-500 font-mono">Diurutkan berdasarkan waktu terbaru</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold text-[10px]">
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Tipe Aktivitas</th>
                        <th class="py-3 px-4">Produk Merchandise</th>
                        <th class="py-3 px-4">Referensi Dokumen</th>
                        <th class="py-3 px-4 text-center">Perubahan Kuantitas</th>
                        <th class="py-3 px-4 text-center">Stok Sebelum / Sesudah</th>
                        <th class="py-3 px-4">Pelaksana / User</th>
                        <th class="py-3 px-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    {{ $log->type === 'inbound' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $log->type === 'sale' ? 'bg-cyan-100 text-cyan-800' : '' }}
                                    {{ $log->type === 'adjustment_plus' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $log->type === 'adjustment_minus' ? 'bg-rose-100 text-rose-800' : '' }}">
                                    {{ $log->type_label }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $log->product->name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $log->product->sku }}</div>
                            </td>
                            <td class="py-3 px-4 font-mono font-semibold text-slate-700 whitespace-nowrap">
                                {{ $log->reference_number ?: '-' }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap font-mono font-bold text-sm
                                {{ $log->quantity_change > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ ($log->quantity_change > 0 ? '+' : '') . $log->quantity_change }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap font-mono text-slate-600">
                                <span>{{ $log->stock_before }}</span>
                                <span class="text-slate-400 mx-1">➔</span>
                                <span class="font-bold text-navy-900">{{ $log->stock_after }}</span>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-bold text-slate-800">{{ $log->user ? $log->user->name : 'Sistem' }}</span>
                                <span class="text-[10px] block text-slate-400">({{ $log->user ? ucfirst($log->user->role) : 'N/A' }})</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600 text-xs">
                                {{ $log->notes ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <i data-lucide="shield-alert" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                Belum ada catatan log audit mutasi.
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
