@extends('temp.common')
@section('title', 'Permissions')
@section('content')
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Permissions</h4>
                <a class="btn btn-primary" href="{{ route('permissions.create') }}">+ New Permission</a>
            </div>
            <div class="table-responsive p-3">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissions as $perm)
                            <tr>
                                <td>{{ $perm->name }}</td>
                                <td class="d-flex gap-2">
                                    <a class="btn btn-sm btn-primary"
                                        href="{{ route('permissions.edit', $perm->id) }}">Edit</a>
                                    <form action="{{ route('permissions.delete', $perm->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this permission?')">
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
