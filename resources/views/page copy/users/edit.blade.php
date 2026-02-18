@extends('temp.common')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Edit User</h4>
            <div class="text-muted small">
                Update user details, access and security
            </div>
        </div>
        <a href="{{ route('users.view.list') }}" class="btn btn-outline-secondary">
            Back
        </a>
    </div>

    <form id="editUserForm">
        <div class="row g-4">

            {{-- LEFT --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body">

                        <h6 class="mb-3 text-secondary">Basic Information</h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted">First Name</label>
                                <input type="text" name="fname" class="form-control form-control-lg"
                                       value="{{ $user->fname }}">
                                <div class="invalid-feedback" data-error="fname"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-muted">Last Name</label>
                                <input type="text" name="lname" class="form-control form-control-lg"
                                       value="{{ $user->lname }}">
                                <div class="invalid-feedback" data-error="lname"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-muted">Email</label>
                                <input type="email" name="email" class="form-control"
                                       value="{{ $user->email }}">
                                <div class="invalid-feedback" data-error="email"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-muted">Mobile</label>
                                <input type="text" name="mobile" class="form-control"
                                       value="{{ $user->mobile }}">
                                <div class="invalid-feedback" data-error="mobile"></div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-2 text-secondary">Reset Password</h6>
                        <div class="text-muted small mb-3">
                            Leave blank to keep current password
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted">New Password</label>
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

            {{-- RIGHT --}}
            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">

                        <h6 class="mb-3 text-secondary">Access & Status</h6>

                        <div class="mb-3">
                            <label class="form-label small text-muted">Role</label>
                            <select name="role" class="form-select">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}"
                                        @selected($currentRole === $role->name)>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" data-error="role"></div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small text-muted">Status</label>
                            <select name="status" class="form-select">
                                <option value="1" @selected($user->status == 1)>Active</option>
                                <option value="0" @selected($user->status == 0)>Inactive</option>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Update User
                            </button>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection
@push('scripts')
<script>
const UPDATE_URL = "{{ route('users.ajax.update', $user->id) }}";

document.getElementById('editUserForm').addEventListener('submit', function (e) {
    e.preventDefault();

    clearErrors();

    const formData = new FormData(this);

    crmFetch(UPDATE_URL, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            window.location.href = "{{ route('users.view.list') }}";
        } else {
            showErrors(res.errors);
        }
    });
});

function showErrors(errors) {
    Object.keys(errors).forEach(key => {
        const el = document.querySelector(`[name="${key}"]`);
        const err = document.querySelector(`[data-error="${key}"]`);
        if (el) el.classList.add('is-invalid');
        if (err) err.innerText = errors[key][0];
    });
}

function clearErrors() {
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('[data-error]').forEach(el => el.innerText = '');
}
</script>
@endpush
