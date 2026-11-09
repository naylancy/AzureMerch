<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SampleTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $cashier = User::where('role', 'kasir')->first();
        if (!$cashier) {
            return;
        }

        $products = Product::all();
        if ($products->isEmpty()) {
            return;
        }

        $customerNames = [
            'Aulia Rahma (ARMY)',
            'Blink Indonesia Official',
            'Stay Jakarta Club',
            'Bunnies Community',
            'Carat Surabaya',
            'Farras Althaf',
            'Nadine Clarissa',
            'Revan K-Pop Store Reseller',
            'Siti Nurhaliza',
            'Walk-in Customer',
        ];

        $paymentMethods = ['cash', 'qris', 'transfer', 'debit'];

        // Generate sales across past 6 days up to today
        for ($i = 0; $i < 18; $i++) {
            $daysAgo = rand(0, 6);
            $createdAt = Carbon::now()->subDays($daysAgo)->subHours(rand(1, 10))->subMinutes(rand(5, 50));

            $numItems = rand(1, 3);
            $pickedProducts = $products->random($numItems);

            $subtotal = 0;
            $totalCost = 0;
            $lineItems = [];

            foreach ($pickedProducts as $prod) {
                $qty = rand(1, 2);
                $itemSubtotal = $prod->selling_price * $qty;
                $itemCost = $prod->cost_price * $qty;

                $subtotal += $itemSubtotal;
                $totalCost += $itemCost;

                $lineItems[] = [
                    'product' => $prod,
                    'sku' => $prod->sku,
                    'product_name' => $prod->name,
                    'cost_price' => $prod->cost_price,
                    'unit_price' => $prod->selling_price,
                    'quantity' => $qty,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $discount = (rand(0, 5) === 0) ? 20000 : 0;
            $grandTotal = max(0, $subtotal - $discount);
            $payMethod = $paymentMethods[array_rand($paymentMethods)];
            $paidAmount = $payMethod === 'cash' ? ceil($grandTotal / 50000) * 50000 : $grandTotal;
            $changeAmount = $paidAmount - $grandTotal;

            $invoice = 'INV-' . $createdAt->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);

            $tx = Transaction::create([
                'invoice_number' => $invoice,
                'cashier_id' => $cashier->id,
                'customer_name' => $customerNames[array_rand($customerNames)],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => 0,
                'grand_total' => $grandTotal,
                'total_cost' => $totalCost,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'payment_method' => $payMethod,
                'payment_reference' => $payMethod !== 'cash' ? 'REF-KPOP-' . rand(100000, 999999) : null,
                'notes' => 'Official K-pop retail store purchase',
                'status' => 'completed',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            foreach ($lineItems as $line) {
                TransactionItem::create([
                    'transaction_id' => $tx->id,
                    'product_id' => $line['product']->id,
                    'sku' => $line['sku'],
                    'product_name' => $line['product_name'],
                    'cost_price' => $line['cost_price'],
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'subtotal' => $line['subtotal'],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // Also record sample sale stock log
                $before = $line['product']->stock;
                $after = max(0, $before - $line['quantity']);
                $line['product']->update(['stock' => $after]);

                StockLog::create([
                    'product_id' => $line['product']->id,
                    'user_id' => $cashier->id,
                    'type' => 'sale',
                    'reference_number' => $tx->invoice_number,
                    'quantity_change' => -$line['quantity'],
                    'stock_before' => $before,
                    'stock_after' => $after,
                    'notes' => "Penjualan POS Kasir Faktur: {$tx->invoice_number}",
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }
    }
}
