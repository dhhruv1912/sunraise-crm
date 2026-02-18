@extends('temp.common')
@section('title','Create Invoice')

@section('content')
<div class="container-fluid">
<div class="crm-page">

    {{-- HEADER --}}
    <div class="mb-2">
        <h4 class="mb-1">
            <i class="fa-solid fa-file-invoice-dollar me-2"></i>
            Create Invoice
        </h4>
        <div class="text-muted small">
            Draft invoice creation
        </div>
    </div>

    <form id="invoiceForm">

        {{-- BASIC --}}
        <div class="crm-section">
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label small">Project</label>
                    <select id="project_id" class="form-select" required>
                        <option value="">Select Project</option>
                        {{-- loaded via ajax --}}
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small">Customer</label>
                    <select id="customer_id" class="form-select" required disabled>
                        <option value="">Auto from project</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small">Invoice Date</label>
                    <input type="date"
                           id="invoice_date"
                           class="form-control"
                           value="{{ now()->toDateString() }}"
                           required>
                </div>

                <div class="col-md-4">
                    <label class="form-label small">Due Date</label>
                    <input type="date"
                           id="due_date"
                           class="form-control">
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
                        <th width="60"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    {{-- Quote master row injected --}}
                </tbody>
            </table>

            <button type="button"
                    class="btn btn-sm btn-light mt-2"
                    onclick="addItem()"
                    id="addItemBtn"
                    disabled>
                <i class="fa-solid fa-plus me-1"></i>
                Add Item
            </button>
        </div>

        {{-- TOTAL --}}
        <div class="crm-section mt-3">
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <label class="form-label small">Discount</label>
                    <input type="number"
                           id="discount"
                           class="form-control"
                           value="0">
                    <div class="mt-2">
                        <strong>Total: ₹ <span id="grandTotal">0.00</span></strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- ACTION --}}
        <div class="text-end mt-3">
            <button class="btn btn-primary">
                Save Draft
            </button>
        </div>

    </form>

</div>
</div>
@endsection

@push('scripts')
<script>
const CREATE_URL = "{{ route('invoices.ajax.store') }}";
const PROJECTS_URL = "{{ route('projects.ajax.list') }}"; 
// ↑ must return only projects WITHOUT invoices
// response: [{id, project_code, customer_id, customer_name}]

/**
 * Items array
 * First item MUST be quote master
 */
let items = [];
</script>

<script src="{{ asset('assets/js/page/invoices/form.js') }}"></script>
@endpush
