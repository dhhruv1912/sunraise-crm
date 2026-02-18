@extends('temp.common')
@section('title', 'Vendors')
@section('content')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Vendors</h4>
            <div>
                <button id="addVendorBtn" class="btn btn-primary">+ New</button>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex mb-3 gap-2">
                <input type="text" style="max-width:250px" id="searchVendor" class="form-control"
                    placeholder="Search vendors...">
                <select id="perPage" class="form-select" style="width:140px;">
                    <option value="10">10</option>
                    <option value="20" selected>20</option>
                    <option value="50">50</option>
                </select>
            </div>
            <div class="table-responsive py-3">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Company Name</th>
                            <th>Pan</th>
                            <th>GST</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="vendorList">
                    </tbody>
                </table>
                <div class="p-3" id="vendorPaginate"></div>
                {{-- <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" id="searchVendor" class="form-control" placeholder="Search vendors...">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary" id="addVendorBtn">Add Vendor</button>
                </div>
            </div> --}}
            </div>
        </div>
    </div>

    {{-- <div id="vendorList"></div> --}}

    @include('page.vendors.modal')
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/page/vendors.js') }}"></script>
@endsection
