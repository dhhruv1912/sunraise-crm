@extends('temp.common')

@section('title','Tally Monthly Reports')

@section('content')
<div class="crm-page">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Tally Monthly Reports</h4>
            <div class="text-muted small">
                Ledger / Voucher based analytics
            </div>
        </div>

        <button class="btn btn-outline-primary" onclick="reloadData()">
            <i class="mdi mdi-reload"></i> Refresh
        </button>
    </div>

    {{-- FILTERS --}}
    <div class="crm-section">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small">Ledger</label>
                <select id="ledgerSelect" class="form-select"></select>
            </div>

            <div class="col-md-4">
                <label class="form-label small">Year</label>
                <select id="yearSelect" class="form-select">
                    <option value="">Select Year</option>
                    <option value="22">2022</option>
                    <option value="23">2023</option>
                    <option value="24">2024</option>
                    <option value="25">2025</option>
                    <option value="26">2026</option>
                </select>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-primary w-100" onclick="generateReport()">
                    Generate Report
                </button>
            </div>
        </div>
    </div>

    {{-- KPI WIDGETS --}}
    <div class="crm-section position-relative" style="min-height: 100px">
        <div class="row g-3" id="monthlyWidgets"></div>
        <div class="crm-loader-overlay d-none" id="monthlyWidgetsLoader">
            <div class="crm-spinner"></div>
        </div>
    </div>

    {{-- CHART --}}
    <div class="crm-section position-relative" style="min-height: 100px">
        <canvas id="monthlyChart" height="90"></canvas>
        <div class="crm-loader-overlay d-none" id="monthlyChartLoader">
            <div class="crm-spinner"></div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="crm-section position-relative" style="min-height: 100px">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th class="text-end">Account</th>
                        <th class="text-end">Type</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Credit</th>
                    </tr>
                </thead>
                <tbody id="monthlyTable"></tbody>
            </table>
        </div>
        <div class="crm-loader-overlay d-none" id="monthlyTableLoader">
            <div class="crm-spinner"></div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('assets/js/page/tally/monthly.js') }}"></script>
@endpush
