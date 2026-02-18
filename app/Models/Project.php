<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "quote_request_id",
        "quote_master_id",
        "lead_id",
        "customer_id",
        "project_code",
        "status",
        "survey_date",
        "installation_start_date",
        "installation_end_date",
        "inspection_date",
        "handover_date",
        "estimated_complete_date",
        "actual_complete_date",
        "design_file",
        "boq_file",
        "assignee",
        "reporter",
        "finalize_price",
        "emi",
        "billing_status",
        "invoice_number",
        "invoice_date",
        "payment_received",
        "subsidy_status",
        "subsidy_amount",
        "subsidy_file",
        "site_photos",
        "documents",
        "priority",
        "current_step",
        "next_step",
        "is_on_hold",
        "hold_reason",
        "project_note",
        "created_at",
        "updated_at",
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
        'emi' => 'array',
        'next_step' => 'array',
        'meta' => 'array'
    ];

    protected $with = []; //'quoteRequest','quoteMaster'

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
    public const SUBSIDY_STATUS_LABELS = [
        'not_applied'           => 'Not Applied',
        'applied'               => 'Applied',
        'inspection_pending'    => 'Inspection Pending',
        'approved'              => 'Approved',
        'rejected'              => 'Rejected',
        'subsidy_released'      => 'Subsidy Released',
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

    public static function generateCode(): string
    {
        $now = Carbon::now();

        $prefix = 'PRJ';
        $yearMonth = $now->format('ym'); // 2509

        // Find last project of current month
        $lastCode = self::where('project_code', 'like', "{$prefix}-{$yearMonth}-%")
            ->orderByDesc('id')
            ->value('project_code');

        if ($lastCode) {
            // Extract last number
            $lastNumber = (int) substr($lastCode, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf(
            '%s-%s-%04d',
            $prefix,
            $yearMonth,
            $nextNumber
        );
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
        return $this->hasOne(Invoice::class, 'project_id');
    }

    public function history()
    {
        return $this->hasMany(ProjectHistory::class, 'project_id')->latest();
    }

    public function quoteRequest()
    {
        return $this->belongsTo(\App\Models\QuoteRequest::class);
    }

    public function projectDocuments()
    {
        return $this->morphMany(Document::class, 'entity',null);
    }

    public function boqs()
    {
        return $this->hasOne(Boq::class);
    }

    public function quoteMaster()
    {
        return $this->hasOneThrough(
            QuoteMaster::class,     // final model
            QuoteRequest::class,    // intermediate model
            'id',                   // FK on quote_requests pointing to quote_masters? NO
            'id',                   // local key on quote_masters
            'quote_request_id',     // FK on leads table
            'quote_master_id'       // FK on quote_requests table
        );
    }
}
