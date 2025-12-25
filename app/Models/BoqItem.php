<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoqItem extends Model
{
    protected $fillable = [
        'item', 
        'specification', 
        'unit', 
        'quantity', 
        'rate', 
        'amount',
        'boq_id'
    ];

    public function boq()
    {
        return $this->belongsTo(Boq::class);
    }
}
