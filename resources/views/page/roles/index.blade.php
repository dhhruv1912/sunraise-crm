@extends('temp.common')

@section('title', 'Roles')

@section('content')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Roles</h4>
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                + Create Role
            </a>
        </div>

        <div class="table-responsive p-3">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Permissions</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>

                            <td>
                                @foreach ($role->permissions as $perm)
                                    <span class="badge bg-info me-1">{{ $perm->name }}</span>
                                @endforeach
                            </td>

                            <td class="d-flex gap-2">
                                <a href="{{ route('roles.edit', $role->id) }}"
                                   class="btn btn-sm btn-primary">Edit</a>

                                <form action="{{ route('roles.delete', $role->id) }}"
                                      method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('Delete this role?')"
                                            class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

@endsection
