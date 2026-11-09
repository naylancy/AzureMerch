<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockLog;
use Carbon\Carbon;

class PosController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)->withCount('products')->get();
        $products = Product::where('is_active', true)->with('category')->orderBy('name')->get();

        // Daily shift stats for current cashier
        $today = Carbon::today();
        $shiftStats = [
            'today_sales' => Transaction::where('cashier_id', auth()->id())
                ->whereDate('created_at', $today)
                ->where('status', 'completed')
                ->sum('grand_total'),
            'today_count' => Transaction::where('cashier_id', auth()->id())
                ->whereDate('created_at', $today)
                ->where('status', 'completed')
                ->count(),
            'recent_invoices' => Transaction::where('cashier_id', auth()->id())
                ->whereDate('created_at', $today)
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('pos.index', compact('categories', 'products', 'shiftStats'));
    }

    /**
     * Fast search / barcode scanner endpoint (JSON)
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $categoryId = $request->get('category_id');

        $products = Product::query()
            ->where('is_active', true)
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('barcode', $query)
                        ->orWhere('sku', 'like', "%{$query}%")
                        ->orWhere('group_artist', 'like', "%{$query}%");
                });
            })
            ->with('category')
            ->orderBy('name')
            ->get();

        return response()->json($products);
    }

    /**
     * Process checkout with DB transaction and row-level stock locking
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'in:cash,qris,transfer,debit'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'customer_name' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $transaction = DB::transaction(function () use ($validated) {
                $itemInputs = collect($validated['items']);
                $productIds = $itemInputs->pluck('product_id')->all();

                // Lock the rows to prevent race condition concurrency issues
                $products = Product::whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $subtotal = 0;
                $totalCost = 0;
                $lineItemsData = [];

                foreach ($itemInputs as $item) {
                    $product = $products->get($item['product_id']);

                    if (!$product || !$product->is_active) {
                        throw new \Exception("Produk '{$item['product_id']}' tidak tersedia atau tidak aktif.");
                    }

                    $qty = (int) $item['quantity'];

                    // Strict stock validation
                    if ($product->stock < $qty) {
                        throw new \Exception("Stok tidak mencukupi untuk item: {$product->name}. Sisa stok: {$product->stock}, permintaan: {$qty}.");
                    }

                    $itemSubtotal = $product->selling_price * $qty;
                    $itemCostTotal = $product->cost_price * $qty;

                    $subtotal += $itemSubtotal;
                    $totalCost += $itemCostTotal;

                    $lineItemsData[] = [
                        'product' => $product,
                        'product_id' => $product->id,
                        'sku' => $product->sku,
                        'product_name' => $product->name,
                        'cost_price' => $product->cost_price,
                        'unit_price' => $product->selling_price,
                        'quantity' => $qty,
                        'subtotal' => $itemSubtotal,
                    ];
                }

                $discount = (float) ($validated['discount'] ?? 0);
                $tax = (float) ($validated['tax'] ?? 0);
                $grandTotal = max(0, ($subtotal - $discount) + $tax);
                $paidAmount = (float) $validated['paid_amount'];

                if ($paidAmount < $grandTotal) {
                    throw new \Exception("Nominal pembayaran (Rp " . number_format($paidAmount, 0, ',', '.') . ") kurang dari total belanja (Rp " . number_format($grandTotal, 0, ',', '.') . ").");
                }

                $changeAmount = $paidAmount - $grandTotal;

                // 1. Create Transaction
                $tx = Transaction::create([
                    'invoice_number' => Transaction::generateInvoiceNumber(),
                    'cashier_id' => auth()->id(),
                    'customer_name' => $validated['customer_name'] ?: 'Pelanggan Walk-in',
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'tax' => $tax,
                    'grand_total' => $grandTotal,
                    'total_cost' => $totalCost,
                    'paid_amount' => $paidAmount,
                    'change_amount' => $changeAmount,
                    'payment_method' => $validated['payment_method'],
                    'payment_reference' => $validated['payment_reference'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'completed',
                ]);

                // 2. Insert items, deduct stock, and generate stock audit logs
                foreach ($lineItemsData as $line) {
                    TransactionItem::create([
                        'transaction_id' => $tx->id,
                        'product_id' => $line['product_id'],
                        'sku' => $line['sku'],
                        'product_name' => $line['product_name'],
                        'cost_price' => $line['cost_price'],
                        'unit_price' => $line['unit_price'],
                        'quantity' => $line['quantity'],
                        'subtotal' => $line['subtotal'],
                    ]);

                    $product = $line['product'];
                    $stockBefore = $product->stock;
                    $stockAfter = $stockBefore - $line['quantity'];

                    // Deduct stock
                    $product->update(['stock' => $stockAfter]);

                    // Audit Log
                    StockLog::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'type' => 'sale',
                        'reference_number' => $tx->invoice_number,
                        'quantity_change' => -$line['quantity'],
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'notes' => "Penjualan POS Kasir Faktur: {$tx->invoice_number}",
                    ]);
                }

                return $tx->load(['items', 'cashier']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses!',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'invoice_number' => $transaction->invoice_number,
                    'grand_total' => $transaction->grand_total,
                    'paid_amount' => $transaction->paid_amount,
                    'change_amount' => $transaction->change_amount,
                    'receipt_url' => route('pos.receipt', $transaction->id),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Thermal Receipt Printable View (58mm / 80mm ESC/POS layout)
     */
    public function receipt($id)
    {
        $transaction = Transaction::with(['items.product', 'cashier'])->findOrFail($id);
        return view('pos.receipt', compact('transaction'));
    }

    /**
     * Cashier Recent Shift History
     */
    public function history()
    {
        $transactions = Transaction::where('cashier_id', auth()->id())
            ->with(['items'])
            ->latest()
            ->paginate(15);

        return view('pos.history', compact('transactions'));
    }
}
