@extends('temp.common')

@section('title', 'Projects')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Projects</h4>
            <div>
                <a href="{{ route('projects.create') }}" class="btn btn-primary">New Project</a>
            </div>
        </div>

        <div class="card-body">
            <div class="row mb-3 g-2 align-items-center">
                <div class="col-sm-3">
                    <input id="searchBox" class="form-control" placeholder="Search project code / customer / mobile">
                </div>

                <div class="col-sm-2">
                    <select id="filter_status" class="form-select">
                        <option value="">All Status</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-3">
                    <select id="filter_assigned" class="form-select">
                        <option value="">All Assignees</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->fname }} {{ $u->lname }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-2">
                    <select id="perPage" class="form-select">
                        <option value="10">10 / page</option>
                        <option value="20" selected>20 / page</option>
                        <option value="50">50 / page</option>
                    </select>
                </div>

                <div class="col-sm-2 text-end">
                    <button id="btnFilter" class="btn btn-secondary">Filter</button>
                    <button id="btnReset" class="btn btn-light">Reset</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Customer</th>
                            <th>KW / Modules</th>
                            <th>Assigned</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th style="width:170px">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="projects-body">
                        <!-- loaded by JS -->
                    </tbody>
                </table>
            </div>

            <nav id="projects-pagination" aria-label="Projects pagination" class="mt-3"></nav>
        </div>
    </div>

@include('page.projects.modal') {{-- modal partial --}}
@endsection

@section('scripts')
<script>
    window.__PROJECT_STATUSES = @json($statuses);
</script>
<script src="{{ asset('assets/js/page/projects.js') }}"></script>
@endsection
