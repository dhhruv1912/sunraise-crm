<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'model',
        'watt',
        'description',
        'image',
    ];

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function batches()
    {
        return $this->hasMany(Batch::class, 'item_id');
    }

    public function panels()
    {
        return $this->hasMany(Panel::class, 'item_id');
    }
}
