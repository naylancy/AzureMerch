<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\StockLog;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Executive Overview Dashboard
     */
    public function dashboard(Request $request)
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // 1. High Level Financial KPIs (Lifetime & This Month)
        $monthQuery = Transaction::where('status', 'completed')->where('created_at', '>=', $thisMonth);
        $totalRevenueMonth = (float) $monthQuery->sum('grand_total');
        $totalCostMonth = (float) $monthQuery->sum('total_cost');
        $totalGrossProfitMonth = $totalRevenueMonth - $totalCostMonth;
        $profitMarginMonth = $totalRevenueMonth > 0 ? round(($totalGrossProfitMonth / $totalRevenueMonth) * 100, 1) : 0;
        $txCountMonth = $monthQuery->count();
        $avgTicketMonth = $txCountMonth > 0 ? round($totalRevenueMonth / $txCountMonth) : 0;

        // Today KPIs
        $todayQuery = Transaction::where('status', 'completed')->whereDate('created_at', $today);
        $todayRevenue = (float) $todayQuery->sum('grand_total');
        $todayCost = (float) $todayQuery->sum('total_cost');
        $todayProfit = $todayRevenue - $todayCost;
        $todayCount = $todayQuery->count();

        // 2. Popular Categories Breakdown
        $categorySales = DB::table('transaction_items')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.name as category_name',
                'categories.code as category_code',
                DB::raw('SUM(transaction_items.quantity) as total_qty'),
                DB::raw('SUM(transaction_items.subtotal) as total_sales'),
                DB::raw('SUM(transaction_items.subtotal - (transaction_items.cost_price * transaction_items.quantity)) as total_profit')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.code')
            ->orderByDesc('total_sales')
            ->get();

        // 3. Top Selling K-Pop Merchandise
        $topProducts = DB::table('transaction_items')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->select(
                'products.name',
                'products.sku',
                'products.group_artist',
                'products.stock',
                DB::raw('SUM(transaction_items.quantity) as units_sold'),
                DB::raw('SUM(transaction_items.subtotal) as revenue_generated'),
                DB::raw('SUM(transaction_items.subtotal - (transaction_items.cost_price * transaction_items.quantity)) as gross_profit')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.group_artist', 'products.stock')
            ->orderByDesc('units_sold')
            ->take(6)
            ->get();

        // 4. Low Stock Alerts
        $lowStockProducts = Product::with('category')->lowStock()->orderBy('stock', 'asc')->take(5)->get();

        // 5. Recent POS Transactions Feed
        $recentTransactions = Transaction::with(['cashier', 'items'])->latest()->take(6)->get();

        // 6. Last 7 Days Sales Trend Data for Chart
        $last7Days = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            $dayTx = Transaction::where('status', 'completed')->whereDate('created_at', $date);

            $revenue = (float) $dayTx->sum('grand_total');
            $cost = (float) $dayTx->sum('total_cost');
            $profit = $revenue - $cost;

            return [
                'date_label' => $date->format('d M'),
                'revenue' => $revenue,
                'profit' => $profit,
            ];
        });

        return view('admin.dashboard', compact(
            'totalRevenueMonth',
            'totalGrossProfitMonth',
            'profitMarginMonth',
            'txCountMonth',
            'avgTicketMonth',
            'todayRevenue',
            'todayProfit',
            'todayCount',
            'categorySales',
            'topProducts',
            'lowStockProducts',
            'recentTransactions',
            'last7Days'
        ));
    }

    /**
     * Financial & Sales Detailed Report with Date Range Filtering
     */
    public function reports(Request $request)
    {
        $range = $request->get('range', 'this_month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $now = Carbon::now();

        if ($range === 'today') {
            $from = $now->copy()->startOfDay();
            $to = $now->copy()->endOfDay();
        } elseif ($range === 'yesterday') {
            $from = $now->copy()->subDay()->startOfDay();
            $to = $now->copy()->subDay()->endOfDay();
        } elseif ($range === 'this_week') {
            $from = $now->copy()->startOfWeek();
            $to = $now->copy()->endOfWeek();
        } elseif ($range === 'custom' && $startDate && $endDate) {
            $from = Carbon::parse($startDate)->startOfDay();
            $to = Carbon::parse($endDate)->endOfDay();
        } else { // default 'this_month'
            $from = $now->copy()->startOfMonth();
            $to = $now->copy()->endOfMonth();
        }

        $query = Transaction::where('status', 'completed')
            ->whereBetween('created_at', [$from, $to])
            ->with(['cashier', 'items.product']);

        $transactions = (clone $query)->latest()->paginate(15)->withQueryString();

        // Summary Aggregates
        $summary = [
            'total_sales' => (float) (clone $query)->sum('grand_total'),
            'total_cost' => (float) (clone $query)->sum('total_cost'),
            'total_transactions' => (clone $query)->count(),
            'total_discount' => (float) (clone $query)->sum('discount'),
        ];

        $summary['gross_profit'] = $summary['total_sales'] - $summary['total_cost'];
        $summary['profit_margin'] = $summary['total_sales'] > 0
            ? round(($summary['gross_profit'] / $summary['total_sales']) * 100, 1)
            : 0;

        return view('admin.reports', compact('transactions', 'summary', 'range', 'from', 'to'));
    }

    /**
     * Read-only Audit Logs (System & Stock Movements)
     */
    public function auditLogs(Request $request)
    {
        $logs = StockLog::with(['product.category', 'user'])
            ->latest()
            ->paginate(20);

        return view('admin.audit-logs', compact('logs'));
    }
}
