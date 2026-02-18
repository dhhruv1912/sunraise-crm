@extends('temp.common')

@section('title', 'Warehouses')

@section('content')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Warehouses</h4>
            <div>
                <button id="addWarehouseBtn" class="btn btn-primary">+ New</button>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex mb-3 gap-2">
                <input type="text" style="max-width:250px" id="searchWarehouse" class="form-control"
                    placeholder="Search Warehouse...">
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
                            <th>Code</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="warehouseList">
                    </tbody>
                </table>
                <div class="p-3" id="warehousePaginate"></div>
            </div>
        </div>
        {{-- <div class="row mb-3">
            <div class="col-md-3">
                <input type="text" id="searchWarehouse" class="form-control" placeholder="Search...">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary" id="addWarehouseBtn">Add Location</button>
            </div>
        </div> --}}
    </div>

    {{-- <div id="warehouseList"></div> --}}

    @include('page.warehouse.warehouse-modal')

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/page/warehouse.js') }}"></script>
@endsection
