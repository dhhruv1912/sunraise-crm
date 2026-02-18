<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boq extends Model
{
    protected $fillable = [
        'project_id', 
        'boq_no', 
        'boq_date', 
        'total_amount', 
        'pdf_path'
    ];

    public function items()
    {
        return $this->hasMany(BoqItem::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
