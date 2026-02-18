@extends('temp.common')
@section('title')
    Tally
@endsection
@section('content')
<style>
    .loader-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.5); /* Half opacity */
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }

    .spinner {
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-left-color: #3498db;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
    #tally-datatable thead th {
        position: sticky;
        top: 0;
    }
    .dt-layout-start, .dt-layout-end, .dt-search{
        display: none !important;
    }
    .dt-layout-row{
        margin: 0 !important;
    }

    #tally-datatable thead th {
    position: sticky;
    z-index: 10;
    background: #e9ecef; /* Bootstrap's .table-secondary */
}

/* First header row (Date / Type / etc.) */
#tally-datatable thead tr:nth-child(1) th {
    top: 0;
}

/* Second header row (QTY / Amount below main headers) */
#tally-datatable thead tr:nth-child(2) th {
    top: 37px; /* Adjust depending on your header row height */
}
</style>
        <!-- Striped Rows -->
        <div class="card">
            <div class="card-header row">
                <h5 id="table-title" data-path="home" data-back="home" class="col">Stock</h5>
                <nav aria-label="breadcrumb" id="tally-breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1">
                    </ol>
                  </nav>
                <div id="tally_navigation">
                    <button id="home" onclick="goHome()" class="rounded-circle btn btn-outline-primary waves-effect px-2">
                        <span class="mdi mdi-home"></span>
                    </button>
                    <button id="back" onclick="goBack()" class="rounded-circle btn btn-outline-primary waves-effect px-2">
                        <span class="mdi mdi-arrow-left"></span>
                    </button>
                </div>
            </div>
            <div class="table-responsive text-nowrap m-3">
                <div class="table-responsive text-nowrap" style="min-height: 300px;max-height: 55vh;">
                    <table class="table table-striped table-hover table-datatable position-relative" id="tally-datatable">
                        <thead></thead>
                        <tbody></tbody>
                        <div id="table-loader" class="loader-overlay" style="display:none;">
                            <div class="spinner">
                            </div>
                            Loading...
                        </div>
                    </table>
                </div>
            </div>
        </div>
@endsection
@section('headbar')
    {{-- <div class="input-group input-group-merge mx-2" style="max-width: 200px">
        <span class="border-0 input-group-text rounded-end rounded-pill text-primary">Start Date :</span>
        <input type="date" class="border-0 form-control rounded-pill rounded-start text-primary" id="start-date">
    </div>
    <div class="input-group input-group-merge mx-2" style="max-width: 200px">
        <span class="border-0 input-group-text rounded-end rounded-pill text-primary" id="">End Date :</span>
        <input type="date" class="border-0 form-control rounded-pill rounded-start text-primary" id="end-date">
    </div>
    <button id="home" onclick="goHome()" class="rounded-circle btn btn-outline-primary waves-effect px-2">
                        <span class="mdi mdi-home"></span>
                    </button>
                    <button id="back" onclick="goBack()" class="rounded-circle btn btn-outline-primary waves-effect px-2">
                        <span class="mdi mdi-arrow-left"></span>
                    </button> --}}
    {{-- <button id="back" onclick="reload()" class="rounded-circle btn btn-outline-primary waves-effect px-2 py-1" style="max-width: 200px">
        <span class="mdi mdi-arrow-left"></span>
    </button> --}}
@endsection
@section('head')

    <script src="{{ asset('assets/js/page/stocks.js') }}"></script>
@endsection
{{-- @section('scripts')
<script>

</script>
@endsection --}}
