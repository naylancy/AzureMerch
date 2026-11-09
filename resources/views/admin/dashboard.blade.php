@extends('layouts.app')

@section('title', 'Dashboard Eksekutif')

@section('main-content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-navy-900 tracking-tight flex items-center gap-2">
                <i data-lucide="pie-chart" class="w-6 h-6 text-indigo-600"></i>
                Dashboard Eksekutif & Kinerja Penjualan
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Ringkasan analitik finansial, margin profitabilitas, dan popularitas merchandise K-Pop.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="{{ route('admin.reports') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-navy-900 hover:bg-navy-800 shadow-md transition-all">
                <i data-lucide="file-text" class="w-4 h-4 text-cyan-400"></i>
                Laporan Keuangan Rinci
            </a>
        </div>
    </div>

    <!-- Hero Financial KPIs Grid (Pastel Ocean Style) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Revenue This Month (Pastel Navy with Cyan Glow) -->
        <div class="bg-gradient-to-br from-navy-950 via-navy-900 to-navy-850 text-white p-5 rounded-3xl border border-cyan-400/30 shadow-xl relative overflow-hidden pastel-card-hover">
            <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-cyan-400/15 rounded-full blur-2xl pointer-events-none animate-glow-pulse"></div>
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-cyan-300">Pendapatan Bulan Ini</span>
                <div class="w-8 h-8 rounded-xl bg-white/10 text-cyan-300 flex items-center justify-center shadow-inner">
                    <i data-lucide="wallet" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl 2xl:text-3xl font-black font-mono tracking-tight text-white">
                Rp {{ number_format($totalRevenueMonth, 0, ',', '.') }}
            </div>
            <div class="flex items-center gap-2 mt-2 text-[11px] text-slate-300">
                <span class="text-teal-400 font-bold flex items-center gap-0.5">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                    {{ $txCountMonth }} Transaksi
                </span>
                <span>•</span>
                <span>Hari ini: Rp {{ number_format($todayRevenue, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Total Gross Profit (Pastel Teal / Mint) -->
        <div class="bg-gradient-to-br from-white via-white to-teal-50/80 p-5 rounded-3xl border border-teal-200/80 shadow-sm relative overflow-hidden pastel-card-hover">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-teal-700">Total Laba Kotor</span>
                <div class="w-8 h-8 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center">
                    <i data-lucide="banknote" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl 2xl:text-3xl font-black font-mono tracking-tight text-navy-900">
                Rp {{ number_format($totalGrossProfitMonth, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-500 mt-2">
                Hari ini: <strong class="text-teal-700 font-mono">Rp {{ number_format($todayProfit, 0, ',', '.') }}</strong>
            </div>
        </div>

        <!-- Profit Margin % (Pastel Indigo / Lavender) -->
        <div class="bg-gradient-to-br from-white via-white to-indigo-50/80 p-5 rounded-3xl border border-indigo-200/80 shadow-sm pastel-card-hover">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-700">Margin Keuntungan</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="percent" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl 2xl:text-3xl font-black font-mono tracking-tight text-indigo-900">
                {{ $profitMarginMonth }}%
            </div>
            <div class="text-[11px] text-slate-500 mt-2">
                Rasio laba kotor terhadap omset
            </div>
        </div>

        <!-- Average Ticket Value (Pastel Sky Breeze) -->
        <div class="bg-gradient-to-br from-white via-white to-sky-50/80 p-5 rounded-3xl border border-sky-200/80 shadow-sm pastel-card-hover">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-sky-700">Rata-rata Transaksi (AOV)</span>
                <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl 2xl:text-3xl font-black font-mono tracking-tight text-navy-900">
                Rp {{ number_format($avgTicketMonth, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-500 mt-2">
                Pengeluaran rata-rata per transaksi
            </div>
        </div>

    </div>

    <!-- Sales Trend Visual Chart & Category Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- 7-Day Sales Trend Bar Chart (2 Cols) -->
        <div class="lg:col-span-2 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div>
                    <h3 class="font-extrabold text-sm text-navy-900">Tren Penjualan 7 Hari Terakhir</h3>
                    <p class="text-[11px] text-slate-400">Perbandingan harian Pendapatan Kotor (Biru) vs Laba Kotor (Hijau)</p>
                </div>
                <div class="flex items-center gap-3 text-[10px] font-bold">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-cyan-500 inline-block"></span> Omset</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-emerald-500 inline-block"></span> Laba</span>
                </div>
            </div>

            <!-- CSS Bar Graph -->
            <div class="h-56 flex items-end justify-between gap-2 pt-6 pb-2 px-2">
                @php
                    $maxRev = max(1, $last7Days->max('revenue'));
                @endphp
                @foreach($last7Days as $day)
                    @php
                        $revHeight = round(($day['revenue'] / $maxRev) * 100);
                        $profHeight = round(($day['profit'] / $maxRev) * 100);
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end group">
                        
                        <!-- Tooltip on hover -->
                        <div class="hidden group-hover:block absolute -translate-y-24 bg-navy-950 text-white p-2 rounded-lg text-[10px] font-mono shadow-xl z-20 pointer-events-none whitespace-nowrap">
                            <div>{{ $day['date_label'] }}</div>
                            <div class="text-cyan-300">Omset: Rp {{ number_format($day['revenue'], 0, ',', '.') }}</div>
                            <div class="text-emerald-300">Laba: Rp {{ number_format($day['profit'], 0, ',', '.') }}</div>
                        </div>

                        <!-- Twin Bars -->
                        <div class="w-full flex items-end justify-center gap-1 h-44">
                            <!-- Revenue Bar -->
                            <div class="w-3 sm:w-5 bg-gradient-to-t from-blue-600 to-cyan-400 rounded-t-md transition-all duration-500 group-hover:opacity-90"
                                 style="height: {{ max(4, $revHeight) }}%;"></div>
                            <!-- Profit Bar -->
                            <div class="w-3 sm:w-5 bg-gradient-to-t from-emerald-600 to-teal-400 rounded-t-md transition-all duration-500 group-hover:opacity-90"
                                 style="height: {{ max(4, $profHeight) }}%;"></div>
                        </div>

                        <span class="text-[10px] font-semibold text-slate-500 truncate">{{ $day['date_label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Popular Categories Breakdown (1 Col) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="pb-3 border-b border-slate-100 mb-3">
                <h3 class="font-extrabold text-sm text-navy-900">Kategori Terlaris</h3>
                <p class="text-[11px] text-slate-400">Pangsa kontribusi penjualan per kategori merchandise</p>
            </div>

            <div class="space-y-3.5 flex-1 overflow-y-auto">
                @php
                    $totalCatSales = max(1, $categorySales->sum('total_sales'));
                @endphp
                @forelse($categorySales as $cat)
                    @php
                        $percentage = round(($cat->total_sales / $totalCatSales) * 100);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-bold text-slate-800">{{ $cat->category_name }}</span>
                            <span class="font-mono text-slate-500 font-semibold">Rp {{ number_format($cat->total_sales, 0, ',', '.') }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r from-cyan-500 to-blue-600 h-2 rounded-full transition-all duration-500" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-slate-400 mt-0.5">
                            <span>Terjual: {{ $cat->total_qty }} pcs</span>
                            <span class="text-emerald-600 font-semibold">Laba: Rp {{ number_format($cat->total_profit, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs">
                        Belum ada data penjualan kategori.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Leaderboards & Alerts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Top Selling Merchandise (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-sm text-navy-900 flex items-center gap-2">
                        <i data-lucide="award" class="w-4 h-4 text-amber-500"></i>
                        Top Merchandise Terlaris
                    </h3>
                    <p class="text-[11px] text-slate-400">Peringkat produk terlaris berdasarkan kuantitas unit terjual</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-semibold text-[10px]">
                            <th class="py-2.5 px-4">#</th>
                            <th class="py-2.5 px-4">Merchandise</th>
                            <th class="py-2.5 px-4">Grup / Artis</th>
                            <th class="py-2.5 px-4 text-center">Unit Terjual</th>
                            <th class="py-2.5 px-4 text-right">Omset</th>
                            <th class="py-2.5 px-4 text-right">Laba Kotor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($topProducts as $idx => $prod)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-2.5 px-4 font-bold text-slate-400 font-mono">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="py-2.5 px-4 font-bold text-slate-900">
                                    {{ $prod->name }}
                                    <span class="block text-[10px] text-slate-400 font-mono">{{ $prod->sku }}</span>
                                </td>
                                <td class="py-2.5 px-4 text-cyan-700 font-semibold">
                                    {{ $prod->group_artist ?: '-' }}
                                </td>
                                <td class="py-2.5 px-4 text-center font-mono font-bold text-navy-900">
                                    <span class="px-2 py-0.5 rounded-full bg-cyan-50 text-cyan-800 font-bold">
                                        {{ $prod->units_sold }} pcs
                                    </span>
                                </td>
                                <td class="py-2.5 px-4 text-right font-mono font-semibold text-slate-700">
                                    Rp {{ number_format($prod->revenue_generated, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-4 text-right font-mono font-bold text-emerald-600">
                                    Rp {{ number_format($prod->gross_profit, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Belum ada data penjualan tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Low Stock Alerts & Recent Feed (1 Col) -->
        <div class="space-y-6">
            
            <!-- Low Stock Watchlist (Read-Only) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <h3 class="font-extrabold text-sm text-navy-900 flex items-center gap-1.5">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-500"></i>
                        Peringatan Stok Menipis
                    </h3>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold font-mono">
                        {{ count($lowStockProducts) }} Item
                    </span>
                </div>

                <div class="space-y-2.5">
                    @forelse($lowStockProducts as $low)
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                            <div class="min-w-0 pr-2">
                                <div class="font-bold text-slate-900 truncate">{{ $low->name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $low->sku }} • Min: {{ $low->min_stock_alert }}</div>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg font-mono font-black text-xs whitespace-nowrap {{ $low->stock <= 0 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $low->stock }} pcs
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-slate-400 text-xs">
                            ✅ Semua stok berada dalam kondisi aman.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Transactions Feed -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <h3 class="font-extrabold text-sm text-navy-900 flex items-center gap-1.5">
                        <i data-lucide="clock" class="w-4 h-4 text-cyan-600"></i>
                        Aktivitas Penjualan Terakhir
                    </h3>
                </div>

                <div class="space-y-2.5">
                    @forelse($recentTransactions as $tx)
                        <div class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-50 text-xs border border-slate-100">
                            <div>
                                <div class="font-bold text-navy-900 font-mono text-[11px]">{{ $tx->invoice_number }}</div>
                                <div class="text-[10px] text-slate-400">{{ $tx->customer_name }} • {{ $tx->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="text-right font-mono">
                                <div class="font-extrabold text-navy-900 text-xs">Rp {{ number_format($tx->grand_total, 0, ',', '.') }}</div>
                                <span class="text-[9px] uppercase px-1.5 py-0.2 rounded font-bold bg-slate-100 text-slate-600">{{ $tx->payment_method }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-slate-400 text-xs">
                            Belum ada transaksi terbaru.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
