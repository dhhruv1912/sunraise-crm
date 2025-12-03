@extends('temp.common')
@section('title','Edit User')

@section('content')
  <div class="card">
    <h4 class="card-header">Edit User: {{ $user->fname }}</h4>
    <form method="POST" action="{{ route('users.update', $user->id) }}" class="p-3">
      @csrf
      @method('PUT')
      <div class="row">
        <div class="col-md-6 mb-3">
          <label>First Name</label>
          <input name="firstname" class="form-control" value="{{ $user->fname }}" required>
        </div>
        <div class="col-md-6 mb-3">
          <label>Last Name</label>
          <input name="lastname" class="form-control" value="{{ $user->lname }}" required>
        </div>
        <div class="col-md-6 mb-3">
          <label>Mobile</label>
          <input name="mobile" class="form-control" value="{{ $user->mobile }}" required>
        </div>
        <div class="col-md-6 mb-3">
          <label>Email</label>
          <input name="email" class="form-control" value="{{ $user->email }}" required>
        </div>
        <div class="col-md-6 mb-3">
          <label>New Password (leave blank to keep)</label>
          <input name="password" type="password" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
          <label>Salary</label>
          <input name="salary" type="number" step="0.01" class="form-control" value="{{ $user->salary }}" required>
        </div>
        <div class="col-md-6 mb-3">
          <label>Role</label>
          <select name="role" class="form-select" required>
            @foreach($roles as $role)
              <option value="{{ $role->id }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                {{ $role->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label>Status</label><br>
          <input type="checkbox" name="status" value="1" {{ $user->status ? 'checked' : '' }}> Active
        </div>
      </div>

      <button class="btn btn-primary">Update</button>
    </form>
  </div>
@endsection
