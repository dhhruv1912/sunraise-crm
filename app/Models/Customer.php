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
        'aadhar_card_number',
        'aadhar_card',
        'pan_card_number',
        'pan_card',
        'lightbill_number',
        'light_bill',
        'sanction_load',
        'service_number',
        'bank_account_number',
        'micr_code',
        'ifsc_code',
        'bank_name',
        'branch_name',
        'cancel_cheque',
        'passport_size_photo',
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
