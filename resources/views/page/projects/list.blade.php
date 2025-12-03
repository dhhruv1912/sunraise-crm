@extends('temp.common')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Projects</h4>
        <div>
            <a href="{{ route('projects.create') }}" class="btn btn-primary">New Project</a>
        </div>
    </div>

    <div class="card-body">
        <div class="row g-2 mb-3">
            <div class="col-md-3">
                <input id="searchBox" placeholder="Search project code / customer / mobile" class="form-control">
            </div>

            <div class="col-md-2">
                <select id="filter_status" class="form-control">
                    <option value="">All Status</option>
                    @foreach($statuses as $k => $v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select id="filter_assignee" class="form-control">
                    <option value="">All Assignees</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select id="perPage" class="form-control">
                    <option>10</option>
                    <option selected>20</option>
                    <option>50</option>
                    <option>100</option>
                </select>
            </div>

            <div class="col-md-3 text-end">
                <button id="applyFilters" class="btn btn-secondary">Apply</button>
                <button id="clearFilters" class="btn btn-light">Clear</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Project Code</th>
                        <th>Customer</th>
                        <th>Mobile</th>
                        <th>KW</th>
                        <th>Assignee</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th style="width:170px">Actions</th>
                    </tr>
                </thead>
                <tbody id="projectsBody">
                    {{-- rows inserted by AJAX --}}
                </tbody>
            </table>
        </div>

        <nav id="paginationWrap"></nav>
    </div>
</div>

@include('page.projects.modal') {{-- modal partial --}}
@endsection

@section('scripts')
<script>
    window.__PROJECT_USERS = @json($users);
    window.__PROJECT_STATUSES = @json($statuses);
</script>
<script src="{{ asset('assets/js/page/projects.js') }}"></script>
@endsection
