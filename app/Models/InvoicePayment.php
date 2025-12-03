<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    use HasFactory;

    protected $table = 'invoice_payments';

    protected $fillable = [
        'invoice_id','amount','method','reference','paid_at','received_by','meta'
    ];

    protected $casts = [
        'paid_at' => 'date',
        'meta' => 'array',
        'amount' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class,'invoice_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class,'received_by');
    }
}
