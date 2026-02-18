<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
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
        'recurring_type',
        'recurring_interval',
        'recurring_next_at',
        'recurring_end_at',
        'pdf_path',
        'created_by',
        'sent_by',
        'sent_at',
        'meta',
    ];

    protected $casts = [
        'meta'              => 'array',
        'is_recurring'      => 'boolean',
        'invoice_date'      => 'date',
        'due_date'          => 'date',
        'recurring_next_at' => 'date',
        'recurring_end_at'  => 'date',
    ];


    public const STATUS_LABELS = [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'paid' => 'Paid',
        'partial' => 'Partial',
        'overdue' => 'Overdue',
        'cancelled' => 'Cancelled',
    ];


    /* ================== RELATIONS ================== */

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    /* ================== BUSINESS LOGIC ================== */

    public function recalculateTotals(): void
    {
        $subTotal = $this->items()->sum('line_total');
        $taxTotal = $this->items()->sum('tax');

        $this->sub_total   = $subTotal;
        $this->tax_total   = $taxTotal;
        $this->total       = max(($subTotal + $taxTotal) - $this->discount, 0);
        $this->paid_amount = $this->payments()->sum('amount');
        $this->balance     = max($this->total - $this->paid_amount, 0);

        $this->status = $this->resolveStatus();

        $this->save();
    }

    protected function resolveStatus(): string
    {
        if ($this->paid_amount <= 0) {
            return $this->isOverdue() ? 'overdue' : $this->status;
        }

        if ($this->paid_amount >= $this->total) {
            return 'paid';
        }

        return 'partial';
    }

    protected function isOverdue(): bool
    {
        return $this->due_date && now()->gt($this->due_date) && $this->balance > 0;
    }
}
