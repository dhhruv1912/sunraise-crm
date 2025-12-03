@extends('temp.common')
@section('title')
    Logs
@endsection
@section('headbar')
    <select name="staff" id="AttendanceStaff" class="col border-primary form-select-sm me-2 text-primary waves-effect" style="max-width: 250px">
        <option value="">Select Staff</option>
        @foreach ($users as $user)
            <option value="{{ $user->id }}">{{ $user->fname }} {{ $user->lname }}</option>
        @endforeach
    </select>
    <input type="month" class="col border-primary form-control me-2 text-primary" id="month" style="max-width: 250px">
    <button id="GenerateReport" onclick="GenerateAttandenceReport()"
        class="col btn btn-outline-primary float-end waves-effect"  style="max-width: 250px">Report</button>
@endsection
@section('content')
    <input type="hidden" class="recall_attandance" name="staff" id="staff" value="">
    <!-- Striped Rows -->
    <div class="card">
        <div class="card-header row">
            <h5 class="col">Logs</h5>
            <div class="col">
                <div class="d-flex">
                    {{-- <li class="nav-item dropdown" style="max-width: 250px"> --}}
                        <div class="input-group input-group-merge">
                                <a class="bg-label-gray px-3 rounded-pill text-primary dropdown-toggle nav-link"
                                    href="javascript:void(0)" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    User
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    @foreach ($users as $user)
                                        <li><a class="dropdown-item waves-effect user-list" id="id{{ $user->id }}"
                                                href="javascript:void(0)"
                                                onclick="changeValue('{{ $user->id }}','#staff')">{{ $user->fname }}
                                                {{ $user->lname }}</a></li>
                                    @endforeach
                                </ul>

                        </div>
                    {{-- </li> --}}
                    <div class="input-group input-group-merge mx-2" style="max-width: 250px">
                        <span class="border-0 input-group-text rounded-end rounded-pill text-primary">Start Date :</span>
                        <input type="date" class="border-0 form-control rounded-pill rounded-start text-primary"
                            id="start-date">
                    </div>
                    <div class="input-group input-group-merge mx-2" style="max-width: 250px">
                        <span class="border-0 input-group-text rounded-end rounded-pill text-primary" id="">End
                            Date :</span>
                        <input type="date" class="border-0 form-control rounded-pill rounded-start text-primary"
                            id="end-date">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap m-3" id="Attandance-wrapper">
            <table class="table table-striped table-hover table-datatable" id="attandance-log-datatable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date Time</th>
                        <th>Message</th>
                        <th>Location</th>
                        <th>Device</th>
                        <th>Map Pin</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                </tbody>
            </table>
        </div>
    </div>
    <!--/ Striped Rows -->
@endsection

@section('head')
    {{-- <link rel="stylesheet" href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css">
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script> --}}
@endsection
@section('scripts')
    <script></script>
    <script src="{{ asset('assets/js/page/attendance.js') }}"></script>
    {{-- <script src="{{ asset('assets/admin/js/page/profile.js') }}"></script> --}}
@endsection
