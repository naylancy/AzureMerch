<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $transaction->invoice_number }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Courier New', Courier, monospace;
        }

        body {
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            padding: 15px;
        }

        .receipt-card {
            width: 80mm;
            max-width: 100%;
            background: #fff;
            padding: 12px 10px;
            font-size: 11px;
            color: #111;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        .store-title {
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 0.5px;
        }

        .store-subtitle {
            font-size: 9px;
            color: #333;
            margin-top: 2px;
        }

        .divider {
            border-top: 1px dashed #444;
            margin: 6px 0;
        }

        .divider-double {
            border-top: 2px solid #222;
            margin: 6px 0;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .item-name {
            font-weight: bold;
            font-size: 10.5px;
            word-break: break-word;
        }

        .item-calc {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #333;
            padding-left: 6px;
        }

        .calc-table {
            width: 100%;
            margin-top: 4px;
        }

        .calc-table td {
            padding: 1.5px 0;
            font-size: 10.5px;
        }

        .total-row {
            font-size: 13px;
            font-weight: bold;
        }

        .footer-note {
            font-size: 9px;
            line-height: 1.3;
            margin-top: 8px;
        }

        .barcode-box {
            margin: 8px auto 4px auto;
            text-align: center;
            font-family: monospace;
            font-size: 12px;
            letter-spacing: 3px;
            border: 1px solid #ddd;
            padding: 3px;
            width: 85%;
        }

        .print-btn-container {
            margin-top: 12px;
            text-align: center;
        }

        .print-btn {
            background-color: #0284c7;
            color: #fff;
            border: none;
            padding: 6px 14px;
            font-size: 12px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .receipt-card {
                width: 100%;
                box-shadow: none;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="receipt-card">
        
        <!-- Header -->
        <div class="text-center">
            <div class="store-title">AZUREMERCH</div>
            <div class="store-subtitle">K-POP OFFICIAL MERCHANDISE & ALBUMS</div>
            <div class="store-subtitle">Jl. Samudra Biru No. 88, Jakarta Selatan</div>
            <div class="store-subtitle">Telp/WA: 0812-3456-7890 • IG: @azuremerch.id</div>
        </div>

        <div class="divider"></div>

        <!-- Meta Information -->
        <div style="font-size: 9.5px; line-height: 1.4;">
            <div class="item-row">
                <span>No. Faktur</span>
                <span class="font-bold">{{ $transaction->invoice_number }}</span>
            </div>
            <div class="item-row">
                <span>Tanggal</span>
                <span>{{ $transaction->created_at->format('d/m/Y H:i:s') }}</span>
            </div>
            <div class="item-row">
                <span>Kasir</span>
                <span>{{ $transaction->cashier->name }}</span>
            </div>
            <div class="item-row">
                <span>Pelanggan</span>
                <span>{{ $transaction->customer_name }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Items List -->
        <div>
            @foreach($transaction->items as $item)
                <div style="margin-bottom: 4px;">
                    <div class="item-name">{{ $item->product_name }}</div>
                    <div class="item-calc">
                        <span>{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                        <span class="font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="divider"></div>

        <!-- Calculations -->
        <table class="calc-table">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($transaction->discount > 0)
                <tr>
                    <td>Diskon Fandom</td>
                    <td class="text-right">- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td>
                </tr>
            @endif
            @if($transaction->tax > 0)
                <tr>
                    <td>Pajak (PPN)</td>
                    <td class="text-right">Rp {{ number_format($transaction->tax, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td style="padding-top: 4px;">TOTAL</td>
                <td class="text-right" style="padding-top: 4px;">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2"><div class="divider"></div></td>
            </tr>
            <tr>
                <td>Metode Bayar</td>
                <td class="text-right uppercase font-bold">{{ $transaction->payment_method }}</td>
            </tr>
            <tr>
                <td>Jumlah Bayar</td>
                <td class="text-right">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembalian</td>
                <td class="text-right font-bold">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- Barcode Stamp -->
        <div class="barcode-box">
            *{{ $transaction->invoice_number }}*
        </div>

        <!-- Footer -->
        <div class="text-center footer-note">
            <p class="font-bold">KAMSAHAMNIDA! (감사합니다)</p>
            <p>Terima kasih telah berbelanja di AzureMerch!</p>
            <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan tanpa unboxing video.</p>
        </div>

        <!-- Print Button for Screen View -->
        <div class="print-btn-container no-print">
            <button onclick="window.print()" class="print-btn">
                🖨️ Cetak Struk Thermal
            </button>
        </div>

    </div>

</body>
</html>
