@extends('layouts.app')

@section('title', 'Riwayat Shift Kasir')

@section('main-content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-navy-900 tracking-tight flex items-center gap-2">
                <i data-lucide="history" class="w-6 h-6 text-cyan-600"></i>
                Riwayat Transaksi Shift Kasir
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftar transaksi penjualan yang telah Anda proses pada shift aktif.</p>
        </div>

        <a href="{{ route('pos.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-500 shadow-md shadow-cyan-500/20 transition-all self-start sm:self-auto">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Terminal POS
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold text-[10px]">
                        <th class="py-3 px-4">No. Faktur</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Item Terjual</th>
                        <th class="py-3 px-4">Metode Bayar</th>
                        <th class="py-3 px-4 text-right">Total Transaksi</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-navy-900">
                                {{ $tx->invoice_number }}
                            </td>
                            <td class="py-3 px-4 text-slate-500">
                                {{ $tx->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="py-3 px-4 text-slate-800 font-semibold">
                                {{ $tx->customer_name }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold font-mono">
                                    {{ $tx->items->sum('quantity') }} pcs
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded uppercase font-bold text-[10px] 
                                    {{ $tx->payment_method === 'cash' ? 'bg-emerald-100 text-emerald-800' : 'bg-cyan-100 text-cyan-800' }}">
                                    {{ $tx->payment_method }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-navy-900 font-mono">
                                Rp {{ number_format($tx->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('pos.receipt', $tx->id) }}" target="_blank"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-navy-50 hover:bg-navy-100 text-navy-800 font-bold text-[11px] border border-navy-200 transition-colors">
                                    <i data-lucide="printer" class="w-3.5 h-3.5 text-cyan-600"></i>
                                    Cetak Struk
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                Belum ada riwayat transaksi pada shift ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
