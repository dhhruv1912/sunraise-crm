@extends('temp.common')

@section('title', 'Sunraise Dashboard')

@section('content')

    {{-- Top KPI Cards --}}
    <div class="row g-4 mb-4">

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6>Total Leads</h6>
                    <h4 class="mt-2 mb-2 text-primary">{{ $stats['leads'] ?? 0 }}</h4>
                    <small class="text-muted">New this month: {{ $stats['leads_month'] ?? 0 }}</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6>Active Quotes</h6>
                    <h4 class="mt-2 mb-2 text-warning">{{ $stats['quotes'] ?? 0 }}</h4>
                    <small class="text-muted">Awaiting approval</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6>Installation Scheduled</h6>
                    <h4 class="mt-2 mb-2 text-info">{{ $stats['installation'] ?? 0 }}</h4>
                    <small class="text-muted">Next 7 days</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6>Completed Projects</h6>
                    <h4 class="mt-2 mb-2 text-success">{{ $stats['completed'] ?? 0 }}</h4>
                    <small class="text-muted">Lifetime total</small>
                </div>
            </div>
        </div>

    </div>

    {{-- Pipeline Overview Chart --}}
    <div class="row mb-4">
        <div class="col-xl-8 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>Project Pipeline</h5>
                    <button class="btn p-0" data-bs-toggle="dropdown">
                        <i class="mdi mdi-dots-vertical mdi-24px"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div id="pipelineChart"></div>
                </div>
            </div>
        </div>

        {{-- Revenue + Subsidy --}}
        <div class="col-xl-4 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Revenue Overview</h5>
                </div>
                <div class="card-body">
                    <div id="revenueChart"></div>

                    <div class="mt-3">
                        <h4 class="text-success">₹{{ $stats['revenue'] ?? '0' }}</h4>
                        <small class="text-muted">Govt. Subsidy: ₹{{ $stats['subsidy'] ?? '0' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Lead Sources + Quick Actions --}}
    <div class="row mb-4">

        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Lead Sources</h5>
                </div>
                <div class="card-body">
                    <div id="leadSourceChart"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5>Quick Actions</h5>
                </div>
                <div class="card-body d-flex gap-3 flex-wrap">

                    <a href="/" class="btn btn-primary">New Quote</a> {{-- {{ route('QuoteRequests') }} --}}
                    <a href="/" class="btn btn-secondary">Add Lead</a> {{-- {{ route('MarketingLead') }} --}}
                    <a href="/" class="btn btn-info">New Project</a> {{-- {{ route('ProjectLead') }} --}}

                </div>
            </div>
        </div>

    </div>

    {{-- Project Status Table --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Status</th>
                        <th>Projects</th>
                        <th>Percentage</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @foreach ($projectData as $item)
                    <tr>
                        <td>
                            <span class="badge bg-label-{{ $item['badge'] }}">
                                {{ $item['status'] }}
                            </span>
                        </td>
                        <td>{{ $item['count'] }}</td>
                        <td>{{ $item['percentage'] }}%</td>
                        <td>
                            <a href="{{ route('ProjectLead') }}" class="text-primary">
                                View Details <i class="mdi mdi-arrow-right"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach --}}
                </tbody>
            </table>
        </div>
    </div>

@endsection
