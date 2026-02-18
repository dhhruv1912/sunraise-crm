@extends('temp.common')

@section('title','Stock Monthly Reports')

@section('content')
<div class="crm-page">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Stock Monthly Report</h4>
            <div class="text-muted small">
                Item-wise monthly movement & valuation
            </div>
        </div>

        <button class="btn btn-outline-primary" onclick="reloadData()">
            <i class="mdi mdi-reload"></i> Refresh
        </button>
    </div>

    {{-- FILTER --}}
    <div class="crm-section">
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label small">Stock Item</label>
                <select id="stockSelect" class="form-select"></select>
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

            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary w-100" onclick="generateReport()">
                    Generate
                </button>
            </div>
        </div>
    </div>

    {{-- WIDGETS --}}
    <div class="crm-section position-relative" style="min-height: 100px">
        <div class="row g-3" id="stockWidgets"></div>

        <div class="crm-loader-overlay d-none" id="stockWidgetsLoader">
            <div class="crm-spinner"></div>
        </div>
    </div>

    {{-- CHART --}}
    <div class="crm-section position-relative" style="min-height: 100px">
        <canvas id="stockChart" height="90"></canvas>
        <div class="crm-loader-overlay d-none" id="stockChartLoader">
            <div class="crm-spinner"></div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="crm-section position-relative" style="min-height: 100px">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th class="text-end">In Qty</th>
                        <th class="text-end">Out Qty</th>
                        <th class="text-end">In Value</th>
                        <th class="text-end">Out Value</th>
                        <th class="text-end">Closing Qty</th>
                    </tr>
                </thead>
                <tbody id="stockMonthlyTable"></tbody>
            </table>
        </div>
        <div class="crm-loader-overlay d-none" id="stockMonthlyTableLoader">
            <div class="crm-spinner"></div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('assets/js/page/tally/stock-monthly.js') }}"></script>
@endpush
