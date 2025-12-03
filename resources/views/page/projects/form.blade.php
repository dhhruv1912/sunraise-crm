@extends('temp.common')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>{{ isset($project) ? 'Edit Project' : 'New Project' }}</h4>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ isset($project) ? route('projects.update', $project->id) : route('projects.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
                <div class="col-md-4">
                    <label>Project Code</label>
                    <input name="project_code" class="form-control" value="{{ old('project_code', $project->project_code ?? '') }}">
                </div>

                <div class="col-md-4">
                    <label>Customer Name</label>
                    <input name="customer_name" class="form-control" required value="{{ old('customer_name', $project->customer_name ?? '') }}">
                </div>

                <div class="col-md-4">
                    <label>Mobile</label>
                    <input name="mobile" class="form-control" value="{{ old('mobile', $project->mobile ?? '') }}">
                </div>

                <div class="col-md-4">
                    <label>KW</label>
                    <input name="kw" class="form-control" value="{{ old('kw', $project->kw ?? '') }}">
                </div>

                <div class="col-md-4">
                    <label>Module Brand</label>
                    <input name="module_brand" class="form-control" value="{{ old('module_brand', $project->module_brand ?? '') }}">
                </div>

                <div class="col-md-4">
                    <label>Inverter Brand</label>
                    <input name="inverter_brand" class="form-control" value="{{ old('inverter_brand', $project->inverter_brand ?? '') }}">
                </div>

                <div class="col-md-4">
                    <label>Module Count</label>
                    <input name="module_count" class="form-control" value="{{ old('module_count', $project->module_count ?? '') }}">
                </div>

                <div class="col-md-4">
                    <label>Assignee</label>
                    <select name="assignee" class="form-control">
                        <option value="">—</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ (isset($project) && $project->assignee == $u->id) ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        @foreach($statuses as $k=>$v)
                            <option value="{{ $k }}" {{ (isset($project) && $project->status == $k) ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label>Address</label>
                    <textarea name="address" class="form-control">{{ old('address', $project->address ?? '') }}</textarea>
                </div>

                <div class="col-12">
                    <label>Project Notes</label>
                    <textarea name="project_note" class="form-control">{{ old('project_note', $project->project_note ?? '') }}</textarea>
                </div>

                <div class="col-12 text-end">
                    <a href="{{ route('projects.index') }}" class="btn btn-light">Back</a>
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection
