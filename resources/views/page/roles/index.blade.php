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
                        <th width="150">Name</th>
                        <th>Permissions</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>

                            <td class="p-1">
                                <div class="row gap-1">
                                    @foreach ($role->permissions as $perm)
                                        <span class="badge rounded-pill bg-label-primary py-2 col-2">{{ $perm->name }}</span>
                                    @endforeach
                                </div>
                            </td>

                            <td class="d-flex gap-2 py-5">
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
