@extends('temp.common')

@section('title', 'Staff')

@section('headbar')
    {{-- <a href="{{ route('StaffExcelDownload') }}" target="_blank"
       class="bg-label-gray nav-link px-3 rounded-pill text-primary me-2">
        Download Excel
    </a> --}}
@endsection

@section('content')

    {{-- Unified modal include --}}
    @include('page.common.add-employee')

    <div class="card">
        <h5 class="card-header">Users</h5>

        <div class="table-responsive text-nowrap m-3">
            <table class="table table-striped table-hover">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="user-body"></tbody>
            </table>
        </div>

        <div id="user-pagination">
            <nav>
                <ul class="pagination justify-content-center" id="pagination-ul"></ul>
            </nav>
        </div>
    </div>

    <button id="add-employee" class="btn btn-danger floating-btn shadow-danger waves-effect waves-light">
      Add Employee
    </button>
@endsection

@section('scripts')
  <script src="{{ asset('assets/js/page/user.js') }}"></script>
@endsection
