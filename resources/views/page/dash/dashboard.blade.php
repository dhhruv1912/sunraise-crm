@extends('temp.common')

@section('title', 'Dashboard')

@section('content')

    <div class="row gy-4">

        {{-- Welcome Card --}}
        <div class="col-md-12 col-lg-4">
            <div class="card position-relative overflow-hidden">
                <div class="card-body">
                    <h4 class="card-title mb-1">Welcome, {{ session('staff.fname') }} 🎉</h4>
                    <p class="pb-0">Your quick performance overview</p>

                    {{-- Example KPI --}}
                    <h4 class="text-primary mb-1">$42.8k</h4>
                    <p class="mb-2 pb-1">78% of target</p>

                    <a href="javascript:;" class="btn btn-sm btn-primary">View Analytics</a>
                </div>

                {{-- Background Illustrations --}}
                <img src="{{ asset('assets/img/icons/misc/triangle-light.png') }}"
                     class="scaleX-n1-rtl position-absolute bottom-0 end-0" width="160" alt="">
                <img src="{{ asset('assets/img/illustrations/trophy.png') }}"
                     class="scaleX-n1-rtl position-absolute bottom-0 end-0 me-4 mb-4 pb-2"
                     width="80" alt="">
            </div>
        </div>

        {{-- Weekly Overview Chart --}}
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-1">Weekly Overview</h5>

                    <button class="btn p-0" data-bs-toggle="dropdown">
                        <i class="mdi mdi-dots-vertical mdi-24px"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">Refresh</a></li>
                        <li><a class="dropdown-item" href="#">Share</a></li>
                        <li><a class="dropdown-item" href="#">Update</a></li>
                    </ul>
                </div>

                <div class="card-body">
                    <div id="weeklyOverviewChart"></div>

                    <div class="mt-3 d-flex align-items-center gap-3">
                        <h3 class="mb-0">45%</h3>
                        <p class="mb-0">Better performance compared to last month.</p>
                    </div>

                    <div class="d-grid mt-4">
                        <button class="btn btn-primary" type="button">Details</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Insights Cards --}}
        <div class="col-xl-4 col-md-6">
            <div class="row gy-4">

                {{-- Total Profit Chart --}}
                <div class="col-sm-6">
                    <div class="card h-100">
                        <div class="card-header pb-0">
                            <h4 class="mb-0">$86.4k</h4>
                        </div>
                        <div class="card-body">
                            <div id="totalProfitLineChart" class="mb-3"></div>
                            <h6 class="text-center mb-0">Total Profit</h6>
                        </div>
                    </div>
                </div>

                {{-- Total Profit Weekly --}}
                <div class="col-sm-6">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="avatar">
                                <div class="avatar-initial bg-secondary rounded-circle shadow">
                                    <i class="mdi mdi-poll mdi-24px"></i>
                                </div>
                            </div>

                            <button class="btn p-0" data-bs-toggle="dropdown">
                                <i class="mdi mdi-dots-vertical mdi-24px"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Refresh</a></li>
                                <li><a class="dropdown-item" href="#">Share</a></li>
                                <li><a class="dropdown-item" href="#">Update</a></li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <h6 class="mb-2">Total Profit</h6>
                            <div class="d-flex align-items-center mb-2">
                                <h4 class="mb-0 me-2">$25.6k</h4>
                                <small class="text-success mt-1">+42%</small>
                            </div>
                            <small>Weekly Project</small>
                        </div>
                    </div>
                </div>

                {{-- Yearly Project --}}
                <div class="col-sm-6">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="avatar">
                                <div class="avatar-initial bg-primary rounded-circle shadow">
                                    <i class="mdi mdi-wallet-travel mdi-24px"></i>
                                </div>
                            </div>

                            <button class="btn p-0" data-bs-toggle="dropdown">
                                <i class="mdi mdi-dots-vertical mdi-24px"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Refresh</a></li>
                                <li><a class="dropdown-item" href="#">Share</a></li>
                                <li><a class="dropdown-item" href="#">Update</a></li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <h6 class="mb-2">New Projects</h6>
                            <div class="d-flex align-items-center mb-2">
                                <h4 class="mb-0 me-2">862</h4>
                                <small class="text-danger mt-1">-18%</small>
                            </div>
                            <small>Yearly Project</small>
                        </div>
                    </div>
                </div>

                {{-- Sessions Chart --}}
                <div class="col-sm-6">
                    <div class="card h-100">
                        <div class="card-header pb-0">
                            <h4 class="mb-0">2,856</h4>
                        </div>
                        <div class="card-body">
                            <div id="sessionsColumnChart" class="mb-3"></div>
                            <h6 class="text-center mb-0">Sessions</h6>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Project Status Table --}}
        <div class="col-12">
            <div class="card">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Status</th>
                                <th>Projects</th>
                                <th>Percentage</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projectData as $status => $pd)
                                <tr>
                                    <td>
                                        <button
                                            class="change-status-1 badge border-0 bg-label-{{ $pd['badge'] }} rounded-pill"
                                            data-bs-toggle="dropdown">
                                            {{ $pd['status'] }}
                                        </button>
                                    </td>
                                    <td>{{ $pd['count'] }}</td>
                                    <td>{{ $pd['percentage'] }}%</td>
                                    <td>
                                        <a href="">View <i class="mdi mdi-arrow-right"></i></a>{{-- {{ route('ProjectLead') }} --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Staff Table --}}
        <div class="col-12">
            <div class="card">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Salary</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($staff as $s)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <img src="{{ asset('assets/img/avatars/1.png') }}"
                                                     class="rounded-circle" alt="">
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $s->fname }} {{ $s->lname }}</h6>
                                                <small>@amiccoo</small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>{{ $s->email }}</td>

                                    <td>
                                        <i class="mdi mdi-laptop mdi-20px text-danger me-1"></i>
                                        {{ $s->getRole($s->role) }}
                                    </td>

                                    <td>{{ $s->salary }}</td>

                                    <td>
                                        <span class="badge bg-label-{{ $s->status ? 'success' : 'secondary' }} rounded-pill">
                                            {{ $s->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
