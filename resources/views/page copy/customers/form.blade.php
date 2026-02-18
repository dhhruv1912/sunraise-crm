@extends('temp.common')

@section('content')

<div class="card">
    <div class="card-header">
        <h4>{{ isset($customer) ? 'Edit Customer' : 'Add Customer' }}</h4>
    </div>

    <div class="card-body">
        <form method="POST"
              action="{{ isset($customer) ? route('customers.update',$customer->id) : route('customers.store') }}">
            @csrf

            <div class="row g-3">

                <div class="col-md-6">
                    <label>Name</label>
                    <input name="name" class="form-control" required value="{{ old('name',$customer->name ?? '') }}">
                </div>

                <div class="col-md-6">
                    <label>Email</label>
                    <input name="email" class="form-control" value="{{ old('email',$customer->email ?? '') }}">
                </div>

                <div class="col-md-6">
                    <label>Mobile</label>
                    <input name="mobile" class="form-control" value="{{ old('mobile',$customer->mobile ?? '') }}">
                </div>

                <div class="col-md-6">
                    <label>Alternate Mobile</label>
                    <input name="alternate_mobile" class="form-control" value="{{ old('alternate_mobile',$customer->alternate_mobile ?? '') }}">
                </div>

                <div class="col-12">
                    <label>Address</label>
                    <textarea name="address" class="form-control">{{ old('address',$customer->address ?? '') }}</textarea>
                </div>

                <div class="col-12">
                    <label>Note</label>
                    <textarea name="note" class="form-control">{{ old('note',$customer->note ?? '') }}</textarea>
                </div>

                <div class="col-12 text-end">
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Back</a>
                    <button class="btn btn-primary">Save</button>
                </div>

            </div>

        </form>
    </div>
</div>

@endsection
