@extends('temp.common')
@section('title', 'Invoice')

@section('head')
<style>
    .crm-timeline-item:first-child .crm-timeline-content {
        border-left: 3px solid var(--bs-danger);
    }
</style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="crm-page">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h4 class="mb-1">
                        <i class="fa-solid fa-file-invoice-dollar me-2"></i>
                        Invoice {{ $invoice->invoice_no }}
                    </h4>
                    <div class="text-muted small">
                        {{ $invoice->customer?->name }} |
                        {{ optional($invoice->invoice_date)->format('d M Y') }}
                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center">

                    <span class="badge bg-secondary">
                        {{ ucfirst($invoice->status) }}
                    </span>
                    @can('billing.edit')
                        <a href="/invoices/{{ $invoice->id }}/edit" class="btn btn-sm btn-primary" title="View">
                            <i class="fa-solid fa-pen me-1"> </i>Edit
                        </a>
                        <button class="btn btn-sm btn-outline-primary" id="generatePdfBtn">
                            <i class="fa-solid fa-file-pdf me-1"></i>
                            {{ $invoice->pdf_path ? 'Regenerate PDF' : 'Generate PDF' }}
                        </button>
                    @endcan

                    @if ($invoice->pdf_path)
                        <a href="{{ Storage::disk('public')->url($invoice->pdf_path) }}" target="_blank"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="fa-solid fa-eye me-1"></i>
                            View PDF
                        </a>
                    @endif
                </div>

            </div>

            {{-- DETAILS --}}
            <div class="crm-section">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="text-muted small">Customer</div>
                        <div class="fw-semibold">{{ $invoice->customer?->name }}</div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-muted small">Invoice Date</div>
                        <div>{{ optional($invoice->invoice_date)->format('Y-m-d') }}</div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-muted small">Due Date</div>
                        <div>{{ optional($invoice->due_date)->format('Y-m-d') ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="crm-section">
                <div class="row g-3 text-center">
                    <div class="col-md-4">
                        <div class="text-muted small">Total</div>
                        <div class="fs-5 fw-bold text-primary">
                            ₹ <span id="invTotal">0.00</span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-muted small">Paid</div>
                        <div class="fs-5 fw-bold text-success">
                            ₹ <span id="invPaid">0.00</span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-muted small">Remaining</div>
                        <div class="fs-5 fw-bold text-danger">
                            ₹ <span id="invRemaining">0.00</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="crm-section position-relative" id="invoiceWidgets" style="min-height: 100px">
                <div class="crm-loader-overlay">
                    <div class="crm-spinner"></div>
                </div>
            </div>
            {{-- ITEMS --}}
            <div class="crm-section">
                <table class="table crm-table mb-0">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th width="120">Price</th>
                            <th width="80">Qty</th>
                            <th width="120">Tax</th>
                            <th width="120">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->items as $i)
                            <tr>
                                <td>{{ $i->description }}</td>
                                <td>₹ {{ number_format($i->unit_price, 2) }}</td>
                                <td>{{ $i->quantity }}</td>
                                <td>₹ {{ number_format($i->tax, 2) }}</td>
                                <td>₹ {{ number_format($i->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- TOTALS --}}
            <div class="crm-section">
                <div class="row justify-content-end">
                    <div class="col-md-4">

                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Total Price</span>
                            <strong>₹ {{ $invoice->sub_total }}</strong>
                        </div>

                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Total Tax</span>
                            <strong>₹ {{ $invoice->tax_total }}</strong>
                        </div>

                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Discount</span>
                            <strong>₹ {{ $invoice->discount }}</strong>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between fs-5">
                            <span>Grand Total</span>
                            <strong>₹ {{ $invoice->total }}</strong>
                        </div>

                    </div>
                </div>
            </div>
            <div class="crm-section">
                <h6 class="mb-2">Payments</h6>
                <div class="crm-table-wrapper position-relative">
                    <table class="table crm-table mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Reference</th>
                                <th>Amount</th>
                                <th>EMI</th>
                                <th>Received By</th>
                            </tr>
                        </thead>
                        <tbody id="paymentTable">
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    <td colspan="6">
                                        <div class="crm-skeleton"></div>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                    <div class="crm-loader-overlay" id="paymentTableLoader">
                        <div class="crm-spinner"></div>
                    </div>
                </div>
            </div>
            <div class="crm-section">
                <h6 class="mb-2">Add Payment</h6>

                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small">EMI</label>
                        <select id="emiSelect" class="form-select">
                            <option value="">Manual Payment</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Amount</label>
                        <input type="number" id="payAmount" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small">Method</label>
                        <select id="payMethod" class="form-select">
                            <option value="" selected>Select Method</option>
                            <option value="cash">Cash</option>
                            <option value="upi">UPI</option>
                            <option value="bank">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small">Reference</label>
                        <input type="text" id="payRef" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small">Paid On</label>
                        <input type="date" id="payDate" value="{{ now()->toDateString() }}" class="form-control">
                    </div>
                    @can('billing.edit')
                        <div class="col-md-12 text-end">
                            <button class="btn btn-primary" onclick="submitPayment()">
                                Add Payment
                            </button>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        const GENERATE_PDF_URL = "{{ route('invoices.ajax.generatePdf', $invoice->id) }}";
        const PAYMENTS_URL = "{{ route('invoices.ajax.payments', $invoice->id) }}";
        const UPCOMING_PAYMENTS_URL = "{{ route('invoices.ajax.upcomingPayments', $invoice->id) }}";
    </script>
    <script src="{{ asset('assets/js/page/invoices/view.js') }}"></script>
@endpush
