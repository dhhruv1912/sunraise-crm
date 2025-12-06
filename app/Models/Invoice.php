<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'customer_id',
        'invoice_no',
        'status',
        'sub_total',
        'tax_total',
        'discount',
        'total',
        'paid_amount',
        'balance',
        'currency',
        'invoice_date',
        'due_date',
        'notes',
        'is_recurring',
        'meta',
        'pdf_path',
        'created_by',
        'sent_by',
        'sent_at',

        // recurring
        'recurring_type',
        'recurring_interval',
        'recurring_next_at',
        'recurring_end_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'sent_at' => 'datetime',
        'recurring_next_at' => 'date',
        'recurring_end_at' => 'date',
    ];

    public const STATUS_LABELS = [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'paid' => 'Paid',
        'partial' => 'Partial',
        'overdue' => 'Overdue',
        'cancelled' => 'Cancelled',
    ];

    /*----------------------------------------
     | Relationships
     ----------------------------------------*/

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class, 'invoice_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function quoteRequest()
    {
        return $this->hasOneThrough(
            QuoteRequest::class,
            Project::class,
            'id',                // Project.id
            'id',                // QuoteRequest.id
            'project_id',        // Invoice.project_id
            'lead_id'            // Project.lead_id → Lead → qr
        );
    }

}
