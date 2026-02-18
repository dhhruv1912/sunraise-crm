@extends('temp.common')

@section('title', 'Invoices')
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
                        Invoices
                    </h4>
                    <div class="text-muted small">
                        Generated invoices list
                    </div>
                </div>
            </div>

            {{-- WIDGETS --}}
            <div class="row g-3 mt-2 position-relative" id="invoiceWidgets" style="min-height:100px">
                <div class="crm-loader-overlay">
                    <div class="crm-spinner"></div>
                </div>
            </div>
            <div class="crm-section position-relative" id="invoiceWidgets2" style="min-height:100px">
                <div class="crm-loader-overlay">
                    <div class="crm-spinner"></div>
                </div>
            </div>

            {{-- FILTER BAR --}}
            <div class="crm-section mt-2">
                <div class="row g-3 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label small">Status</label>
                        <select id="filterStatus" class="form-select">
                            <option value="">All</option>
                            <option value="draft">Draft</option>
                            <option value="sent">Sent</option>
                            <option value="partial">Partial</option>
                            <option value="paid">Paid</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small">Search</label>
                        <input type="text" id="searchBox" class="form-control" placeholder="Invoice / Customer / Mobile">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small">Per Page</label>
                        <select id="perPage" class="form-select">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>

                    <div class="col-md-2 text-end">
                        <button class="btn btn-primary w-100" onclick="loadInvoices(1)">
                            <i class="fa-solid fa-filter me-1"></i>
                            Apply
                        </button>
                    </div>

                </div>
            </div>

            {{-- TABLE --}}
            <div class="crm-section mt-3">
                <div class="crm-table-wrapper position-relative">

                    <table class="table crm-table mb-0">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Project</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>

                        <tbody id="invoiceTable">
                            {{-- skeleton --}}
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    <td colspan="6">
                                        <div class="crm-skeleton"></div>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>

                    <div id="invoiceLoader" class="crm-loader-overlay d-none">
                        <div class="crm-spinner"></div>
                    </div>

                </div>

                {{-- PAGINATION --}}
                <div class="d-flex justify-content-end mt-2" id="invoicePagination"></div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const LIST_URL = "{{ route('invoices.ajax.list') }}";
        const WIDGET_URL = "{{ route('invoices.ajax.widgets') }}";
        const UPCOMING_PAYMENTS_URL = "{{ route('invoices.ajax.upcomingPayments') }}";
    </script>
    <script src="{{ asset('assets/js/page/invoices/index.js') }}"></script>
@endpush
