@extends('temp.common')
@section('title','Assign Roles')
@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h4>Assign Roles to: {{ $user->fname }} {{ $user->lname }}</h4>
      <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Manage Roles</a>
    </div>

    <div class="card-body">
      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

      <form id="assign-roles-form" method="post" action="{{ route('users.assign.update', $user->id) }}">
        @csrf
        <div class="row">
          @foreach($roles as $role)
            <div class="col-md-3 mb-2">
              <label class="form-check-label">
                <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="form-check-input"
                       {{ in_array($role->name, $userRoles) ? 'checked' : '' }}>
                {{ $role->name }}
              </label>
            </div>
          @endforeach
        </div>
        <button class="btn btn-primary mt-3">Save</button>
      </form>

      {{-- Optional quick AJAX script --}}
      <script>
        (function(){
          const form = document.getElementById('assign-roles-form');
          form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(form);
            const res = await fetch(form.action, {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf_token"]').content },
              body: fd
            });
            if(res.ok) {
              const out = await res.json();
              alert(out.message || 'Saved');
              location.reload();
            } else {
              alert('Failed');
            }
          });
        })();
      </script>
    </div>
  </div>
@endsection
