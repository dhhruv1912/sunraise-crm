@extends('temp.common')
@section('title','Edit Invoice')

@section('content')
<div class="container-fluid">
<div class="crm-page">

    {{-- HEADER --}}
    <div class="mb-2">
        <h4 class="mb-1">
            <i class="fa-solid fa-file-invoice-dollar me-2"></i>
            Edit Invoice {{ $invoice->invoice_no }}
        </h4>
        <div class="text-muted small">
            Draft invoice
        </div>
    </div>

    <form id="invoiceForm">
        {{-- BASIC --}}
        <div class="crm-section">
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label small">Project</label>
                    <select id="project_id" class="form-select" required>
                        <option value="{{ $invoice->project_id }}">
                            {{ $invoice->project?->project_code }}
                        </option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small">Customer</label>
                    <select id="customer_id" class="form-select" required>
                        <option value="{{ $invoice->customer_id }}">
                            {{ $invoice->customer?->name }}
                        </option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small">Invoice Date</label>
                    <input type="date"
                           id="invoice_date"
                           class="form-control"
                           value="{{ $invoice->invoice_date->format('Y-m-d') }}"
                           required>
                </div>

                <div class="col-md-4">
                    <label class="form-label small">Due Date</label>
                    <input type="date"
                           id="due_date"
                           class="form-control"
                           value="{{ $invoice->due_date->format('Y-m-d') }}">
                </div>

            </div>
        </div>

        {{-- ITEMS --}}
        <div class="crm-section mt-3">
            <table class="table crm-table mb-0">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th width="120">Price</th>
                        <th width="80">Qty</th>
                        <th width="120">Tax</th>
                        <th width="140">Subtotal</th>
                        <th width="60"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody"></tbody>
            </table>

            <button type="button"
                    class="btn btn-sm btn-light mt-2"
                    id="addItemBtn"
                    onclick="addItem()">
                <i class="fa-solid fa-plus me-1"></i>
                Add Item
            </button>
        </div>

        {{-- TOTAL --}}
        <div class="crm-section mt-3">
    <div class="row justify-content-end">
        <div class="col-md-4">

            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Total Price</span>
                <strong>₹ <span id="totalPrice">0.00</span></strong>
            </div>

            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Total Tax</span>
                <strong>₹ <span id="totalTax">0.00</span></strong>
            </div>

            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Discount</span>
                <strong>
                    ₹ <input type="number"
                             id="discount"
                             class="form-control form-control-sm d-inline-block"
                             style="width:120px"
                             value="{{ $invoice->discount || 0 }}">
                </strong>
            </div>

            <hr>

            <div class="d-flex justify-content-between fs-5">
                <span>Grand Total</span>
                <strong>₹ <span id="grandTotal">0.00</span></strong>
            </div>

        </div>
    </div>
</div>

        {{-- ACTION --}}
        <div class="text-end mt-3">
            <button class="btn btn-primary">
                Update Invoice
            </button>
            <a href="{{ route('invoices.view.list') }}" class="btn btn-outline-secondary">
                Back
            </a>
        </div>

    </form>

</div>
</div>
@endsection

@push('scripts')
@php
    $invs = $invoice->items->map(fn($i) => [
            'is_quote_master' => (bool) $i->quote_master_id,
            'quote_master_id' => $i->quote_master_id,
            'description'     => $i->description,
            'unit_price'      => $i->unit_price,
            'quantity'        => $i->quantity,
            'tax'             => $i->tax,
    ]);
@endphp
<script>
const UPDATE_URL = "{{ route('invoices.ajax.update', $invoice->id) }}";
const QUOTE_MASTER_URL = "{{ route('invoices.ajax.quoteMaster', $invoice->project_id) }}";

/**
 * Items array
 * First item MUST be quote master
 */
let items = @json($invs);
</script>

<script src="{{ asset('assets/js/page/invoices/form.js') }}"></script>
@endpush
