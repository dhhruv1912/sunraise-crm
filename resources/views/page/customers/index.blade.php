@extends('temp.common')

@section('title', 'Customers')
@section('head')
    <style>
        .crm-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid rgba(0,0,0,0.08);
}

.crm-avatar-placeholder {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
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
                    <i class="fa-solid fa-users me-2"></i>
                    Customers
                </h4>
                <div class="text-muted small">
                    All registered customers
                </div>
            </div>
        </div>

        {{-- WIDGETS --}}
        <div class="row g-3 mt-2 position-relative" id="customerWidgets" style="min-height:100px">
            <div class="crm-loader-overlay">
                <div class="crm-spinner"></div>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="crm-section mt-2">
            <div class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label class="form-label small">Search</label>
                    <input type="text"
                           id="searchBox"
                           class="form-control"
                           placeholder="Name / Mobile / Email">
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
                    <button class="btn btn-primary w-100"
                            onclick="loadCustomers(1)">
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
                            <th>Customer</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="customerTable">
                        @for($i=0;$i<5;$i++)
                            <tr>
                                <td colspan="6">
                                    <div class="crm-skeleton"></div>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>

                <div id="customerLoader" class="crm-loader-overlay d-none">
                    <div class="crm-spinner"></div>
                </div>

            </div>

            {{-- PAGINATION --}}
            <div class="d-flex justify-content-end mt-2"
                 id="customerPagination"></div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const LIST_URL   = "{{ route('customers.ajax.list') }}";
    const WIDGET_URL = "{{ route('customers.ajax.widgets') }}";
</script>
<script src="{{ asset('assets/js/page/customers/list.js') }}"></script>
@endpush
