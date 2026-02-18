@extends('temp.common')

@section('title', 'Edit Lead')

@section('content')
<div class="container-fluid">
    <div class="crm-page">

        {{-- ================= HEADER ================= --}}
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h4 class="mb-1">
                    <i class="fa-solid fa-pen-to-square me-2 text-primary"></i>
                    Edit Lead — {{ $lead->lead_code }}
                </h4>
                <div class="text-muted small">
                    Update lead details carefully
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('leads.view.show', $lead->id) }}"
                   class="btn btn-light btn-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back
                </a>
            </div>
        </div>

        <div class="row g-3">

            {{-- ================= LEFT ================= --}}
            <div class="col-md-8">

                {{-- CONTEXT : CUSTOMER --}}
                <div class="crm-section">
                    <div class="fw-semibold mb-2">
                        <i class="fa-solid fa-user me-1"></i>
                        Customer (Read Only)
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
                            <div class="text-muted small">From Quote Request</div>
                            <div>#{{ $lead->quote_request_id }}</div>
                        </div>
                    </div>
                </div>

                {{-- EDITABLE FORM --}}
                <div class="crm-section mt-3 position-relative">

                    <div class="fw-semibold mb-2">
                        <i class="fa-solid fa-sliders me-1"></i>
                        Editable Fields
                    </div>

                    {{-- LOADER --}}
                    <div class="crm-loader-overlay d-none" id="leadEditLoader">
                        <div class="crm-spinner"></div>
                    </div>

                    <form id="leadEditForm">
                        @csrf

                        <div class="row g-3">

                            {{-- STATUS --}}
                            <div class="col-md-4">
                                <label class="form-label small">Status</label>
                                <select name="status" class="form-select">
                                    @foreach(\App\Models\Lead::$STATUS as $key => $label)
                                        <option value="{{ $key }}"
                                            @selected($lead->status === $key)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- ASSIGNED --}}
                            <div class="col-md-4">
                                <label class="form-label small">Assigned To</label>
                                <select name="assigned_to" class="form-select">
                                    <option value="">Unassigned</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}"
                                            @selected($lead->assigned_to == $u->id)>
                                            {{ $u->fname }} {{ $u->lname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- FOLLOW UP --}}
                            <div class="col-md-4">
                                <label class="form-label small">Next Follow-up</label>
                                <input type="date"
                                       name="next_followup_at"
                                       value="{{ $lead->next_followup_at }}"
                                       class="form-control">
                            </div>

                            {{-- REMARKS --}}
                            <div class="col-md-12">
                                <label class="form-label small">Remarks</label>
                                <textarea name="remarks"
                                          rows="3"
                                          class="form-control"
                                          placeholder="Internal remarks / follow-up notes">{{ $lead->remarks }}</textarea>
                            </div>

                        </div>

                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-save me-1"></i>
                                Save Changes
                            </button>
                        </div>

                    </form>
                </div>

            </div>

            {{-- ================= RIGHT ================= --}}
            <div class="col-md-4">

                {{-- CONTEXT : QUOTE MASTER --}}
                <div class="crm-section">
                    <div class="fw-semibold mb-2">
                        <i class="fa-solid fa-box me-1"></i>
                        Suggested Package (Read Only)
                    </div>

                    @if($lead->quoteMaster)
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="text-muted small">SKU</div>
                                <div>{{ $lead->quoteMaster->sku }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">Capacity</div>
                                <div>{{ $lead->quoteMaster->kw }} kW</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">Payable</div>
                                <div class="fw-bold text-primary">
                                    ₹ {{ number_format($lead->quoteMaster->payable) }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">Subsidy</div>
                                <div class="fw-semibold text-warning">
                                    ₹ {{ number_format($lead->quoteMaster->subsidy) }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-muted small">
                            No package assigned
                        </div>
                    @endif
                </div>

            </div>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const LEAD_UPDATE_URL = "{{ route('leads.ajax.update', $lead->id) }}";
    const LEAD_VIEW_URL   = "{{ route('leads.view.show', $lead->id) }}";
</script>
<script src="{{ asset('assets/js/page/leads.edit.js') }}"></script>
@endpush
