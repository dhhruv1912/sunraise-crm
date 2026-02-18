@extends('temp.common')

@section('title', "Project #{$project->project_code}")

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div>
                <h5>{{ $project->project_code }} — {{ $project->customer_name }}</h5>
                <small class="text-muted">{{ $project->mobile }}</small>
            </div>
            <div>
                <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-primary">Edit</a>
                <a href="{{ route('projects.index') }}" class="btn btn-sm btn-light">Back</a>
            </div>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs mb-3" role="tablist">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-overview">Overview</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-docs">Documents</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-history">History</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-timeline">Timeline</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="tab-overview">
                    <dl class="row">
                        <dt class="col-sm-3">Project Code</dt><dd class="col-sm-9">{{ $project->project_code }}</dd>
                        <dt class="col-sm-3">Customer</dt><dd class="col-sm-9">{{ $project->customer_name }}</dd>
                        <dt class="col-sm-3">Mobile</dt><dd class="col-sm-9">{{ $project->mobile }}</dd>
                        <dt class="col-sm-3">Address</dt><dd class="col-sm-9">{{ $project->address }}</dd>
                        <dt class="col-sm-3">KW</dt><dd class="col-sm-9">{{ $project->kw }}</dd>
                        <dt class="col-sm-3">Modules</dt><dd class="col-sm-9">{{ $project->module_brand }} ({{ $project->module_count }})</dd>
                        <dt class="col-sm-3">Assignee</dt><dd class="col-sm-9">{{ optional($project->assigneeUser)->name ?? '—' }}</dd>
                        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">
                            <span class="badge bg-{{ $project->status_badge }}">{{ $project->status_label }}</span>
                        </dd>
                        <dt class="col-sm-3">Notes</dt><dd class="col-sm-9">{{ $project->project_note ?? '—' }}</dd>
                    </dl>
                </div>

                <div class="tab-pane" id="tab-docs">
                    @include('page.projects._documents', ['project' => $project])
                </div>

                <div class="tab-pane" id="tab-history">
                    @include('page.projects._history', ['project' => $project])
                </div>

                <div class="tab-pane" id="tab-timeline">
                    @include('page.projects._timeline', ['project' => $project])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // you may add small dynamic behaviors here if needed
</script>
@endsection
