@extends('layouts.app')

@section('title', 'Laporan Keuangan & Penjualan')

@section('main-content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-navy-900 tracking-tight flex items-center gap-2">
                <i data-lucide="trending-up" class="w-6 h-6 text-cyan-600"></i>
                Laporan Keuangan & Penjualan Ritel
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Periode: <strong class="text-navy-900">{{ $from->format('d M Y') }}</strong> s/d <strong class="text-navy-900">{{ $to->format('d M Y') }}</strong>
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto no-print">
            <button onclick="window.print()" 
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-sm transition-all">
                <i data-lucide="printer" class="w-4 h-4"></i>
                Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm no-print">
        <form method="GET" action="{{ route('admin.reports') }}" class="flex flex-wrap items-center gap-3">
            
            <!-- Quick Preset Pills -->
            <div class="flex items-center gap-1.5 flex-wrap">
                <a href="{{ route('admin.reports', ['range' => 'today']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $range === 'today' ? 'bg-cyan-500 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Hari Ini
                </a>
                <a href="{{ route('admin.reports', ['range' => 'yesterday']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $range === 'yesterday' ? 'bg-cyan-500 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Kemarin
                </a>
                <a href="{{ route('admin.reports', ['range' => 'this_week']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $range === 'this_week' ? 'bg-cyan-500 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Minggu Ini
                </a>
                <a href="{{ route('admin.reports', ['range' => 'this_month']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $range === 'this_month' ? 'bg-cyan-500 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Bulan Ini
                </a>
            </div>

            <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

            <!-- Custom Date Range -->
            <div class="flex items-center gap-2">
                <input type="hidden" name="range" value="custom">
                <input type="date" name="start_date" value="{{ request('start_date', $from->format('Y-m-d')) }}" 
                       class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs focus:ring-1 focus:ring-cyan-500">
                <span class="text-xs text-slate-400">s/d</span>
                <input type="date" name="end_date" value="{{ request('end_date', $to->format('Y-m-d')) }}" 
                       class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs focus:ring-1 focus:ring-cyan-500">

                <button type="submit" class="px-4 py-1.5 bg-navy-900 hover:bg-navy-800 text-white font-bold text-xs rounded-lg transition-colors">
                    Filter Tanggal
                </button>
            </div>

        </form>
    </div>

    <!-- Aggregate Financial Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Penjualan Kotor</span>
            <div class="text-2xl font-black text-navy-900 font-mono mt-1">
                Rp {{ number_format($summary['total_sales'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">{{ $summary['total_transactions'] }} transaksi</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Harga Pokok (HPP / Modal)</span>
            <div class="text-2xl font-black text-slate-700 font-mono mt-1">
                Rp {{ number_format($summary['total_cost'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">Modal item merchandise terjual</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm bg-gradient-to-br from-white to-emerald-50">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Laba Bersih Kotor</span>
            <div class="text-2xl font-black text-emerald-600 font-mono mt-1">
                Rp {{ number_format($summary['gross_profit'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-emerald-600 font-bold mt-1">Margin: {{ $summary['profit_margin'] }}%</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Diskon Diberikan</span>
            <div class="text-2xl font-black text-rose-600 font-mono mt-1">
                Rp {{ number_format($summary['total_discount'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">Potongan harga promo & event</div>
        </div>

    </div>

    <!-- Transactions Detailed Breakdown Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-extrabold text-sm text-navy-900">Rincian Faktur Penjualan</h3>
            <span class="text-xs text-slate-500 font-mono">{{ $summary['total_transactions'] }} Faktur Ditemukan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold text-[10px]">
                        <th class="py-3 px-4">Faktur & Tanggal</th>
                        <th class="py-3 px-4">Kasir Bertugas</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Rincian Item</th>
                        <th class="py-3 px-4">Metode Bayar</th>
                        <th class="py-3 px-4 text-right">Modal (HPP)</th>
                        <th class="py-3 px-4 text-right">Total Tagihan</th>
                        <th class="py-3 px-4 text-right">Laba Kotor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="font-bold text-navy-900 font-mono">{{ $tx->invoice_number }}</div>
                                <div class="text-[10px] text-slate-400">{{ $tx->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap text-slate-700">
                                {{ $tx->cashier->name }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap text-slate-800 font-semibold">
                                {{ $tx->customer_name }}
                            </td>
                            <td class="py-3 px-4 text-xs">
                                <div class="max-w-xs space-y-0.5">
                                    @foreach($tx->items as $item)
                                        <div class="text-[11px] text-slate-700 truncate">
                                            <span class="font-mono font-bold">{{ $item->quantity }}x</span> {{ $item->product_name }}
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded uppercase font-bold text-[10px] 
                                    {{ $tx->payment_method === 'cash' ? 'bg-emerald-100 text-emerald-800' : 'bg-cyan-100 text-cyan-800' }}">
                                    {{ $tx->payment_method }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-slate-500 whitespace-nowrap">
                                Rp {{ number_format($tx->total_cost, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-navy-900 whitespace-nowrap">
                                Rp {{ number_format($tx->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-emerald-600 whitespace-nowrap">
                                Rp {{ number_format($tx->gross_profit, 0, ',', '.') }}
                                <span class="block text-[10px] text-slate-400 font-sans font-normal">({{ $tx->margin_percent }}%)</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <i data-lucide="calendar-x" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                Tidak ada transaksi pada periode yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50 no-print">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
