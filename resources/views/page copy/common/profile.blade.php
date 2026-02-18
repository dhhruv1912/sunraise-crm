@extends('admin.temp.common')

@section('title', 'Profile')

@section('content')

    <div class="card">
        <h5 class="card-header">My Profile</h5>

        <div class="card-body">
            {{-- Reuse employee form --}}
            @include('admin.page.edit-employee', ['is_new' => false])
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/admin/js/page/profile.js') }}"></script>
@endsection
