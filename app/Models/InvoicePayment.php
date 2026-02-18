<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePayment extends Model
{
    protected $fillable = [
        'invoice_id',
        'amount',
        'method',
        'reference',
        'paid_at',
        'received_by',
        'meta',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',  
        'paid_at' => 'date',
        'meta'    => 'array'
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
    
    public function receiver()
    {
        return $this->belongsTo(User::class,'received_by');
    }

    protected static function booted()
    {
        static::saved(fn ($payment) => $payment->invoice?->recalculateTotals());
        static::deleted(fn ($payment) => $payment->invoice?->recalculateTotals());
    }
}
