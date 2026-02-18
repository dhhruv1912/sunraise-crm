@extends('temp.common')

@section('title', 'Create Quotation')

@section('content')
    <div class="container-fluid">
        <div class="crm-page">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1">
                        <i class="fa-solid fa-file-circle-plus me-2"></i>
                        Create Quotation
                    </h4>
                    <div class="text-muted small">
                        Lead — {{ $lead->lead_code }}
                    </div>
                </div>

                <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="row g-3">

                {{-- LEFT --}}
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
                        </div>
                    </div>

                    {{-- PRICING FORM --}}
                    <div class="crm-section mt-3 position-relative">

                        <div class="fw-semibold mb-2">
                            <i class="fa-solid fa-indian-rupee-sign me-1"></i>
                            Pricing
                        </div>

                        <input type="hidden" id="leadId" value="{{ $lead->id }}">

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small">Base Price</label>
                                <input type="number" id="basePrice" class="form-control"
                                    value="{{ optional($lead->quoteMaster)->payable ?? 0 }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Discount</label>
                                <input type="number" id="discount" class="form-control" value="0">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Final Price</label>
                                <input type="number" id="finalPrice" class="form-control fw-bold text-primary" readonly>
                            </div>
                        </div>

                        <div class="crm-loader-overlay d-none" id="quotationLoader">
                            <div class="crm-spinner"></div>
                        </div>

                    </div>

                </div>

                {{-- RIGHT --}}
                <div class="col-md-4">

                    <div class="crm-section">
                        <div class="fw-semibold mb-2">
                            <i class="fa-solid fa-gears me-1"></i>
                            Actions
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-success" onclick="submitQuotation()">
                                <i class="fa-solid fa-check me-1"></i>
                                Create Quotation
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function recalcPrice() {
            const base = Number(document.getElementById('basePrice').value || 0);
            const discount = Number(document.getElementById('discount').value || 0);
            document.getElementById('finalPrice').value =
                Math.max(base - discount, 0);
        }

        document.addEventListener('input', e => {
            if (['basePrice', 'discount'].includes(e.target.id)) {
                recalcPrice();
            }
        });

        recalcPrice();

        function submitQuotation() {
            const loader = document.getElementById('quotationLoader');
            loader.classList.remove('d-none');

            crmFetch(`{{ route('quotations.ajax.store') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        lead_id: document.getElementById('leadId').value,
                        base_price: document.getElementById('basePrice').value,
                        discount: document.getElementById('discount').value,
                        final_price: document.getElementById('finalPrice').value
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (!res.status) {
                        showToast('danger', res.message || 'Failed');
                        return;
                    }
                    showToast('success', res.message);
                    window.location.href = res.redirect;
                })
                .finally(() => loader.classList.add('d-none'));
        }
    </script>
@endpush
