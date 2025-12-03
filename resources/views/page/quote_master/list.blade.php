@extends('temp.common')

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Quote Master</h4>

        <div class="d-flex align-items-center">
            <a href="{{ route('quote_master.create') }}" class="btn btn-primary me-2">Add New</a>
            <a href="{{ route('quote_master.export') }}" class="btn btn-success me-2">Export</a>

            <form action="{{ route('quote_master.import') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center me-2">
                @csrf
                <input type="file" name="file" required class="form-control form-control-sm me-2" style="width:220px;">
                <button type="submit" class="btn btn-warning btn-sm">Import</button>
            </form>
        </div>
    </div>

    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" id="searchBox" class="form-control" placeholder="Search SKU, module, kw">
            </div>

            <div class="col-md-6 d-flex justify-content-end">
                <label class="me-2 align-self-center">Per Page</label>
                <select id="perPage" class="form-select form-select-sm" style="width:100px;">
                    <option value="10">10</option>
                    <option value="20" selected>20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th style="width:140px">SKU</th>
                        <th>Module</th>
                        <th style="width:90px">KW</th>
                        <th style="width:90px">Count</th>
                        <th style="width:120px">Value</th>
                        <th style="width:120px">Payable</th>
                        <th style="width:120px">Projected</th>
                        <th style="width:260px">Actions</th>
                    </tr>
                </thead>
                <tbody id="dataBody"></tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div id="tableInfo"></div>
            <div id="pagination"></div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/page/quote_master.js') }}"></script>
@endsection
