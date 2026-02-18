@extends('temp.common')

@section('title', 'Reports')

@section('content')
<div class="container-fluid">
    <div class="crm-page">

        {{-- HEADER --}}
        <div class="mb-2">
            <h4 class="mb-0">
                <i class="fa-solid fa-chart-line me-2"></i>
                Reports
            </h4>
            <div class="text-muted small">
                Execution, delays, cashflow & workload insights
            </div>
        </div>

        {{-- TABS --}}
        <div class="crm-section">
            <ul class="nav nav-pills gap-2">
                <li class="nav-item">
                    <button class="nav-link active" onclick="loadReport('execution')">
                        Execution
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" onclick="loadReport('delays')">
                        Delays
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" onclick="loadReport('cashflow')">
                        Cashflow
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" onclick="loadReport('workload')">
                        Workload
                    </button>
                </li>
            </ul>
        </div>

        {{-- CONTENT --}}
        <div id="reportContainer" class="position-relative mt-2">
            <div class="crm-loader-overlay">
                <div class="crm-spinner"></div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const REPORT_URLS = {
        execution: "{{ route('reports.ajax.execution') }}",
        delays: "{{ route('reports.ajax.delays') }}",
        cashflow: "{{ route('reports.ajax.cashflow') }}",
        workload: "{{ route('reports.ajax.workload') }}",
    };
</script>
<script src="{{ asset('assets/js/page/reports/index.js') }}"></script>
@endpush
