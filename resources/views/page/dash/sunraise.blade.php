@extends('temp.common')

@section('title', 'Sunraise Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="crm-page">

            {{-- ================= TOP KPIs ================= --}}
            <div class="row position-relative" id="dashTop">
                <div class="crm-loader-overlay">
                    <div class="crm-spinner"></div>
                </div>
            </div>
            <div class="row g-3 ms-0">
                <div class="col-md-6 crm-section mt-3 position-relative" id="aiInsights">
                    <div class="fw-semibold mb-2">
                        <i class="fa-solid fa-brain me-1"></i>
                        Smart Insights
                    </div>

                    <div id="aiInsightList"></div>

                    <div class="crm-loader-overlay">
                        <div class="crm-spinner"></div>
                    </div>
                </div>
                <div class="col-md-6 position-relative">
                    <div class="crm-section">
                        <div class="fw-semibold mb-2">EMI Overview</div>
                        <div id="emiSummary"></div>
                    </div>
                    <div class="crm-loader-overlay">
                        <div class="crm-spinner"></div>
                    </div>
                </div>
            </div>

            {{-- ================= FINANCIAL ================= --}}

                <div class="position-relative">
                    <div class="crm-section">
                        <div class="fw-semibold mb-2">Financial Pulse</div>
                        <canvas id="invoiceChart" height="120"></canvas>
                    </div>
                    <div class="crm-loader-overlay">
                        <div class="crm-spinner"></div>
                    </div>
                </div>




            {{-- ================= PROJECT HEALTH ================= --}}
            <div class="crm-section position-relative">
                <div class="fw-semibold mb-2">Project Health</div>
                <div class="row g-3" id="projectHealth"></div>
                <div class="crm-loader-overlay">
                    <div class="crm-spinner"></div>
                </div>
            </div>

            {{-- ================= TIMELINES ================= --}}
            <div class="row g-3">

                <div class="col-md-6 position-relative">
                    <div class="crm-section">
                        <div class="fw-semibold text-danger mb-2">Overdue</div>
                        <div id="overdueList"></div>
                    </div>
                    <div class="crm-loader-overlay">
                        <div class="crm-spinner"></div>
                    </div>
                </div>

                <div class="col-md-6 position-relative">
                    <div class="crm-section">
                        <div class="fw-semibold mb-2">Upcoming (7 days)</div>
                        <div id="upcomingList"></div>
                    </div>
                    <div class="crm-loader-overlay">
                        <div class="crm-spinner"></div>
                    </div>
                </div>

            </div>

            {{-- ================= WORKLOAD ================= --}}
            <div class="crm-section position-relative">
                <div class="fw-semibold mb-2">Team Workload</div>
                <canvas id="workloadChart" height="90"></canvas>
                <div class="crm-loader-overlay">
                    <div class="crm-spinner"></div>
                </div>
            </div>

            {{-- ================= ACTIVITY ================= --}}
            <div class="crm-section position-relative">
                <div class="fw-semibold mb-2">Recent Activity</div>
                <div id="activityTimeline"></div>
                <div class="crm-loader-overlay">
                    <div class="crm-spinner"></div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('assets/js/page/dashboard/sunraise.js') }}"></script>
@endpush
