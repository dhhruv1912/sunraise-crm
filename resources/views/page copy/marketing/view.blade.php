@extends('temp.common')

@section('title', 'View Lead')

@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-baseline justify-content-between">
            <h4>Lead — {{ $lead->lead_code }}</h4>
            <div class="">
                <button class="btn btn-warning" id="create-project" data-id="{{ $lead->id }}">
                    Convert to Project
                </button>
                <button class="btn btn-info" id="view-lead-quotation" data-id="{{ $lead->id }}">
                    View Quotations
                </button>
                <button class="btn btn-secondary" id="view-lead-history" data-id="{{ $lead->id }}">
                    View History
                </button>
            </div>
        </div>
        <div class="card-body">

            <dl class="row">
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $lead->customer->name }}</dd>
                <dt class="col-sm-3">Mobile</dt>
                <dd class="col-sm-9">{{ $lead->customer->mobile }}</dd>
                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $lead->customer->email }}</dd>

                <dt class="col-sm-3">Assigned</dt>
                <dd class="col-sm-9">
                    {{ $lead->assignedUser->fname . ' ' . $lead->assignedUser->lname ?? '—' }}
                </dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    {{ \App\Models\Lead::$STATUS[$lead->status] }}
                </dd>

                <dt class="col-sm-3">Next Follow-up</dt>
                <dd class="col-sm-9">
                    {{ $lead->next_followup_at ?? '—' }}
                </dd>

                <dt class="col-sm-3">Remarks</dt>
                <dd class="col-sm-9">{{ $lead->remarks ?? '—' }}</dd>
            </dl>
            <hr>
            <div class="row g-2">
                <div class="col-md-6">
                    <h6>Quote Request Information</h6>
                    <dl class="row g-2">
                        <div class="col-sm-4">Name</div>
                        <div class="col-sm-8">{{ $lead->customer->name }}</div>
                        <div class="col-sm-4">Mobile</div>
                        <div class="col-sm-8">{{ $lead->customer->mobile }}</div>
                        <div class="col-sm-4">Email</div>
                        <div class="col-sm-8">{{ $lead->customer->email }}</div>
                        <div class="col-sm-4">Module</div>
                        <div class="col-sm-8">{{ $lead->quoteRequest->module }}</div>
                        <div class="col-sm-4">KW</div>
                        <div class="col-sm-8">{{ $lead->quoteRequest->kw }}</div>
                        <div class="col-sm-4">MC</div>
                        <div class="col-sm-8">{{ $lead->quoteRequest->mc }}</div>
                        <div class="col-sm-4">Status</div>
                        <div class="col-sm-8">{{ $lead->status }}</div>
                        <div class="col-sm-4">Assigned</div>
                        <div class="col-sm-8">{{ $lead->assignedUser->fname }} {{ $lead->assignedUser->lname }}</div>
                        <div class="col-sm-4">Notes</div>
                        <div class="col-sm-8">{{ $lead->notes }}</div>
                    </dl>
                </div>
                <div class="col-md-6">
                    <h6>Quote Master Information</h6>
                    <dl class="row g-2">
                        <div class="col-sm-4">SKU</div>
                        <div class="col-sm-8" id="modal-sku">{{ optional($lead->quoteMaster)->sku }}</div>
                        <div class="col-sm-4">Module</div>
                        <div class="col-sm-8" id="modal-module">{{ optional($lead->quoteMaster)->module }}</div>
                        <div class="col-sm-4">KW</div>
                        <div class="col-sm-8" id="modal-kw">{{ optional($lead->quoteMaster)->kw }}</div>
                        <div class="col-sm-4">Module Count</div>
                        <div class="col-sm-8" id="modal-module_count">{{ optional($lead->quoteMaster)->module_count }}
                        </div>
                        <div class="col-sm-4">Value</div>
                        <div class="col-sm-8" id="modal-value">{{ optional($lead->quoteMaster)->value }}</div>
                        <div class="col-sm-4">Taxes</div>
                        <div class="col-sm-8" id="modal-taxes">{{ optional($lead->quoteMaster)->taxes }}</div>
                        <div class="col-sm-4">Metering Cost</div>
                        <div class="col-sm-8" id="modal-metering_cost">{{ optional($lead->quoteMaster)->metering_cost }}
                        </div>
                        <div class="col-sm-4">MCB/PPA</div>
                        <div class="col-sm-8" id="modal-mcb_ppa">{{ optional($lead->quoteMaster)->mcb_ppa }}</div>
                        <div class="col-sm-4 bg-label-info">Payable</div>
                        <div class="col-sm-8 bg-label-info" id="modal-payable">{{ optional($lead->quoteMaster)->payable }}
                        </div>
                        <div class="col-sm-4 bg-label-warning">Subsidy</div>
                        <div class="col-sm-8 bg-label-warning" id="modal-subsidy">
                            {{ optional($lead->quoteMaster)->subsidy }}</div>
                        <div class="col-sm-4 bg-label-success">Projected</div>
                        <div class="col-sm-8 bg-label-success" id="modal-projected">
                            {{ optional($lead->quoteMaster)->projected }}
                        </div>
                    </dl>
                </div>
            </div>
            <hr>
            <a href="{{ route('marketing.index') }}" class="btn btn-secondary mb-3">Back</a>
        </div>

    </div>


    <div class="offcanvas offcanvas-end w-px-800" tabindex="-1" id="offcanvasWithBothOptions"
        aria-labelledby="offcanvasWithBothOptionsLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasWithBothOptionsLabel">Backdroped with scrolling</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div id="offcanvasWithBothOptionsBody" class="offcanvas-body">
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        window.QUOTE_ROUTES = {
            ajax: "{{ route('quotations.ajax') }}",
            create: "{{ route('quotations.create') }}",
            edit: "{{ url('quote/quotations') }}", // append /{id}/edit or /{id}/download etc
            generatePdf: "{{ url('quote/quotations') }}", // append /{id}/generate-pdf
            sendEmail: "{{ url('quote/quotations') }}" // append /{id}/send-email
        };
        window.auth = @json(auth()->user())
    </script>
    {{-- <script src="{{ asset('assets/js/page/quotations.js') }}"></script> --}}
    <script src="{{ asset('assets/js/page/quotation.view.js') }}"></script>
@endsection
