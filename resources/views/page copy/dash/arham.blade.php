@extends('temp.common')

@section('title', 'Arham Dashboard')

@section('content')

    {{-- KPI Cards --}}
    <div class="row g-4 mb-4">

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6>Total Orders</h6>
                    <h4 class="mt-2 mb-2 text-primary">{{ @$stats['orders'] ?? 0 }}</h4>
                    <small class="text-muted">This month: {{ @$stats['orders_month'] }}</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6>In Production</h6>
                    <h4 class="mt-2 mb-2 text-warning">{{ @$stats['production'] ?? 0 }}</h4>
                    <small class="text-muted">Ongoing jobs</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6>Ready for QC</h6>
                    <h4 class="mt-2 mb-2 text-info">{{ @$stats['qc_ready'] ?? 0 }}</h4>
                    <small class="text-muted">Pending QC</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6>Deliveries</h6>
                    <h4 class="mt-2 mb-2 text-success">{{ @$stats['deliveries'] ?? 0 }}</h4>
                    <small class="text-muted">Completed</small>
                </div>
            </div>
        </div>

    </div>

    {{-- Workflow Chart --}}
    <div class="row mb-4">
        <div class="col-xl-8 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>Production Workflow</h5>
                </div>
                <div class="card-body">
                    <div id="workflowChart"></div>
                </div>
            </div>
        </div>

        {{-- Sales --}}
        <div class="col-xl-4 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Monthly Sales</h5>
                </div>
                <div class="card-body">
                    <div id="salesChart"></div>

                    <h4 class="mt-3 text-success">₹{{ @$stats['sales'] ?? '0' }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row mb-4">

        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5>Quick Actions</h5>
                </div>
                <div class="card-body d-flex gap-3 flex-wrap">
                    <a href="#" class="btn btn-primary">New Order</a>
                    <a href="#" class="btn btn-secondary">Add Customer</a>
                    <a href="#" class="btn btn-info">Check Stock</a>
                </div>
            </div>
        </div>

        {{-- Orders by Stage --}}
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h5>Orders by Stage</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Stage</th>
                                <th>Orders</th>
                                <th>%</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach (@$workflowData as $row)
                            <tr>
                                <td>
                                    <span class="badge bg-label-{{ $row['badge'] }}">
                                        {{ $row['stage'] }}
                                    </span>
                                </td>
                                <td>{{ $row['count'] }}</td>
                                <td>{{ $row['percentage'] }}%</td>
                                <td><a href="#" class="text-primary">View</a></td>
                            </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- Staff Overview --}}
    <div class="card">
        <div class="card-header">
            <h5>Staff Overview</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Assigned Orders</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @foreach ($staff as $member)
                    <tr>
                        <td>{{ $member->fname }} {{ $member->lname }}</td>
                        <td>{{ $member->getRole($member->role) }}</td>
                        <td>{{ $member->orders_assigned ?? 0 }}</td>
                        <td>
                            <span class="badge bg-label-{{ $member->status ? 'success' : 'secondary' }}">
                                {{ $member->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach --}}
                </tbody>
            </table>
        </div>
    </div>

@endsection
