@extends('temp.common')

@section('title', 'Lead Details')

@section('content')
    <div class="container-fluid">
        <div class="crm-page">

            {{-- ================= HEADER ================= --}}
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h4 class="mb-1">
                        <i class="fa-solid fa-user-check me-2 text-primary"></i>
                        Lead — {{ $lead->lead_code }}
                    </h4>
                    <div class="text-muted small">
                        From Quote Request #{{ $lead->quote_request_id }}
                    </div>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge bg-info-subtle text-info text-capitalize align-content-center">
                        {{ str_replace('_', ' ', $lead->status) }}
                    </span>

                    <a href="{{ route('leads.view.list') }}" class="btn btn-light btn-sm">
                        <i class="fa-solid fa-arrow-left"></i>
                        Back
                    </a>
                </div>
            </div>

            <div class="row g-3">

                {{-- ================= LEFT ================= --}}
                <div class="col-md-8">

                    {{-- CUSTOMER --}}
                    <div class="crm-section">
                        <div class="fw-semibold mb-2">
                            <i class="fa-solid fa-user me-1"></i>
                            Customer
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="text-muted small">Name</div>
                                <div class="fw-semibold">{{ $lead->customer->name }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">Mobile</div>
                                <div class="fw-semibold">{{ $lead->customer->mobile }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">Email</div>
                                <div>{{ $lead->customer->email ?? '—' }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">Address</div>
                                <div>{{ $lead->customer->address ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- LEAD SNAPSHOT --}}
                    <div class="crm-section mt-3">
                        <div class="fw-semibold mb-2">
                            <i class="fa-solid fa-bullseye me-1"></i>
                            Lead Snapshot
                        </div>

                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="text-muted small">Assigned To</div>
                                <div>
                                    {{ optional($lead->assignedUser)->fname }}
                                    {{ optional($lead->assignedUser)->lname ?? '—' }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-muted small">Next Follow-up</div>
                                <div>{{ $lead->next_followup_at ?? '—' }}</div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-muted small">Priority</div>
                                <div class="text-capitalize">{{ $lead->priority ?? 'medium' }}</div>
                            </div>
                        </div>

                        @if ($lead->remarks)
                            <div class="mt-2">
                                <div class="text-muted small">Remarks</div>
                                <div>{{ $lead->remarks }}</div>
                            </div>
                        @endif
                    </div>

                    {{-- QUOTE REQUEST --}}
                    <div class="crm-section mt-3">
                        <div class="fw-semibold mb-2">
                            <i class="fa-solid fa-file-signature me-1"></i>
                            Quote Request
                        </div>

                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="text-muted small">Type</div>
                                <div>{{ $lead->quoteRequest->type }}</div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-muted small">Capacity</div>
                                <div>{{ $lead->quoteRequest->kw }} kW</div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-muted small">MC</div>
                                <div>{{ $lead->quoteRequest->mc }}</div>
                            </div>

                            <div class="col-md-12">
                                <div class="text-muted small">Notes</div>
                                <div>{{ $lead->quoteRequest->notes ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- SUGGESTED PACKAGE --}}
                    <div class="crm-section mt-3">
                        <div class="fw-semibold mb-2">
                            <i class="fa-solid fa-box me-1"></i>
                            Suggested Quote Package
                        </div>

                        @if ($lead->quoteMaster)
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div class="text-muted small">SKU</div>
                                    <div>{{ $lead->quoteMaster->sku }}</div>
                                </div>

                                <div class="col-md-4">
                                    <div class="text-muted small">Capacity</div>
                                    <div>{{ $lead->quoteMaster->kw }} kW</div>
                                </div>

                                <div class="col-md-4">
                                    <div class="text-muted small">Modules</div>
                                    <div>{{ $lead->quoteMaster->module_count }}</div>
                                </div>

                                <div class="col-md-4">
                                    <div class="text-muted small">Payable</div>
                                    <div class="fw-bold text-primary">
                                        ₹ {{ number_format($lead->quoteMaster->payable) }}
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="text-muted small">Subsidy</div>
                                    <div class="fw-semibold text-warning">
                                        ₹ {{ number_format($lead->quoteMaster->subsidy) }}
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="text-muted small">Projected</div>
                                    <div class="fw-semibold text-success">
                                        ₹ {{ number_format($lead->quoteMaster->projected) }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-muted small">
                                No quote package suggested yet
                            </div>
                        @endif
                    </div>

                    <div class="crm-section mt-3">

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="fw-semibold">
                                <i class="fa-solid fa-file-invoice"></i>
                                Quotations
                            </div>
                            @can('quotation.edit')
                                <a href="{{ route('quotations.view.create', $lead->id) }}"
                                class="btn btn-outline-primary btn-sm">
                                    <i class="fa-solid fa-plus"></i>
                                    New
                                </a>
                            @endcan
                        </div>

                        {{-- CONTENT --}}
                        @forelse($lead->quotation as $q)
                            <div class="border rounded-3 p-3 mb-3 position-relative">

                                {{-- TOP ROW --}}
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="fw-semibold">
                                            {{ $q->quotation_no }}
                                        </div>
                                        <div class="text-muted small">
                                            Created {{ $q->created_at->format('d M Y h:i A') }}
                                            @if($q->sent_at)
                                                • Sent
                                            @endif
                                        </div>
                                    </div>

                                    <span class="badge 
                                        {{ $q->sent_at 
                                            ? 'bg-success-subtle text-success' 
                                            : 'bg-warning-subtle text-warning' }}">
                                        {{ $q->sent_at ? 'Sent' : 'Draft' }}
                                    </span>
                                </div>

                                {{-- PRICE --}}
                                <div class="row g-2 mb-2 small">
                                    <div class="col">
                                        <div class="text-muted">Base</div>
                                        <div>₹ {{ number_format($q->base_price) }}</div>
                                    </div>
                                    <div class="col">
                                        <div class="text-muted">Discount</div>
                                        <div>₹ {{ number_format($q->discount) }}</div>
                                    </div>
                                    <div class="col">
                                        <div class="text-muted fw-semibold">Final</div>
                                        <div class="fw-bold text-primary">
                                            ₹ {{ number_format($q->final_price) }}
                                        </div>
                                    </div>
                                </div>

                                {{-- ACTIONS --}}
                                <div class="d-flex gap-2 mb-2">
                                    <a href="{{ route('quotations.view.show', $q->id) }}"
                                    class="btn btn-light btn-sm">
                                        <i class="fa-solid fa-eye"></i> View
                                    </a>

                                    @if(!$q->pdf_path)
                                        <button class="btn btn-outline-secondary btn-sm"
                                                onclick="generateQuotationPdf({{ $q->id }})">
                                            <i class="fa-solid fa-file-pdf"></i> Generate PDF
                                        </button>
                                    @endif
                                </div>

                                {{-- PDF PREVIEW --}}
                                @if($q->pdf_path)
                                    <div class="mt-2 border rounded-3 overflow-hidden">
                                        <embed src="{{ asset('storage/'.$q->pdf_path) }}"
                                            type="application/pdf"
                                            class="w-100"
                                            style="min-height: 420px;">
                                    </div>
                                @endif

                            </div>
                        @empty
                            <div class="text-muted small text-center py-4">
                                No quotations created yet.
                            </div>
                        @endforelse

                    </div>

                </div>

                {{-- ================= RIGHT ================= --}}
                <div class="col-md-4">

                    {{-- ACTIONS --}}
                    <div class="crm-section">
                        <div class="fw-semibold mb-2">
                            <i class="fa-solid fa-gears me-1"></i>
                            Actions
                        </div>

                        <div class="d-grid gap-2">
                            @can('marketing.lead.edit')
                                @if ($lead->project->id)
                                    <button disabled class="btn btn-outline-primary">
                                        <i class="fa-solid fa-pen me-1"></i>
                                        Edit
                                    </button>
                                    <button class="btn btn-success" id="convertToProjectBtn"
                                        disabled>
                                        <i class="fa-solid fa-diagram-project me-1"></i>
                                        Convert to Project
                                    </button>
                                    <div class="text-success small mt-1">
                                        <i class="fa-solid fa-check-circle"></i>
                                        Already converted to Lead
                                    </div>
                                @else
                                    <a href="{{ route('leads.view.edit', $lead->id) }}" class="btn btn-outline-primary">
                                        <i class="fa-solid fa-pen me-1"></i>
                                        Edit
                                    </a>
                                    <button class="btn btn-success" id="convertToProjectBtn"
                                        onclick="openConvertCanvas({{ $lead->id }})" data-id="{{ $lead->id }}">
                                        <i class="fa-solid fa-diagram-project me-1"></i>
                                        Convert to Project
                                    </button>
                                    @endif
                                    @if(!$lead->quoteMaster)
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            Assign Quote Master First
                                        </button>
                                    @else
                                        <a href="{{ route('quotations.view.create', $lead->id) }}"
                                        class="btn btn-primary btn-sm">
                                            Create Quotation
                                        </a>
                                    @endif
                            @endcan
                        </div>
                    </div>

                </div>

            </div>

            {{-- ================= TIMELINE ================= --}}
            <div class="crm-section mt-4">
                <div class="fw-semibold mb-3">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i>
                    Activity Timeline
                </div>

                @forelse($lead->history as $h)
                    <div class="d-flex gap-3 mb-2">
                        <div class="text-muted small" style="width:140px">
                            {{ optional($h->created_at)->format('d M Y h:i A') }}
                        </div>
                        <div>
                            <div class="fw-semibold text-capitalize">
                                {{ $h->action }}
                            </div>
                            <div class="text-muted small">
                                {{ $h->message }}
                                —
                                {{ optional($h->user)->fname }}
                                {{ optional($h->user)->lname }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-muted small">
                        No activity yet
                    </div>
                @endforelse
            </div>

        </div>
    </div>
    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="leadConvertCanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Convert Lead</h5>
            <button class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body" id="leadCanvasBody"></div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/page/leads.view.js') }}"></script>
@endsection
