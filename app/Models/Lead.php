<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_request_id',
        'lead_code',
        'assigned_to',
        'status',
        'next_followup_at',
        'remarks',
        'meta',
        'created_by',
        'customer_id',
    ];

    protected $casts = [
        'meta' => 'array',
        'next_followup_at' => 'datetime',
    ];

    public static $STATUS = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'site_visit_planned' => 'Site Visit Planned',
        'site_visited' => 'Site Visited',
        'follow_up' => 'Follow Up',
        'negotiation' => 'Negotiation',
        'converted' => 'Converted',
        'dropped' => 'Dropped'
    ];

    /*----------------------------------------
     | Relationships
     ----------------------------------------*/

    public function quoteRequest()
    {
        return $this->belongsTo(QuoteRequest::class, 'quote_request_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'lead_id');
    }

    public function history()
    {
        return $this->hasMany(LeadHistory::class, 'lead_id')->latest();
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    // helper
    public function addHistory($event, $message = null, $performedBy = null, $meta = null)
    {
        return LeadHistory::create([
            'lead_id' => $this->id,
            'action' => $event,
            'message' => $message,
            'changed_by' => $performedBy ?? auth()->id(),
            'meta' => $meta ? json_encode($meta) : null,
        ]);
    }
}
