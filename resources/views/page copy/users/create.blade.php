@extends('temp.common')

@section('title', 'Create User')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Create User</h4>
            <div class="text-muted small">Add a new team member and assign access</div>
        </div>
        <a href="{{ route('users.view.list') }}" class="btn btn-outline-secondary">
            Back
        </a>
    </div>

    <div class="row g-4">

        {{-- LEFT: USER INFO --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h6 class="mb-3 text-secondary">Basic Information</h6>

                    <form id="createUserForm">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label small text-muted">First Name</label>
                                <input type="text" name="fname" class="form-control form-control-lg">
                                <div class="invalid-feedback" data-error="fname"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-muted">Last Name</label>
                                <input type="text" name="lname" class="form-control form-control-lg">
                                <div class="invalid-feedback" data-error="lname"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-muted">Email</label>
                                <input type="email" name="email" class="form-control">
                                <div class="invalid-feedback" data-error="email"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-muted">Mobile</label>
                                <input type="text" name="mobile" class="form-control">
                                <div class="invalid-feedback" data-error="mobile"></div>
                            </div>

                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3 text-secondary">Security</h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Password</label>
                                <input type="password" name="password" class="form-control">
                                <div class="invalid-feedback" data-error="password"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-muted">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>

                </div>
            </div>
        </div>

        {{-- RIGHT: ROLE & ACTION --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h6 class="mb-3 text-secondary">Access & Status</h6>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Role</label>
                        <select name="role" class="form-select">
                            <option value="">Select role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" data-error="role"></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small text-muted">Status</label>
                        <select name="status" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            Create User
                        </button>
                    </div>

                </div>
            </div>
        </div>

        </form>
    </div>

</div>
@endsection
