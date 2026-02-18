{{-- LEFT: STACKED KPI WIDGETS --}}
<div class="d-flex flex-column justify-content-between col-md-3 gap-3">

    <div class="crm-section">
        <div class="text-muted small">Total Packages</div>
        <div class="fs-4 fw-bold text-primary">
            {{ $total }}
        </div>
    </div>

    <div class="crm-section">
        <div class="text-muted small">Average Capacity (kW)</div>
        <div class="fs-4 fw-bold text-info">
            {{ $avgKw }}
        </div>
    </div>

    <div class="crm-section">
        <div class="text-muted small">Average Value</div>
        <div class="fs-4 fw-bold text-success">
            ₹ {{ number_format($avgValue) }}
        </div>
    </div>

</div>

{{-- RIGHT: MAIN CHART --}}
<div class="col-md-9">
    <div class="crm-section position-relative h-100">

        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-semibold">
                <i class="fa-solid fa-chart-line me-1 text-primary"></i>
                kW vs Payable Price
            </div>
        </div>

        <div style="height:260px">
            <canvas id="kwPriceChart"></canvas>
        </div>

        <div class="crm-loader-overlay" id="kwPriceChartLoader">
            <div class="crm-spinner"></div>
        </div>

    </div>
</div>
