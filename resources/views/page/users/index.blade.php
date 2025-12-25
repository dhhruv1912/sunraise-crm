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
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="user-body">
          {{-- @foreach($users as $u)
            <tr>
              <td class="d-flex align-items-center gap-2">
                <div class="avatar {{ in_array($u->id,$sessions) ? "avatar-online" : "avatar-offline" }}">
                  <img src="https://api.dicebear.com/7.x/adventurer-neutral/svg?seed={{ $u->fname }}+{{ $u->lname }}"
                      alt="Avatar"
                      class="w-px-40 h-auto rounded-circle">
                </div>
                <span>
                  {{ $u->fname }} {{ $u->lname }}
                </span>
              </td>
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
              <td class="d-flex gap-1 py-3">
                <a href="{{ route('users.edit', $u->id) }}" class="btn btn-sm btn-primary">Edit</a>

                 <button class="btn btn-sm btn-outline-secondary assign-role-btn"
                        data-user="{{ $u->id }}">Assign Role</button> 

                <form action="{{ route('users.delete', $u->id) }}" method="POST" >
                  @csrf @method('DELETE')
                  <button onclick="return confirm('Delete user?')" class="btn btn-sm btn-danger">Delete</button>
                </form>
              </td>
            </tr>
          @endforeach --}}
        </tbody>
      </table>

      <div class="p-3" id="user-pagination">
        <ul id="pagination-ul"></ul>
        {{-- {{ $users->links() }} --}}
      </div>
    </div>
  </div>

@include('page.users._assign_role_modal') {{-- modal partial below --}}

@endsection

@section('scripts')
<script>
  window.roleNames = @json($role);
  console.log(window.roleNames);
  
</script>
<!-- include your existing user.js (uploaded) -->
<script src="{{ asset('assets/js/page/user.js') }}"></script>
<!-- roles assign ajax -->
<script src="{{ asset('assets/js/page/roles-assign.js') }}"></script>
@endsection
