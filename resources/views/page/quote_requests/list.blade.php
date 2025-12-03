@extends('temp.common')

@section('title','Quote Requests')

@section('content')
<style>
    .highlight-row {
    background-color: #d4edda !important;     /* light green */
    transition: background-color 1s ease;
}
.loading-dropdown {
    opacity: 0.6;
}
.timeline-entry {
    padding: 10px 0;
    border-left: 3px solid #0d6efd;
    margin-left: 10px;
    padding-left: 12px;
}
.timeline-entry small {
    color: #888;
}
</style>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Quote Requests</h4>

            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('quote_requests.create') }}" class="btn btn-primary">Add New</a>

                <a href="{{ route('quote_requests.export') }}" class="btn btn-success">Export</a>

                <form action="{{ route('quote_requests.import') }}" method="POST" enctype="multipart/form-data" class="d-inline-block">
                    @csrf
                    <input type="file" name="file" required style="display:inline-block">
                    <button type="submit" class="btn btn-warning">Import</button>
                </form>
            </div>
        </div>

        <div class="card-body">
            {{-- Filters (above table) --}}
            <div class="row mb-3 g-2">
                <div class="col-sm-3">
                    <input id="searchBox" class="form-control" placeholder="Search name, mobile, email, module">
                </div>

                <div class="col-sm-2">
                    <select id="filter_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="quote">Quote</option>
                        <option value="call">Call</option>
                    </select>
                </div>

                <div class="col-sm-2">
                    <select id="filter_status" class="form-select">
                        <option value="">All Status</option>
                        @foreach($statuses as $k=>$v)
                            <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-2">
                    <select id="filter_assigned" class="form-select">
                        <option value="">Assigned to</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->fname }} {{ $u->lname }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-3 d-flex gap-2">
                    <input id="filter_from" class="form-control" type="date" placeholder="From">
                    <input id="filter_to" class="form-control" type="date" placeholder="To">
                </div>
            </div>

            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div>
                    <label>Per page</label>
                    <select id="perPage" class="form-select d-inline-block" style="width:auto">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <div>
                    <button id="refreshBtn" class="btn btn-outline-secondary">Refresh</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Module / KW</th>
                            <th>Assigned</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="dataBody">
                        {{-- filled by JS --}}
                    </tbody>
                </table>
            </div>

            <nav id="paginationContainer" aria-label="Page navigation"></nav>
        </div>
    </div>

@include('page.quote_requests.view') {{-- modal view --}}
@endsection

@section('scripts')
<script>
    // ensure TOKEN global exists (set in your main layout)
    const TOKEN = "{{ csrf_token() }}";
    window.__QR_STATUS = {!! json_encode(\App\Http\Controllers\QuoteRequestController::$STATUS) !!};
</script>
<script src="{{ asset('assets/js/page/quote_requests.js') }}"></script>
@endsection
