<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PanelMovement extends Model
{
    protected $fillable = [
        'panel_id',
        'action',
        'from_warehouse_id',
        'to_warehouse_id',
        'customer_id',
        'performed_by',
        'note',
        'happened_at',
    ];

    protected $casts = [
        'happened_at' => 'datetime',
    ];

    public function panel()
    {
        return $this->belongsTo(Panel::class, 'panel_id');
    }

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
