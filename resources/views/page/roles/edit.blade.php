@extends('temp.common')

@section('title', 'Edit Role')

@section('content')

    <div class="card">
        <h4 class="card-header">Edit Role: {{ $role->name }}</h4>

        <form method="POST" action="{{ route('roles.update', $role->id) }}" class="p-3">
            @csrf

            <div class="mb-3">
                <label class="form-label">Role Name</label>
                <input type="text" class="form-control"
                       name="name" value="{{ $role->name }}" required>
                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <h5 class="mt-4 mb-2">Permissions</h5>
            <div class="row">
                @foreach ($permissions as $perm)
                    <div class="col-md-3">
                        <label class="form-check-label">
                            <input type="checkbox"
                                   name="permissions[]"
                                   value="{{ $perm->name }}"
                                   {{ in_array($perm->name, $rolePermissions) ? 'checked' : '' }}
                                   class="form-check-input">
                            {{ $perm->name }}
                        </label>
                    </div>
                @endforeach
            </div>

            <button class="btn btn-primary mt-3">Update</button>

        </form>
    </div>

@endsection
