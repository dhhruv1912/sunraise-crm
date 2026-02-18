@extends('temp.common')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h4>Project — {{ $project->project_code }}</h4>
        <div>
            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('projects.index') }}" class="btn btn-light">Back</a>
        </div>
    </div>

    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Customer</dt><dd class="col-sm-9">{{ $project->customer_name }}</dd>
            <dt class="col-sm-3">Mobile</dt><dd class="col-sm-9">{{ $project->mobile }}</dd>
            <dt class="col-sm-3">Address</dt><dd class="col-sm-9">{{ $project->address }}</dd>
            <dt class="col-sm-3">KW</dt><dd class="col-sm-9">{{ $project->kw }}</dd>
            <dt class="col-sm-3">Assignee</dt><dd class="col-sm-9">{{ $project->assigneeUser->name ?? '—' }}</dd>
            <dt class="col-sm-3">Status</dt><dd class="col-sm-9">{{ $project->status_label }}</dd>
        </dl>

        <hr />
        <h5>Documents</h5>
        <div class="row">
            @if ($project->documents && count($project->documents) > 0)
                @foreach($project->documents as $doc)
                    <div class="col-md-3 mb-2">
                        <div class="card">
                            <div class="card-body p-2">
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank">{{ $doc->type ?? 'file' }}</a>
                                <div class="text-muted small">{{ $doc->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <hr />
        <h5>History</h5>
        <div class="list-group">
            @foreach($project->history as $h)
                <div class="list-group-item">
                    <div class="small text-muted">{{ $h->created_at }} — {{ $h->changed_by ? \App\Models\User::find($h->changed_by)->name : 'System' }}</div>
                    <div>{{ $h->notes }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
