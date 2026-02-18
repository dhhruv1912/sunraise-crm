@extends('temp.common')

@section('title', 'Marketing Leads')

@section('content')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Leads</h4>

            <div>
                <a href="{{ route('marketing.create') }}" class="btn btn-primary">Add Lead</a>
                <a href="{{ route('marketing.export') }}" class="btn btn-success">Export</a>

                <form action="{{ route('marketing.import') }}" method="POST" enctype="multipart/form-data"
                    style="display:inline-block;">
                    @csrf
                    <div class="input-group">
                        <input type="file" class="form-control" name="file" required>
                        <button type="submit" class="btn btn-warning">Import</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body border-bottom pb-3">
            <div class="row g-1">
                <div class="col-11 d-flex gap-1">
                    <div class="col d-flex flex-column gap-1">
                        <input type="text" id="filter_name" class="form-control" placeholder="Search name">
                        <input type="text" id="filter_mobile" class="form-control" placeholder="Mobile number">
                    </div>

                    <div class="col d-flex flex-column gap-1">
                        <select id="filter_status" class="form-select">
                            <option value="">All Status</option>
                            @foreach (\App\Models\Lead::$STATUS as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <select id="filter_assigned" class="form-select">
                            <option value="">Assigned User</option>
                            @foreach (\App\Models\User::all() as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col d-flex flex-column gap-1">
                        <div class="input-group">
                            <span class="input-group-text">From : </span>
                            <input type="date" id="filter_from" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">To : </span>
                            <input type="date" id="filter_to" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="col-1 d-flex flex-column gap-1">
                    <button class="btn btn-secondary w-100" id="btnFilter">Apply</button>
                    <button class="btn btn-dark w-100" id="btnReset">Reset</button>
                </div>




            </div>
        </div>
    </div>
    <div class="card mt-2">
        <div class="card-body">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Lead Code</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Status</th>
                        <th>Assigned</th>
                        <th>Follow-up</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="leadTableBody"></tbody>
            </table>

            <div id="leadPagination" class="mt-3"></div>
        </div>
    </div>

    @include('page.marketing.modal')

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/page/marketing.js') }}"></script>
    <script>
        window.LEAD_STATUS = @json(\App\Models\Lead::$STATUS);
        window.allUsers = @json(\App\Models\User::get());
    </script>
@endsection
