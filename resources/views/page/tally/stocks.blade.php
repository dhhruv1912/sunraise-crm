@extends('temp.common')

@section('title', 'Tally · Stocks')
@section('head')
    <style>
        /* ================= TALLY TABLE ENHANCEMENTS ================= */
        #tally-datatable thead th {
            position: sticky;
            z-index: 5;
            background: var(--bs-secondary-bg, #e9ecef);
        }

        /* First header row */
        #tally-datatable thead tr:nth-child(1) th {
            top: 0;
        }

        /* Second header row (voucher view) */
        #tally-datatable thead tr:nth-child(2) th {
            top: 38px;
        }

        /* Disable unwanted datatable UI if injected */
        .dt-layout-start,
        .dt-layout-end,
        .dt-search {
            display: none !important;
        }

        .dt-layout-row {
            margin: 0 !important;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="crm-page">

            {{-- ================= HEADER ================= --}}
            <div class="d-flex justify-content-between align-items-start mb-2">

                <div>
                    <h4 id="table-title" data-path="home" data-back="home" class="mb-0">
                        Stock
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
            {{-- ================= STOCK MOVEMENT SUMMARY ================= --}}
            <div id="stockMovementWidget" class="crm-section mt-2 d-none">

                <div class="row g-3">

                    {{-- Opening --}}
                    <div class="col-md-2">
                        <div class="crm-stat">
                            <div class="text-muted small">Opening</div>
                            <div class="fw-bold" id="smOpeningQty">—</div>
                            <div class="small text-muted" id="smOpeningVal"></div>
                        </div>
                    </div>

                    {{-- Inward --}}
                    <div class="col-md-2">
                        <div class="crm-stat">
                            <div class="text-muted small">Inward</div>
                            <div class="fw-bold text-success" id="smInQty">—</div>
                            <div class="small text-muted" id="smInVal"></div>
                        </div>
                    </div>

                    {{-- Outward --}}
                    <div class="col-md-2">
                        <div class="crm-stat">
                            <div class="text-muted small">Outward</div>
                            <div class="fw-bold text-danger" id="smOutQty">—</div>
                            <div class="small text-muted" id="smOutVal"></div>
                        </div>
                    </div>

                    {{-- Closing --}}
                    <div class="col-md-2">
                        <div class="crm-stat">
                            <div class="text-muted small">Closing</div>
                            <div class="fw-bold" id="smClosingQty">—</div>
                            <div class="small text-muted" id="smClosingVal"></div>
                        </div>
                    </div>

                    {{-- Net Movement --}}
                    <div class="col-md-2">
                        <div class="crm-stat">
                            <div class="text-muted small">Net Movement</div>
                            <div class="fw-bold" id="smNetQty">—</div>
                            <div class="small text-muted" id="smNetVal"></div>
                        </div>
                    </div>

                    {{-- Velocity --}}
                    <div class="col-md-2">
                        <div class="crm-stat">
                            <div class="text-muted small">Velocity</div>
                            <div class="fw-bold" id="smVelocity">—</div>
                            <div class="progress mt-1" style="height:6px;">
                                <div class="progress-bar" id="smVelocityBar"></div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            {{-- ================= TABLE SECTION ================= --}}
            <div class="crm-section p-0 mt-2">

                <div class="crm-table-wrapper position-relative">

                    <table id="tally-datatable" class="table crm-table table-striped table-hover mb-0">
                        <thead></thead>
                        <tbody></tbody>
                    </table>

                    {{-- LOADER --}}
                    <div id="table-loader" class="crm-loader-overlay d-none">
                        <div class="crm-spinner"></div>
                    </div>

                </div>

            </div>

            {{-- ================= FOOTER INFO ================= --}}
            <div class="text-muted small mt-2">
                <i class="mdi mdi-information-outline me-1"></i>
                Double-click rows to drill down · Press <b>ESC</b> to go back
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/page/stocks.js') }}"></script>
@endsection
