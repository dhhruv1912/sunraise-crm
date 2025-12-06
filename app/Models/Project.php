<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'customer_id',
        'project_code',

        // location
        'address',
        'latitude',
        'longitude',

        // technical
        'kw',
        'module_brand',
        'inverter_brand',
        'module_count',

        // workflow
        'status',
        'survey_date',
        'installation_start_date',
        'installation_end_date',
        'inspection_date',
        'handover_date',
        'estimated_complete_date',
        'actual_complete_date',

        // files
        'design_file',
        'boq_file',

        // users
        'assignee',
        'reporter',

        // finance
        'project_value',
        'finalize_price',
        'emi',

        // billing
        'billing_status',
        'invoice_number',
        'invoice_date',
        'payment_received',

        // subsidy
        'subsidy_status',
        'subsidy_amount',
        'subsidy_file',

        'priority',
        'current_step',
        'next_step',
        'is_on_hold',
        'hold_reason',

        'project_note',
    ];

    protected $casts = [
        'site_photos' => 'array',
        'documents' => 'array',
        'survey_date' => 'date',
        'installation_start_date' => 'date',
        'installation_end_date' => 'date',
        'inspection_date' => 'date',
        'handover_date' => 'date',
        'estimated_complete_date' => 'date',
        'actual_complete_date' => 'date',
        'invoice_date' => 'date',
    ];

    public const BADGES = [];
    /**
     * Status Label Mapping
     */
    public const STATUS_LABELS = [
        'new'                       => 'New',
        'document_collection'       => 'Document Collection',
        'document_registration'     => 'Document Registration',
        'document_verification'     => 'Document Verification',
        'fisibility_approval'       => 'Feasibility Approval',
        'site_visit'                => 'Site Visit',

        'make_boq'                  => 'Make BOQ',
        'boq_approved'              => 'BOQ Approved',

        'site_dispatch'             => 'Site Dispatch',
        'installation_started'      => 'Installation Started',
        'project_execution'         => 'Project Execution',

        'project_completion'        => 'Project Completion',
        'quality_check'             => 'Quality Check',
        'inspection'                => 'Inspection',
        'lisoning_after_qc'         => 'Lisoning After QC',
        'self_certification'        => 'Self Certification',

        'meter_file'                => 'Meter File',
        'meter_file_submission'     => 'Meter File Submission',
        'subsidy_claim'             => 'Subsidy Claim',

        'project_invoice'           => 'Project Invoice',
        'project_file'              => 'Project File',

        'handover'                  => 'Handover',
        'complete'                  => 'Complete',
    ];

    /**
     * Accessors
     */
    public function getStatusLabelAttribute()
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        return self::BADGES[$this->status] ?? 'secondary';
    }

    /*----------------------------------------
     | Relationships
     ----------------------------------------*/

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function assigneeUser()
    {
        return $this->belongsTo(User::class, 'assignee');
    }

    public function reporterUser()
    {
        return $this->belongsTo(User::class, 'reporter');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'project_id');
    }

    public function history()
    {
        return $this->hasMany(ProjectHistory::class, 'project_id')->latest();
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'entity');
    }

    public function quoteRequest()
    {
        return $this->hasOneThrough(
            QuoteRequest::class,
            Lead::class,
            'id',                // Lead.id
            'id',                // QuoteRequest.id
            'lead_id',           // Project.lead_id
            'quote_request_id'   // Lead.quote_request_id
        );
    }
}
