@extends('temp.common')

@section('title', 'Marketing Leads')

@section('content')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Leads</h4>

            <div>
                <a href="{{ route('marketing.create') }}" class="btn btn-primary">Add Lead</a>
                <a href="{{ route('marketing.export') }}" class="btn btn-success">Export</a>

                <form action="{{ route('marketing.import') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      style="display:inline-block;">
                    @csrf
                    <input type="file" name="file" required>
                    <button type="submit" class="btn btn-warning">Import</button>
                </form>
            </div>
        </div>

        <div class="card-body border-bottom pb-3">
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="text" id="filter_name" class="form-control" placeholder="Search name">
                </div>

                <div class="col-md-3">
                    <input type="text" id="filter_mobile" class="form-control" placeholder="Mobile number">
                </div>

                <div class="col-md-3">
                    <select id="filter_status" class="form-control">
                        <option value="">All Status</option>
                        @foreach (\App\Models\Lead::$STATUS as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select id="filter_assigned" class="form-control">
                        <option value="">Assigned User</option>
                        @foreach (\App\Models\User::all() as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mt-2">
                    <input type="date" id="filter_from" class="form-control" placeholder="From date">
                </div>

                <div class="col-md-3 mt-2">
                    <input type="date" id="filter_to" class="form-control" placeholder="To date">
                </div>

                <div class="col-md-3 mt-2">
                    <button class="btn btn-secondary w-100" id="btnFilter">Apply</button>
                </div>

                <div class="col-md-3 mt-2">
                    <button class="btn btn-dark w-100" id="btnReset">Reset</button>
                </div>

            </div>
        </div>

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
