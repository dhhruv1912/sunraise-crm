@extends('temp.common')
@section('title','Edit Permission')
@section('content')
  <div class="card">
    <div class="card-header"><h4>Edit Permission</h4></div>
    <div class="card-body">
      <form method="post" action="{{ route('permissions.update', $permission->id) }}">
        @csrf
        <div class="mb-3">
          <label class="form-label">Permission Name</label>
          <input type="text" name="name" class="form-control" value="{{ $permission->name }}" required>
          @error('name') <div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-primary">Update</button>
      </form>
    </div>
  </div>
@endsection

