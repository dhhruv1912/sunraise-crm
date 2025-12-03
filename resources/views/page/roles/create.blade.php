@extends('temp.common')

@section('title', 'Create Role')

@section('content')

    <div class="card">
        <h4 class="card-header">Create Role</h4>

        <form method="POST" action="{{ route('roles.store') }}" class="p-3">
            @csrf

            <div class="mb-3">
                <label class="form-label">Role Name</label>
                <input type="text" class="form-control" name="name" required>
                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <h5 class="mt-4 mb-2">Assign Permissions</h5>
            <div class="row">
                @foreach ($permissions as $perm)
                    <div class="col-md-3">
                        <label class="form-check-label">
                            <input type="checkbox" name="permissions[]"
                                   value="{{ $perm->name }}" class="form-check-input">
                            {{ $perm->name }}
                        </label>
                    </div>
                @endforeach
            </div>

            <button class="btn btn-primary mt-3">Save</button>

        </form>
    </div>

@endsection
