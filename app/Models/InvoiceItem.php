<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'invoice_items';

    protected $fillable = [
        'invoice_id','quote_master_id','description','unit_price','quantity','tax','line_total','meta'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'tax' => 'decimal:2',
        'line_total' => 'decimal:2',
        'meta' => 'array',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class,'invoice_id');
    }

    public function quoteMaster()
    {
        return $this->belongsTo(QuoteMaster::class,'quote_master_id');
    }
}
