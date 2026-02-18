{{-- ================= KPI ROW ================= --}}
<div class="row g-3">

    <div class="col-md-3">
        <div class="crm-section">
            <div class="text-muted small">Total Requests</div>
            <div class="fs-4 fw-bold text-primary">{{ $total }}</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="crm-section cursor-pointer" onclick="filterByStatus('new_request')">
            <div class="text-muted small">New</div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="fs-4 fw-bold text-info">{{ $new }}</div>
                <span class="badge bg-info-subtle text-info">
                    +{{ $today }} today
                </span>
            </div>
            <div class="text-muted small mt-1">
                Needs first contact
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="crm-section cursor-pointer" onclick="filterByStatus('pending')">
            <div class="text-muted small">Pending</div>
            <div class="fs-4 fw-bold text-warning">{{ $pending }}</div>
            <div class="text-muted small mt-1">
                Follow-up required
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="crm-section cursor-pointer" onclick="filterByStatus('responded')">
            <div class="text-muted small">Responded</div>
            <div class="fs-4 fw-bold text-success">{{ $responded }}</div>
            <div class="text-muted small mt-1">
                In conversation
            </div>
        </div>
    </div>

</div>

{{-- ================= CHARTS ================= --}}
<div class="row g-3 mt-3">

    {{-- STATUS DONUT --}}
    <div class="col-md-4">
        <div class="crm-section position-relative">
            <div class="fw-semibold mb-2">
                Status Distribution
            </div>

            <canvas id="statusDonut"></canvas>

            <div class="crm-loader-overlay" id="statusDonutLoader">
                <div class="crm-spinner"></div>
            </div>
        </div>
    </div>

    {{-- TREND --}}
    <div class="col-md-8">
        <div class="crm-section position-relative">
            <div class="fw-semibold mb-2">
                Requests Trend (Last 30 Days)
            </div>

            <canvas id="requestTrend"></canvas>

            <div class="crm-loader-overlay" id="trendLoader">
                <div class="crm-spinner"></div>
            </div>
        </div>
    </div>

</div>

{{-- ================= STALE ================= --}}
@if ($stale > 0)
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="crm-section border-start border-4 border-danger
                    cursor-pointer"
                onclick="filterStale()">
                <div class="fw-semibold text-danger">
                    {{ $stale }} stale requests
                </div>
                <div class="text-muted small">
                    Not contacted in last 3 days
                </div>
            </div>
        </div>
    </div>
@endif
{{-- ================= ADVANCED ANALYTICS ================= --}}
<div class="row g-3 mt-3">

    {{-- HEATMAP --}}
    <div class="col-md-4">
        <div class="crm-section">
            <div class="fw-semibold mb-2">
                Requests by Weekday
            </div>
            <canvas id="weekdayHeatmap"></canvas>
        </div>
    </div>

    {{-- RESPONSE TIME --}}
    <div class="col-md-4">
        <div class="crm-section">
            <div class="fw-semibold mb-2">
                Avg Response Time (mins)
            </div>
            <canvas id="responseTimeChart"></canvas>
        </div>
    </div>

    {{-- SLA --}}
    <div class="col-md-4">
        <div class="crm-section border-start border-4 border-danger
                    cursor-pointer"
             id="slaWidget">
            <div class="fw-semibold text-danger">
                SLA Breach
            </div>
            <div class="fs-4 fw-bold" id="slaCount">0</div>
            <div class="text-muted small">
                Requests > 24 hours without response
            </div>
        </div>
    </div>

</div>
