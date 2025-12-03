@extends('temp.common')

@section('title', isset($quotation) ? 'Edit Quotation' : 'New Quotation')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>{{ isset($quotation) ? 'Edit' : 'Create' }} Quotation</h4>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ isset($quotation) ? route('quotations.update', $quotation->id) : route('quotations.store') }}">
            @csrf
            @if(isset($quotation)) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label">Quote Request</label>
                <select name="quote_request_id" class="form-select">
                    <option value="">-- none --</option>
                    @foreach($quoteRequests as $qr)
                        <option value="{{ $qr->id }}" @if(isset($quotation) && $quotation->quote_request_id==$qr->id) selected @endif>
                            #{{ $qr->id }} — {{ $qr->name }} ({{ $qr->number }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="row gx-2">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Base Price</label>
                    <input type="number" step="0.01" name="base_price" class="form-control" value="{{ old('base_price', $quotation->base_price ?? '') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Discount</label>
                    <input type="number" step="0.01" name="discount" class="form-control" value="{{ old('discount', $quotation->discount ?? 0) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Final Price</label>
                    <input type="number" step="0.01" name="final_price" class="form-control" value="{{ old('final_price', $quotation->final_price ?? '') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Meta (JSON)</label>
                <textarea name="meta" class="form-control" rows="4">@if(old('meta')){{ old('meta') }}@elseif(isset($quotation)){{ json_encode($quotation->meta) }}@endif</textarea>
                <small class="text-muted">Optional JSON for SKU, items, notes. Example: {"sku":"Adani-2.16","items":[...]}</small>
            </div>

            <button class="btn btn-primary">{{ isset($quotation) ? 'Update' : 'Create' }}</button>
        </form>
    </div>
</div>
@endsection
