<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'alternate_mobile',
        'address',
        'note',
        'ac_holder_name',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /*----------------------------------------
     | Relationships
     ----------------------------------------*/

    public function leads()
    {
        return $this->hasMany(Lead::class, 'customer_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'customer_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }

    public function activities()
    {
        return $this->hasMany(CustomerActivity::class, 'customer_id')->latest();
    }

    public function notes()
    {
        return $this->hasMany(CustomerNote::class, 'customer_id')->latest();
    }
}
