<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Panel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'batch_id',
        'item_id',
        'serial_number',
        'model',
        'batch_no_copy',
        'status',
        'warehouse_id',
        'customer_id',
        'sold_at',
        'installed_at',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'sold_at' => 'datetime',
        'installed_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function movements()
    {
        return $this->hasMany(PanelMovement::class, 'panel_id');
    }

    public function attachments()
    {
        return $this->hasMany(PanelAttachment::class, 'panel_id');
    }
}
