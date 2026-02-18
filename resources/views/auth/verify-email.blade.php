@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Verify Your Email</h2>

    @if(session('success')) <div class="text-green-600">{{ session('success') }}</div>@endif

    <p>We've sent a verification link to your email. Please check your inbox.</p>

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit">Resend verification email</button>
    </form>
</div>
@endsection
