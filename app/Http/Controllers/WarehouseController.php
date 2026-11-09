<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\StockLog;

class WarehouseController extends Controller
{
    /**
     * Warehouse Inventory Dashboard & Product List
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');
        $stockFilter = $request->get('stock_filter');

        $query = Product::with('category')->latest();

        if ($search) {
            $query->search($search);
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($stockFilter === 'low') {
            $query->lowStock();
        } elseif ($stockFilter === 'out') {
            $query->where('stock', '<=', 0);
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::withCount('products')->get();

        // Warehouse Inventory KPIs
        $kpis = [
            'total_sku' => Product::count(),
            'total_units' => Product::sum('stock'),
            'low_stock_count' => Product::lowStock()->count(),
            'out_of_stock_count' => Product::where('stock', '<=', 0)->count(),
            'total_valuation' => Product::sum(DB::raw('stock * cost_price')),
        ];

        return view('warehouse.index', compact('products', 'categories', 'kpis'));
    }

    /**
     * Show Product Create Form
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('warehouse.create', compact('categories'));
    }

    /**
     * Store New Merchandise Item
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:255'],
            'barcode' => ['required', 'string', 'max:64', 'unique:products,barcode'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock_alert' => ['required', 'integer', 'min:0'],
            'group_artist' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'supplier_reference' => ['nullable', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $product = Product::create([
                'category_id' => $validated['category_id'],
                'sku' => strtoupper($validated['sku']),
                'name' => $validated['name'],
                'barcode' => $validated['barcode'],
                'cost_price' => $validated['cost_price'],
                'selling_price' => $validated['selling_price'],
                'stock' => $validated['stock'],
                'min_stock_alert' => $validated['min_stock_alert'],
                'group_artist' => $validated['group_artist'] ?? null,
                'description' => $validated['description'] ?? null,
                'image_url' => $validated['image_url'] ?? null,
                'is_active' => true,
            ]);

            // If initial stock > 0, log inbound
            if ($product->stock > 0) {
                StockLog::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'inbound',
                    'reference_number' => $request->get('supplier_reference', 'INITIAL-STOCK'),
                    'quantity_change' => $product->stock,
                    'stock_before' => 0,
                    'stock_after' => $product->stock,
                    'notes' => 'Penerimaan batch awal produk baru ke gudang.',
                ]);
            }
        });

        return redirect()->route('warehouse.index')->with('success', 'Produk merchandise baru berhasil ditambahkan!');
    }

    /**
     * Show Product Edit Form
     */
    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        return view('warehouse.edit', compact('product', 'categories'));
    }

    /**
     * Update Product Details
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku,' . $product->id],
            'name' => ['required', 'string', 'max:255'],
            'barcode' => ['required', 'string', 'max:64', 'unique:products,barcode,' . $product->id],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'min_stock_alert' => ['required', 'integer', 'min:0'],
            'group_artist' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $product->update([
            'category_id' => $validated['category_id'],
            'sku' => strtoupper($validated['sku']),
            'name' => $validated['name'],
            'barcode' => $validated['barcode'],
            'cost_price' => $validated['cost_price'],
            'selling_price' => $validated['selling_price'],
            'min_stock_alert' => $validated['min_stock_alert'],
            'group_artist' => $validated['group_artist'] ?? null,
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('warehouse.index')->with('success', "Data produk {$product->name} berhasil diperbarui.");
    }

    /**
     * Delete / Archive Product
     */
    public function destroy(Product $product)
    {
        // If product already has transactions, soft-archive by setting is_active = false
        if ($product->transactionItems()->count() > 0) {
            $product->update(['is_active' => false]);
            return redirect()->route('warehouse.index')->with('info', "Produk memiliki riwayat transaksi, status diubah menjadi 'Non-aktif'.");
        }

        $product->delete();
        return redirect()->route('warehouse.index')->with('success', 'Produk berhasil dihapus dari sistem gudang.');
    }

    /**
     * Inbound Shipment Intake (Receive Supplier Delivery)
     */
    public function inbound()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('warehouse.inbound', compact('products'));
    }

    public function processInbound(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'supplier_name' => ['required', 'string', 'max:100'],
            'reference_number' => ['required', 'string', 'max:64'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::lockForUpdate()->findOrFail($validated['product_id']);
            $stockBefore = $product->stock;
            $qty = (int) $validated['quantity'];
            $stockAfter = $stockBefore + $qty;

            // Optionally update cost price if new cost provided
            if (!empty($validated['unit_cost']) && $validated['unit_cost'] > 0) {
                $product->cost_price = $validated['unit_cost'];
            }

            $product->stock = $stockAfter;
            $product->save();

            StockLog::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => 'inbound',
                'reference_number' => $validated['reference_number'],
                'quantity_change' => $qty,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'notes' => "Supplier: {$validated['supplier_name']} - " . ($validated['notes'] ?? 'Penerimaan stok dari supplier.'),
            ]);
        });

        return redirect()->route('warehouse.index')->with('success', 'Penerimaan stok supplier berhasil dicatat dan stok telah diperbarui!');
    }

    /**
     * Stock Opname / Manual Adjustment (Correct discrepancies)
     */
    public function adjustment(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'actual_stock' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:64'],
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::lockForUpdate()->findOrFail($validated['product_id']);
            $stockBefore = $product->stock;
            $actualStock = (int) $validated['actual_stock'];
            $delta = $actualStock - $stockBefore;

            if ($delta === 0) {
                return;
            }

            $type = $delta > 0 ? 'adjustment_plus' : 'adjustment_minus';

            $product->stock = $actualStock;
            $product->save();

            StockLog::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => $type,
                'reference_number' => $validated['reference_number'] ?: 'OPNAME-' . date('YmdHi'),
                'quantity_change' => $delta,
                'stock_before' => $stockBefore,
                'stock_after' => $actualStock,
                'notes' => "Stock Opname: {$validated['reason']}",
            ]);
        });

        return redirect()->back()->with('success', 'Penyesuaian stok opname berhasil direkam ke log audit!');
    }

    /**
     * Complete Stock Movement Audit Logs
     */
    public function logs(Request $request)
    {
        $type = $request->get('type');
        $productId = $request->get('product_id');

        $query = StockLog::with(['product.category', 'user'])->latest();

        if ($type) {
            $query->where('type', $type);
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $logs = $query->paginate(20)->withQueryString();
        $products = Product::orderBy('name')->get();

        return view('warehouse.logs', compact('logs', 'products'));
    }
}
