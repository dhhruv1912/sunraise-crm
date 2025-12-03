@extends('temp.common')
@section('title')
    Tally
@endsection
@section('content')
        <!-- Striped Rows -->
        <div class="card">
            <div class="card-header row">
                <h5 id="table-title" data-path="home" data-back="home" class="col">Ledger</h5>
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
                    <button id="back" onclick="reload()" class="rounded-circle btn btn-outline-primary waves-effect px-2">
                        <span class="mdi mdi-reload"></span>
                    </button>
                </div>
            </div>
            {{-- <div class="table-responsive text-nowrap m-3">
                <div class="table-responsive text-nowrap" style="min-height: 300px">
                    <table class="table table-hover table-datatable" id="tally-datatable">
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div> --}}
            <div class="table-responsive">
                    <table id="tally-datatable" class="table table-striped table-bordered">
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
@endsection
@section('headbar')
    <!-- <div class="input-group input-group-merge mx-2">
        <span class="border-0 input-group-text rounded-end rounded-pill text-primary">Start Date :</span>
        <input type="date" class="border-0 form-control rounded-pill rounded-start text-primary" id="start-date">
    </div>
    <div class="input-group input-group-merge mx-2">
        <span class="border-0 input-group-text rounded-end rounded-pill text-primary" id="">End Date :</span>
        <input type="date" class="border-0 form-control rounded-pill rounded-start text-primary" id="end-date">
    </div> -->
@endsection
@section('head')
    {{-- <link rel="stylesheet" href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css">
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script> --}}
    <script src="{{ asset('assets/js/page/ledger.js') }}"></script>
@endsection
{{-- @section('scripts')
<script>

</script>
@endsection --}}
