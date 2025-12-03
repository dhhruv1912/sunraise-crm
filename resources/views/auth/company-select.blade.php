@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Select Company</h2>

        @if ($errors->any())
            <div class="text-red-600">{{ $errors->first() }}</div>
        @endif

        <p>Select which company you want to work on for this session:</p>

        <form method="POST" action="{{ route('company.select.submit') }}">
            @csrf

            @foreach ($companies as $company)
                <div class="mb-3">
                    <button class="btn btn-primary d-grid w-100" name="company" value="{{ $company }}" type="submit">
                        Continue to {{ ucfirst($company) }}
                    </button>
                </div>
                {{-- <div style="margin:10px 0;">
                    <button type="submit" name="company" value="{{ $company }}">
                        Continue to {{ ucfirst($company) }}
                    </button>
                </div> --}}
            @endforeach
        </form>
    </div>
@endsection
