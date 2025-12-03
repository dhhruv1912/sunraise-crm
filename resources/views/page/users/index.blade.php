@extends('temp.common')
@section('title','Users')

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4>Users</h4>
      <div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">+ New</a>
      </div>
    </div>

    <div class="table-responsive p-3">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Name</th>
            <th>Role</th>
            <th>Status</th>
            <th>Mobile</th>
            <th>Email</th>
            <th width="150">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $u)
            <tr>
              <td>{{ $u->fname }} {{ $u->lname }}</td>
              <td>
                @foreach($u->getRoleNames() as $r)
                  <span class="badge bg-secondary">{{ $r }}</span>
                @endforeach
              </td>
              <td>
                <span class="badge {{ $u->status ? 'bg-success':'bg-danger' }}">
                  {{ $u->status ? 'Active':'Inactive' }}
                </span>
              </td>
              <td>{{ $u->mobile }}</td>
              <td>{{ $u->email }}</td>
              <td class="d-flex gap-2">
                <a href="{{ route('users.edit', $u->id) }}" class="btn btn-sm btn-primary">Edit</a>

                <button class="btn btn-sm btn-outline-secondary assign-role-btn"
                        data-user="{{ $u->id }}">Assign Role</button>

                <form action="{{ route('users.delete', $u->id) }}" method="POST" class="d-inline">
                  @csrf @method('DELETE')
                  <button onclick="return confirm('Delete user?')" class="btn btn-sm btn-danger">Delete</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="p-3">
        {{ $users->links() }}
      </div>
    </div>
  </div>

@include('page.users._assign_role_modal') {{-- modal partial below --}}

@endsection

@section('scripts')
<!-- include your existing user.js (uploaded) -->
<script src="/mnt/data/user.js"></script>
<!-- roles assign ajax -->
<script src="{{ asset('assets/js/page/roles-assign.js') }}"></script>
@endsection
