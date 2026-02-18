@extends('temp.common')

@section('title', 'Tally Dashboard')
@section('head')
    <style>
        /* Balance Sheet Widget */
        #balanceSheetWidget {
            min-height: 320px;
        }

        .bs-loader {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, .85);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .bs-row {
            padding: 10px 14px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background .2s ease;
        }

        .bs-row:hover {
            background: #f8f9fa;
        }

        .bs-row-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .bs-name {
            font-weight: 600;
        }

        .bs-amount {
            font-weight: 700;
        }

        .bs-positive {
            color: #198754;
        }

        .bs-negative {
            color: #dc3545;
        }

        .bs-children {
            display: none;
            background: #fafafa;
        }

        .bs-child {
            padding: 8px 14px 8px 30px;
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
        }

        .bs-toggle {
            font-size: 0.8rem;
            margin-right: 6px;
            color: #6c757d;
        }


        /*  */

        /* Trial Balance */
        #trialBalanceWidget {
            min-height: 350px;
        }

        .tb-loader {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, .85);
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        #trialBalanceWidget tbody tr:hover {
            background: #f8f9fa;
            cursor: pointer;
        }

        /*
     */
        .crm-table td {
            padding: 5px 10px
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="crm-page">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h4 class="mb-0">
                        <i class="mdi mdi-view-dashboard me-1"></i>
                        Tally Dashboard
                    </h4>
                    <div class="text-muted small">
                        Accounting & stock overview
                    </div>
                </div>

                <div class="d-flex gap-2">
                    @can('tally.ledger')
                        <a href="{{ route('tally.ledger') }}" class="btn btn-outline-primary btn-sm">
                            Ledger
                        </a>
                    @endcan
                    @can('tally.stock')
                        <a href="{{ route('tally.stocks') }}" class="btn btn-outline-secondary btn-sm">
                            Stocks
                        </a>
                    @endcan
                </div>
            </div>

            {{-- ================= KPI STRIP ================= --}}
            <div class="row g-3 mb-3" id="tallyKpis">
                <div class="crm-loader-overlay">
                    <div class="crm-spinner"></div>
                </div>
            </div>

            {{-- ================= CASH + STOCK ================= --}}
            <div class="row g-3">
                <div class="col-md-7">
                    <div class="crm-section h-100">
                        <div class="fw-semibold mb-2">
                            Cash Flow (Monthly)
                        </div>
                        <canvas id="cashFlowChart" height="110"></canvas>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="crm-section h-100">
                        <div class="fw-semibold mb-2">
                            Stock Snapshot
                        </div>
                        <div id="stockSnapshot" class="row g-3"></div>
                    </div>
                </div>
            </div>

            <div class="crm-section mt-3 card position-relative" id="balanceSheetWidget">
                <div class="bs-loader d-none">
                    <div class="spinner-border text-primary"></div>
                    <div class="small mt-2">Loading Balance Sheet…</div>
                </div>

                <div class="d-flex justify-content-between align-items-center crm-section-header">
                    <div>
                        <h6 class="mb-0 fw-semibold">Balance Sheet</h6>
                        <div class="text-muted small">From Tally (Live)</div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" onclick="reloadBalanceSheet()">
                        <i class="mdi mdi-refresh"></i>
                    </button>
                </div>
                <hr>
                <div class="card-body p-0">
                    <div id="balanceSheetList" class="bs-list row m-0">
                        <div class="cs-list-left col-md-6" id="balanceSheetListLeft"></div>
                        <div class="cs-list-right col-md-6" id="balanceSheetListRight"></div>
                    </div>
                    <div id="balanceSheetTotal" class="bs-list row m-0">
                        <div class="cs-list-left col-md-6" id="balanceSheetTotalLeftDummy">
                            <div class="bs-row">
                                <div class="bs-row-header">
                                    <div class="bs-name">Liabilities</div>
                                    <div class="bs-amount" id="balanceSheetTotalLeft" data-counter="0">
                                        0
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cs-list-left col-md-6" id="balanceSheetTotalLeftDummy">
                            <div class="bs-row">
                                <div class="bs-row-header">
                                    <div class="bs-name">Assets</div>
                                    <div class="bs-amount" id="balanceSheetTotalRight" data-counter="0">
                                        0
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="crm-section position-relative" id="trialBalanceWidget">
                {{-- Loader --}}
                <div class="tb-loader d-none">
                    <div class="spinner-border text-primary"></div>
                    <div class="small mt-2">Loading Trial Balance…</div>
                </div>

                <div class="crm-section-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Trial Balance</h6>
                        <div class="text-muted small">Debit vs Credit</div>
                    </div>

                    <input id="tbSearch" class="form-control form-control-sm w-25" placeholder="Search account…">
                    <button class="btn btn-sm btn-outline-primary" onclick="reloadTrialBalance()">
                        <i class="mdi mdi-refresh"></i>
                    </button>
                </div>
                <hr>
                <div class="table-responsive crm-tablewrapper" style="max-height:55vh;">
                    <table class="crm-table mb-0 w-100">
                        <thead class="table-secondary sticky-top bg-body-secondary z-0">
                            <tr>
                                <th>Account</th>
                                <th class="text-end">Debit (₹)</th>
                                <th class="text-end">Credit (₹)</th>
                            </tr>
                        </thead>
                        <tbody id="trialBalanceBody"></tbody>

                        <tfoot class="table-light fw-bold sticky-bottom bg-body-secondary z-0">
                            <tr>
                                <td>Total</td>
                                <td class="text-end text-danger" id="tbTotalDebit">0</td>
                                <td class="text-end text-success" id="tbTotalCredit">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>


            {{-- ================= RISK + ACTION ================= --}}
            <div class="row g-3 mt-3">
                <div class="col-md-7">
                    <div class="crm-section border border-danger-subtle">
                        <div class="fw-semibold text-danger mb-2">
                            <i class="mdi mdi-alert-circle"></i>
                            Risk Signals
                        </div>
                        <ul class="small mb-0" id="riskList"></ul>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="crm-section">
                        <div class="fw-semibold mb-2">
                            Quick Actions
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-outline-warning btn-sm" onclick="reloadTallyCache()">
                                Refresh Cache
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('assets/js/page/tally/dashboard.js') }}"></script>
@endpush
