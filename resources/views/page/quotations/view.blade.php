@extends('temp.common')

@section('title', 'Quotation')

@section('content')
    <div class="container-fluid">
        <div class="crm-page">

            {{-- ================= HEADER ================= --}}
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h4 class="mb-1">
                        <i class="fa-solid fa-file-invoice-dollar me-2"></i>
                        Quotation — {{ $data->quotation_no }}
                    </h4>
                    <div class="text-muted small">
                        Customer: {{ optional($data->lead->customer)->name ?? '—' }}
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <span
                        class="badge {{ $data->sent_at ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">
                        {{ $data->sent_at ? 'Sent' : 'Draft' }}
                    </span>

                    <a href="{{ route('quotations.view.list') }}" class="btn btn-light btn-sm">
                        <i class="fa-solid fa-arrow-left"></i> Back
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
                            Customer Information
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="text-muted small">Name</div>
                                <div class="fw-semibold">{{ optional($data->lead->customer)->name }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Mobile</div>
                                <div class="fw-semibold">{{ optional($data->lead->customer)->mobile }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Email</div>
                                <div>{{ optional($data->lead->customer)->email ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- PRICING --}}
                    <div class="crm-section mt-3">
                        <div class="fw-semibold mb-2">
                            <i class="fa-solid fa-indian-rupee-sign me-1"></i>
                            Pricing Summary
                        </div>

                        <dl class="row g-2 mb-0">
                            <dt class="col-sm-4">Base Price</dt>
                            <dd class="col-sm-8">₹ {{ number_format($data->base_price ?? 0) }}</dd>

                            <dt class="col-sm-4">Discount</dt>
                            <dd class="col-sm-8">₹ {{ number_format($data->discount ?? 0) }}</dd>

                            <dt class="col-sm-4 fw-semibold text-primary">Final Price</dt>
                            <dd class="col-sm-8 fw-bold text-primary">
                                ₹ {{ number_format($data->final_price ?? 0) }}
                            </dd>
                        </dl>
                    </div>

                    {{-- PDF --}}
                    <div class="crm-section mt-3 position-relative">
                        <div class="fw-semibold mb-2">
                            <i class="fa-solid fa-file-pdf me-1"></i>
                            Quotation PDF
                        </div>

                        @if ($data->pdf_path)
                            <embed src="{{ asset('storage/' . $data->pdf_path) }}" type="application/pdf"
                                class="w-100 border rounded" style="min-height: 600px;">
                        @else
                            <div class="text-muted small">
                                PDF not generated yet.
                            </div>
                        @endif
                    </div>

                </div>

                {{-- ================= RIGHT ================= --}}
                <div class="col-md-4">

                    {{-- ACTIONS --}}
                    @can("quotation.edit")
                    <div class="crm-section">
                        <div class="fw-semibold mb-2">
                            <i class="fa-solid fa-gears me-1"></i>
                            Actions
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary" onclick="generatePdf({{ $data->id }})">
                                <i class="fa-solid fa-file-pdf me-1"></i>
                                Generate PDF
                            </button>

                            <button class="btn btn-outline-primary" onclick="sendQuotationEmail({{ $data->id }})">
                                <i class="fa-solid fa-paper-plane me-1"></i>
                                Send Email
                            </button>

                            @if (!$data->sent_at)
                                <div class="text-muted small">
                                    * Conversion allowed only after email is sent
                                </div>
                            @endif
                        </div>
                    </div>
                        
                    @endcan

                    {{-- META --}}
                    <div class="crm-section mt-3">
                        <div class="text-muted small">Created</div>
                        <div>{{ $data->created_at?->format('d M Y h:i A') }}</div>

                        @if ($data->sent_at)
                            <div class="text-muted small mt-2">Sent At</div>
                            <div>{{ $data->sent_at->format('d M Y h:i A') }}</div>
                        @endif
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function generatePdf(id) {
            crmFetch(`/quotations/${id}/generate-pdf`)
                .then(res => res.json())
                .then(res => {
                    if (!res.status) {
                        showToast('danger', res.message || 'PDF generation failed');
                        return;
                    }
                    showToast('success', 'PDF generated');
                    location.reload();
                });
        }

        function sendQuotationEmail(id) {
            // showLoader();

            crmFetch(`/quotations/ajax/${id}/send-email`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(res => res.json())
                .then(res => {
                    if (!res.status) {
                        showToast('danger', res.message);
                        return;
                    }
                    showToast('success', res.message);
                    location.reload();
                })
            // .finally(hideLoader);
        }
    </script>
@endpush
