<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $table = "quotations";

    protected $fillable = [
        'quote_request_id',
        'lead_id',
        'quotation_no',
        'pdf_path',
        'base_price',
        'discount',
        'final_price',
        'sent_at',
        'sent_by',
        'meta',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'final_price' => 'decimal:2',
        'meta' => 'array',
        'sent_at' => 'datetime',
    ];

    // /**
    //  * Quotation belongs to a Quote Request
    //  */
    // public function quoteRequest()
    // {
    //     return $this->belongsTo(QuoteRequest::class, 'quote_request_id');
    // }
    protected $with = ['sentBy'];
    /**
     * User who sent the quotation
     */
    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Mutator for final_price
     */
    public function setFinalPrice()
    {
        $this->final_price = ($this->base_price ?? 0) - ($this->discount ?? 0);
    }

    /**
     * Get readable final price
     */
    public function getFormattedFinalPriceAttribute()
    {
        return number_format($this->final_price, 2);
    }

    /**
     * Auto-generate quotation number when creating
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->quotation_no)) {
                $model->quotation_no = 'Q-' . strtoupper(uniqid());
            }
        });
    }

    public function quoteMaster()
    {
        return $this->belongsTo(QuoteMaster::class, 'quote_master_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'quotation_id');
    }

}
