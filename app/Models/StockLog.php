<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'reference_number',
        'quantity_change',
        'stock_before',
        'stock_after',
        'notes',
    ];

    protected $casts = [
        'quantity_change' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'inbound' => 'Inbound (Penerimaan)',
            'sale' => 'Penjualan (Kasir)',
            'adjustment_plus' => 'Penyesuaian (+)',
            'adjustment_minus' => 'Penyesuaian (-)',
            'return' => 'Retur Barang',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}
