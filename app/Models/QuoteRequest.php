<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    use HasFactory;

    protected $table = 'quote_requests';

    protected $fillable = [
        'type',
        'customer_id',
        'module',
        'kw',
        'mc',
        'budget',
        'status',
        'assigned_to',
        'quote_master_id',
        'created_by',
        'notes',
        'source',
        'ip',
        'location',
    ];

    protected $casts = [
        'kw' => 'decimal:2',
        'mc' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    // Status label mapping (ENUM → human)
    public const STATUS_LABELS = [
        'new_request'                => 'New Request',
        'viewed'                     => 'Viewed',
        'pending'                    => 'Pending',
        'responded'                  => 'Responded',
        'called'                     => 'Called',
        'called_converted_to_lead'   => 'Called & Converted to Lead',
        'called_closed'              => 'Called & Closed',
    ];

    /**
     * Human readable status name
     */
    public function getStatusLabelAttribute()
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }


    /*----------------------------------------
     | Relationships
     ----------------------------------------*/

    public function lead()
    {
        return $this->hasOne(Lead::class, 'quote_request_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'quote_request_id');
    }

    public function quote()
    {
        return $this->belongsTo(QuoteMaster::class, 'quote_master_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function projects()
    {
        return $this->hasMany(\App\Models\Project::class);
    }

    public function history()
    {
        return $this->hasMany(QuoteRequestHistory::class, 'quote_request_id')
                    ->latest();
    }
}
