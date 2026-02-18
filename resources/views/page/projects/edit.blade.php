@extends('temp.common')

@section('title', 'Edit Project')
@section('head')
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
@endsection
@section('content')
    <div class="container-fluid">
        <div class="crm-page">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-0">{{ $project->project_code }}</h4>
                    <div class="text-muted small">
                        {{ $project->customer?->name }}
                    </div>
                </div>

                <div class="d-flex gap-2">
                    @if ($project->is_on_hold)
                        <button class="btn btn-outline-success" type="button" onclick="resumeProject()">
                            <i class="fa-solid fa-play"></i> Resume
                        </button>
                    @else
                        <button class="btn btn-outline-danger" type="button" onclick="openHoldModal()">
                            <i class="fa-solid fa-pause"></i> Hold
                        </button>
                    @endif

                    <button class="btn btn-primary" onclick="submitForm()">
                        <i class="fa-solid fa-save"></i> Save
                    </button>
                    <a class="btn btn-outline-secondary" href="{{ route('projects.view.list') }}/{{ $project->id }}">
                        <i class="fa-solid fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            @if ($project->is_on_hold && $project->hold_reason)
                <div class="alert alert-warning mb-0 small">
                    <b>Hold Reason:</b> {{ $project->hold_reason }}
                </div>
            @endif
            @if ($project->customer)
                <div class="crm-section">
                    <div class="fw-semibold mb-2">
                        <i class="fa-solid fa-user me-1"></i>
                        Customer Information
                    </div>

                    <div class="row g-3 align-items-start">

                        {{-- PROFILE --}}
                        <div class="col-md-2 text-center">
                            <img src="{{ $project->customer->passport_size_photo
                                ? asset('storage/' . $project->customer->passport_size_photo)
                                : asset('assets/img/avatar-placeholder.png') }}"
                                class="rounded-circle mb-1" style="width:70px;height:70px;object-fit:cover;">

                            <div class="fw-semibold small">
                                {{ $project->customer->name }}
                            </div>
                        </div>

                        {{-- DETAILS --}}
                        <div class="col-md-10">
                            <div class="row g-3">

                                <div class="col-md-3">
                                    <div class="text-muted small">Mobile</div>
                                    <div class="fw-semibold">
                                        {{ $project->customer->mobile ?? '—' }}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="text-muted small">Alternate Mobile</div>
                                    <div class="fw-semibold">
                                        {{ $project->customer->alternate_mobile ?? '—' }}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="text-muted small">Email</div>
                                    <div class="fw-semibold">
                                        {{ $project->customer->email ?? '—' }}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="text-muted small">Service Number</div>
                                    <div class="fw-semibold">
                                        {{ $project->customer->service_number ?? '—' }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="text-muted small">Address</div>
                                    <div class="fw-semibold">
                                        {{ $project->customer->address ?? '—' }}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="text-muted small">Aadhar No</div>
                                    <div class="fw-semibold">
                                        {{ $project->customer->aadhar_card_number ?? '—' }}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="text-muted small">PAN No</div>
                                    <div class="fw-semibold">
                                        {{ $project->customer->pan_card_number ?? '—' }}
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            @endif
            <div class="row g-3">
                <div class="col-xl-6">
                    <div class="crm-section">
                        <div class="fw-semibold mb-2">Execution</div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small">Priority</label>
                                <select name="priority" class="form-select">
                                    @foreach (['low', 'medium', 'high'] as $p)
                                        <option value="{{ $p }}" @selected($project->priority === $p)>
                                            {{ ucfirst($p) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Assignee</label>
                                <select name="assignee" class="form-select">
                                    <option value="">—</option>
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}" @selected($project->assignee == $u->id)>
                                            {{ $u->fname }} {{ $u->lname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Reporter</label>
                                <select name="reporter" class="form-select">
                                    <option value="">—</option>
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}" @selected($project->reporter == $u->id)>
                                            {{ $u->fname }} {{ $u->lname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="crm-section mt-2">
                        <div class="fw-semibold mb-2">Timeline</div>

                        <div class="row g-2">
                            @foreach ([
                                'survey_date' => 'Survey',
                                'installation_start_date' => 'Install Start',
                                'installation_end_date' => 'Install End',
                                'inspection_date' => 'Inspection',
                                'handover_date' => 'Handover',
                                'estimated_complete_date' => 'Estimated Complete',
                            ] as $field => $label)
                                <div class="col-md-4">
                                    <label class="form-label small">{{ $label }}</label>
                                    <input type="date" name="{{ $field }}" class="form-control"
                                        value="{{ optional($project->$field)->format('Y-m-d') }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    {{-- ================= FINANCIALS ================= --}}
                    <div class="crm-section mt-2">
                        <div class="fw-semibold mb-2">Financials</div>

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small">Final Price</label>
                                <input type="number" name="finalize_price" id="finalPrice" class="form-control"
                                    value="{{ $project->finalize_price }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small">Billing Status</label>
                                <input type="text" class="form-control" readonly
                                    value="{{ ucfirst($project->billing_status) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small">Subsidy Status</label>
                                <select name="subsidy_status" class="form-select">
                                    @foreach (['pending', 'applied', 'approved', 'rejected'] as $s)
                                        <option value="{{ $s }}" @selected($project->subsidy_status === $s)>
                                            {{ ucfirst($s) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small">Subsidy Amount</label>
                                <input type="number" name="subsidy_amount" class="form-control"
                                    value="{{ $project->subsidy_amount }}">
                            </div>
                        </div>
                    </div>
                    {{-- ================= EMI EDITOR ================= --}}
                    <div class="crm-section mt-2">
                        <div class="fw-semibold mb-2">EMI Structure</div>

                        <div id="emiRows"></div>

                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addEmiRow()">
                            + Add EMI
                        </button>
                    </div>
                    <div class="crm-section mt-2">
                        <div class="fw-semibold mb-2">Project Documents</div>
                        <a href="{{ route('documents.view.list', ['project_id' => $project->id]) }}"
                            class="btn btn-sm btn-outline-primary">
                            View Documents
                        </a>
                    </div>
                    <div class="crm-section mt-2">
                        <div class="fw-semibold mb-2">Notes</div>

                        <div id="projectNoteEditor" style="height:160px">
                            {!! $project->project_note !!}
                        </div>

                        <input type="hidden" name="project_note">
                    </div>
                </div>
                <div class="col-xl-6">
                    {{-- ================= BILLING SNAPSHOT ================= --}}
                    <div class="crm-section">
                        <div class="fw-semibold mb-2">
                            <i class="fa-solid fa-wallet me-1"></i>
                            Billing Overview
                        </div>

                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="crm-stat">
                                    <div class="text-muted small">Final Price</div>
                                    <div class="fs-5 fw-bold">
                                        ₹ {{ number_format($project->finalize_price, 2) }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="crm-stat">
                                    <div class="text-muted small">Invoiced</div>
                                    <div class="fs-5 fw-bold text-primary">
                                        ₹ {{ number_format($invoice->total ?? 0, 2) }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="crm-stat">
                                    <div class="text-muted small">Paid</div>
                                    <div class="fs-5 fw-bold text-success">
                                        ₹ {{ number_format(($invoice->total ?? 0) - ($invoice->balance ?? 0), 2) }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="crm-stat">
                                    <div class="text-muted small">Balance</div>
                                    <div class="fs-5 fw-bold text-danger">
                                        ₹ {{ number_format($invoice->balance ?? 0, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- ================= EMI TIMELINE ================= --}}
                    @if ($project->emi)
                        <div class="crm-section mt-2">
                            <div class="fw-semibold mb-2">
                                EMI Schedule
                            </div>
                            <div class="crm-timeline">

                                @foreach ($project->emi as $date => $amount)
                                    @php
                                        $emiDate = \Carbon\Carbon::parse($date);
                                        $today = \Carbon\Carbon::today();

                                        if (isset($paidEmiDates[$date])) {
                                            $state = 'paid';
                                            $label = 'Paid';
                                        } elseif ($emiDate->lt($today)) {
                                            $state = 'overdue';
                                            $label = 'Overdue';
                                        } elseif ($emiDate->isSameDay($today)) {
                                            $state = 'due';
                                            $label = 'Due Today';
                                        } else {
                                            $state = 'upcoming';
                                            $label = 'Upcoming';
                                        }
                                    @endphp

                                    <div class="crm-timeline-item {{ $state }}">
                                        <div class="dot"></div>

                                        <div class="content">
                                            <div class="d-flex justify-content-between">
                                                <div class="fw-semibold">
                                                    ₹ {{ number_format($amount, 2) }}
                                                </div>
                                                <span
                                                    class="badge bg-{{ $state === 'paid'
                                                        ? 'success'
                                                        : ($state === 'overdue'
                                                            ? 'danger'
                                                            : ($state === 'due'
                                                                ? 'warning'
                                                                : 'secondary')) }}">
                                                    {{ $label }}
                                                </span>
                                            </div>

                                            <div class="d-flex align-items-baseline justify-content-between">
                                                <div class="small text-muted">
                                                    EMI Date : {{ $emiDate->format('D, d M Y') }}
                                                </div>
                                                @if ($state == 'paid' && !empty($paidEmiDates[$date]))
                                                    <div class="small text-danger-emphasis fst-italic text-decoration-underline">
                                                        Paid On : 
                                                            {{ \Carbon\Carbon::parse($paidEmiDates[$date])->format('D, d M Y') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <hr>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    @endif

                    <div class="crm-section mt-2">
                        <div class="fw-semibold mb-2">Invoice & Payments</div>
                        {{-- ================= INVOICE SUMMARY ================= --}}
                        @if ($invoice)
                            <div class="crm-section mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="fw-semibold">
                                        Invoice · {{ $invoice->invoice_no }}
                                    </div>

                                    <a href="{{ route('invoices.view.edit', $invoice->id) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary">
                                        Open Invoice
                                        <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
                                    </a>
                                </div>

                                <div class="row g-3 small">
                                    <div class="col-md-3">
                                        <div class="text-muted">Invoice Date</div>
                                        <div>{{ $invoice->invoice_date }}</div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="text-muted">Due Date</div>
                                        <div>{{ $invoice->due_date }}</div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="text-muted">Status</div>
                                        <span class="badge bg-info-subtle text-info">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="text-muted">Created By</div>
                                        <div>{{ $invoice->creator->fname ?? '—' }}{{ $invoice->creator->lname ?? '—' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- ================= PAYMENT HISTORY ================= --}}
                        @if ($invoice && $invoice->payments->count())
                            <div class="crm-section mt-2">
                                <div class="fw-semibold mb-2">
                                    Payment History
                                </div>

                                <div class="crm-table-wrapper">
                                    <table class="table crm-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Amount</th>
                                                <th>Date</th>
                                                <th>Method</th>
                                                <th>Received By</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($invoice->payments as $i => $p)
                                                <tr>
                                                    <td>{{ $i + 1 }}</td>
                                                    <td class="fw-semibold text-success">
                                                        ₹ {{ number_format($p->amount, 2) }}
                                                    </td>
                                                    <td>{{ $p->paid_at->format('d-m-Y') }}</td>
                                                    <td>{{ $p->method }}</td>
                                                    <td>{{ $p->receiver->fname ?? '—' }} {{ $p->receiver->lname ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-12">
                    {{-- ================= MILESTONES ================= --}}
                    <div class="crm-section mt-2">
                        <div class="fw-semibold mb-2">Milestones (Planned Dates)</div>

                        <div class="row g-3">
                            @foreach ($project->meta['milestones'] ?? [] as $i => $m)
                                <div class="col-md-4">
                                    <label class="form-label small">
                                        {{ $m['title'] }}
                                    </label>
                                    <input type="date" name="milestones[{{ $i }}][planned_date]"
                                        class="form-control" value="{{ $m['planned_date'] }}">
                                    <input type="hidden" name="milestones[{{ $i }}][key]"
                                        value="{{ $m['key'] }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @if ($quoteRequest || $quoteMaster)
                    <div class="crm-section mt-2">
                        <div class="fw-semibold mb-2">
                            <i class="fa-solid fa-file-lines me-1"></i>
                            Quote Information
                        </div>

                        <div class="row g-3 mt-2">

                            {{-- QUOTE REQUEST --}}
                            @if ($quoteRequest)
                                <div class="col-md-6">
                                    <div class="border rounded p-2 h-100">
                                        <div class="fw-semibold small mb-1">Quote Request</div>

                                        <div class="small text-muted mb-1">
                                            Type: <b>{{ $quoteRequest->type ?? '—' }}</b>
                                        </div>

                                        <div class="small text-muted mb-1">
                                            Module: <b>{{ $quoteRequest->module ?? '—' }}</b>
                                        </div>

                                        <div class="small text-muted mb-1">
                                            kW: <b>{{ $quoteRequest->kw ?? '—' }}</b>
                                        </div>

                                        <div class="small text-muted mb-1">
                                            Budget: <b>₹ {{ number_format($quoteRequest->budget ?? 0) }}</b>
                                        </div>

                                        <div class="small text-muted">
                                            Status:
                                            <span class="badge bg-secondary">
                                                {{ str_replace('_', ' ', $quoteRequest->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- QUOTE MASTER --}}
                            @if ($quoteMaster)
                                <div class="col-md-6">
                                    <div class="border rounded p-2 h-100">
                                        <div class="fw-semibold small mb-1">Quote Master</div>

                                        <div class="small text-muted mb-1">
                                            SKU: <b>{{ $quoteMaster->sku ?? '—' }}</b>
                                        </div>

                                        <div class="small text-muted mb-1">
                                            kW: <b>{{ $quoteMaster->kw ?? '—' }}</b>
                                        </div>

                                        <div class="small text-muted mb-1">
                                            Modules: <b>{{ $quoteMaster->module_count ?? '—' }}</b>
                                        </div>

                                        <div class="small text-muted mb-1">
                                            Base Value:
                                            <b>₹ {{ number_format($quoteMaster->value ?? 0) }}</b>
                                        </div>

                                        <div class="small text-muted mb-1">
                                            Taxes:
                                            <b>₹ {{ number_format($quoteMaster->taxes ?? 0) }}</b>
                                        </div>

                                        <div class="small text-muted mb-1">
                                            Payable:
                                            <b>₹ {{ number_format($quoteMaster->payable ?? 0) }}</b>
                                        </div>

                                        <div class="small text-muted">
                                            Subsidy:
                                            <b>₹ {{ number_format($quoteMaster->subsidy ?? 0) }}</b>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                @endif
                </div>
            </div>

            <form id="projectForm">


                {{-- ================= QUOTE ================= --}}
                









            </form>

        </div>
    </div>
    {{-- ================= HOLD MODAL ================= --}}
    <div class="modal fade" id="holdModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title">
                        Put Project On Hold
                    </h6>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label small">
                        Reason (required)
                    </label>
                    <textarea id="holdReason" class="form-control" rows="3" placeholder="Explain why the project is paused"></textarea>
                </div>



                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button class="btn btn-danger" onclick="confirmHold()">
                        Put On Hold
                    </button>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const UPDATE_URL = "{{ route('projects.ajax.update', $project->id) }}";
        const EXISTING_EMI = @json($project->emi ?? []);
    </script>
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script src="{{ asset('assets/js/page/projects/form.js') }}"></script>
@endpush
