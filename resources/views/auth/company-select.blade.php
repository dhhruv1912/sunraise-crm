 @extends('layouts.app')

 @section('content')
     <div class="container my-4">
         <h2>Select Company</h2>

         @if ($errors->any())
             <div class="text-red-600">{{ $errors->first() }}</div>
         @endif

         <p>Select which company you want to work on for this session:</p>

         <form method="POST" action="{{ route('company.select.submit') }}" class="d-flex flex-column gap-2">
             @csrf

             @foreach ($companies as $company)
                 <button type="submit" name="company" value="{{ $company }}" class="border-0 company-card text-start w-100">
                     <div class="card h-100 shadow-sm company-card-inner">
                         <div class="card-body p-2">
                             <div class="company-logo">
                                 <img src="{{ asset('assets/img/logo/' . $company . '-logo.png') }}"
                                     alt="{{ $company }} Logo">
                             </div>
                         </div>
                     </div>
                 </button>
             @endforeach
         </form>
     </div>
 @endsection
