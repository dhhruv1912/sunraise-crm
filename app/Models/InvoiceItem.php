<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'quote_master_id',
        'description',
        'unit_price',
        'quantity',
        'tax',
        'line_total',
        'meta',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'tax' => 'decimal:2',
        'line_total' => 'decimal:2',
        'meta' => 'array',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->line_total = ($item->unit_price * $item->quantity) + $item->tax;
        });

        static::saved(fn ($item) => $item->invoice?->recalculateTotals());
        static::deleted(fn ($item) => $item->invoice?->recalculateTotals());
    }
}
