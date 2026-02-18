@extends('temp.common')

@section('title', 'Tally · Ledger')
@section('content')
    <div class="container-fluid">
        <div class="crm-page">

            {{-- ================= HEADER ================= --}}
            <div class="d-flex justify-content-between align-items-start">

                <div>
                    <h4 id="table-title" data-path="home" data-back="home" class="mb-0">
                        Ledger
                    </h4>

                    <nav aria-label="breadcrumb" class="mt-1" id="tally-breadcrumb">
                        <ol class="breadcrumb breadcrumb-style1 mb-0">
                            {{-- JS injects breadcrumb --}}
                        </ol>
                    </nav>
                </div>

                {{-- ACTIONS --}}
                <div class="d-flex gap-2" id="tally_navigation">
                    <button id="home" onclick="goHome()" class="btn btn-sm btn-outline-primary rounded-circle"
                        title="Home">
                        <i class="mdi mdi-home"></i>
                    </button>

                    <button id="back" onclick="goBack()" class="btn btn-sm btn-outline-primary rounded-circle"
                        title="Back">
                        <i class="mdi mdi-arrow-left"></i>
                    </button>

                    <button onclick="reload()" class="btn btn-sm btn-outline-secondary rounded-circle" title="Reload">
                        <i class="mdi mdi-reload"></i>
                    </button>
                </div>

            </div>

            <div id="voucherAnalytics" class="crm-section mt-2 d-none">

                {{-- KPIs --}}
                <div class="row g-3 mb-2">
                    <div class="col-md-3">
                        <div class="crm-stat">
                            <div class="text-muted small">Total Debit</div>
                            <div class="fs-5 fw-bold text-danger" id="vaTotalDebit">—</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="crm-stat">
                            <div class="text-muted small">Total Credit</div>
                            <div class="fs-5 fw-bold text-success" id="vaTotalCredit">—</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="crm-stat">
                            <div class="text-muted small">Net Flow</div>
                            <div class="fs-5 fw-bold" id="vaNetFlow">—</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="crm-stat">
                            <div class="text-muted small">Voucher Count</div>
                            <div class="fs-5 fw-bold" id="vaCount">—</div>
                        </div>
                    </div>
                </div>

                {{-- Charts --}}
                <div class="row g-3">
                    <div class="col-md-4">
                        <canvas id="vaDebitCreditChart" height="220" style="max-height: 220px"></canvas>
                    </div>
                    <div class="col-md-4">
                        <canvas id="vaTypeChart" height="220" style="max-height: 220px"></canvas>
                    </div>
                    <div class="col-md-4">
                        <canvas id="vaDailyChart" height="220" style="max-height: 220px"></canvas>
                    </div>
                </div>

            </div>
            {{-- ================= INFO FOOTER ================= --}}
            <div class="text-muted small mt-2">
                <i class="mdi mdi-information-outline me-1"></i>
                Double-click rows to drill down · Press <b>ESC</b> to go back
            </div>
            {{-- ================= TABLE SECTION ================= --}}
            <div class="crm-section mt-2 p-0">

                <div class="crm-table-wrapper position-relative">

                    <table id="tally-datatable" class="table crm-table table-striped mb-0">
                        <thead></thead>
                        <tbody></tbody>
                    </table>

                    {{-- LOADER --}}
                    <div id="table-loader" class="crm-loader-overlay d-none">
                        <div class="crm-spinner"></div>
                    </div>

                </div>

            </div>


        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/page/ledger.js') }}"></script>
@endsection
