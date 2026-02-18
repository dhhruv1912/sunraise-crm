@extends('temp.common')

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h4>Customer Details</h4>
        <a href="{{ route('customers.edit',$customer->id) }}" class="btn btn-primary">Edit</a>
    </div>

    <div class="card-body">
        <p><strong>Name:</strong> {{ $customer->name }}</p>
        <p><strong>Mobile:</strong> {{ $customer->mobile }}</p>
        <p><strong>Email:</strong> {{ $customer->email }}</p>
        <p><strong>Address:</strong> {{ $customer->address }}</p>

        <hr>

        <h5>Notes</h5>
        <form method="POST" action="{{ route('customers.notes.store') }}">
            @csrf
            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
            <textarea name="note" class="form-control"></textarea>
            <button class="btn btn-sm btn-success mt-2">Add Note</button>
        </form>

        <ul class="list-group mt-3">
            @foreach($customer->notes as $n)
                <li class="list-group-item">
                    <strong>{{ $n->user->name ?? 'System' }}</strong>:
                    {{ $n->note }}
                    <br><small>{{ $n->created_at }}</small>
                </li>
            @endforeach
        </ul>

        <hr>

        <h5>Activity Timeline</h5>

        <ul class="list-group mt-2">
            @foreach($customer->activities as $a)
                <li class="list-group-item">
                    <strong>[{{ ucfirst($a->type) }}]</strong>
                    {{ $a->message }}
                    <br>
                    <small>{{ $a->created_at }}</small>
                </li>
            @endforeach
        </ul>
    </div>
</div>

@endsection
