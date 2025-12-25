<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = [
        'batch_no',
        'item_id',
        'warehouse_id',
        'invoice_number',
        'invoice_date',
        'quantity_expected',
        'quantity_received',
        'meta',
        'status',
        'created_by',
    ];

    protected $casts = [
        'meta' => 'array',
        'invoice_date' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function attachments()
    {
        return $this->hasMany(PanelAttachment::class, 'batch_id');
    }

    public function panels()
    {
        return $this->hasMany(Panel::class, 'batch_id');
    }
}
