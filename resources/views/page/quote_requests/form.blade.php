@extends('temp.common')

@section('content')

@php $edit = isset($data); @endphp

<div class="card">
    <div class="card-header"><h4>{{ $edit ? 'Edit Request' : 'New Request' }}</h4></div>
    <div class="card-body">
        <form method="POST" action="{{ $edit ? route('quote_requests.update', $data->id) : route('quote_requests.store') }}">
            @csrf

            <div class="row g-2">
                <div class="col-md-3">
                    <label>Type</label>
                    <select name="type" class="form-select">
                        <option value="" selected>-- Select Type --</option>
                        <option value="quote" {{ (old('type', $data->type ?? '') == "quote") ? 'selected' : ''}}>Quote</option>
                        <option value="call" {{ (old('type', $data->type ?? '') == "call") ? 'selected' : ''}}>Call</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Name</label>
                    <input name="name" class="form-control" required value="{{ old('name', $data->name ?? '') }}">
                </div>

                <div class="col-md-3">
                    <label>Mobile</label>
                    <input name="number" class="form-control" value="{{ old('number', $data->number ?? '') }}">
                </div>

                <div class="col-md-3">
                    <label>Email</label>
                    <input name="email" class="form-control" value="{{ old('email', $data->email ?? '') }}">
                </div>

                <div class="col-md-3">
                    <label>Module</label>
                    <input name="module" class="form-control" value="{{ old('module', $data->module ?? '') }}">
                </div>

                <div class="col-md-2">
                    <label>KW</label>
                    <input name="kw" class="form-control" value="{{ old('kw', $data->kw ?? '') }}">
                </div>

                <div class="col-md-2">
                    <label>MC</label>
                    <input name="mc" class="form-control" value="{{ old('mc', $data->mc ?? '') }}">
                </div>

                <div class="col-md-2">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        @foreach($statuses as $k => $lbl)
                            <option value="{{ $k }}" {{ (old('status', $data->status ?? '') == $k) ? 'selected' : ''}}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Assigned To</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">-- Select Assignee --</option>
                        @foreach($users as $k => $lbl)
                            <option value="{{ $lbl->id }}" {{ (old('assigned_to', $data->assigned_to ?? '') == $lbl->id) ? 'selected' : ''}}>{{ $lbl->fname }} {{ $lbl->lname }}</option>
                        @endforeach
                    </select>
                    {{-- @dd($users) --}}
                </div>

                <div class="col-12 mt-2">
                    <label>Notes</label>
                    <textarea name="notes" rows="4" class="form-control">{{ old('notes', $data->notes ?? '') }}</textarea>
                </div>

                <div class="col-12 mt-3">
                    <button class="btn btn-success">{{ $edit ? 'Update' : 'Create' }}</button>
                    <a href="{{ route('quote_requests.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </div>

        </form>
    </div>
</div>

@endsection
