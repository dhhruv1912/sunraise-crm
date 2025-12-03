@extends('temp.common')
@section('title','Create Permission')
@section('content')
  <div class="card">
    <div class="card-header"><h4>Create Permission</h4></div>
    <div class="card-body">
      <form method="post" action="{{ route('permissions.store') }}">
        @csrf
        <div class="mb-3">
          <label class="form-label">Permission Name</label>
          <input type="text" name="name" class="form-control" required>
          @error('name') <div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-primary">Save</button>
      </form>
    </div>
  </div>
@endsection
