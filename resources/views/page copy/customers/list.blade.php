@extends('temp.common')

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h4>Customers</h4>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">Add Customer</a>
    </div>

    <div class="card-body">
        <div class="d-flex mb-3">
            <input id="searchBox" class="form-control me-2" placeholder="Search name, email, mobile">
            <select id="perPage" class="form-select" style="width:140px;">
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="50">50</option>
            </select>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th width="300">Actions</th>
                </tr>
            </thead>
            <tbody id="dataBody"></tbody>
        </table>

        <div id="pagination"></div>
    </div>
</div>

@include('page.customers.modal')

@endsection

@section('scripts')
<script src="{{ asset('assets/js/page/customers.js') }}"></script>
@endsection
