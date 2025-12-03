@extends('temp.common')
@section('title','Create User')

@section('content')
  <div class="card">
    <h4 class="card-header">Create User</h4>
    <form method="POST" action="{{ route('users.store') }}" class="p-3" id="user-create-form">
      @csrf
      <div class="row">
        <div class="col-md-6 mb-3">
          <label>First Name</label>
          <input name="firstname" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label>Last Name</label>
          <input name="lastname" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label>Mobile</label>
          <input name="mobile" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label>Email</label>
          <input name="email" class="form-control" required autocomplete="new-email" autocorrect="off" autocapitalize="none" spellcheck="false">
        </div>
        <div class="col-md-6 mb-3">
          <label>Password</label>
          <input name="password" type="password" class="form-control" required autocomplete="new-email" autocorrect="off">
        </div>
        <div class="col-md-6 mb-3">
          <label>Salary</label>
          <input name="salary" type="number" step="500" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label>Role</label>
          <select name="role" class="form-select" required>
            @foreach($roles as $role)
              <option value="{{ $role->id }}">{{ $role->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label>Status</label><br>
          <input type="checkbox" name="status" value="1" checked> Active
        </div>
      </div>

      <button class="btn btn-primary">Save</button>
    </form>
  </div>
@endsection
