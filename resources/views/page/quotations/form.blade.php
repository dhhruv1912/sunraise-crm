@extends('temp.common')

@section('title', isset($quotation) ? 'Edit Quotation' : 'New Quotation')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>{{ isset($quotation) ? 'Edit' : 'Create' }} Quotation</h4>
        </div>

        <div class="card-body">
            <form method="POST"
                action="{{ isset($quotation) ? route('quotations.update', $quotation->id) : route('quotations.store') }}">
                @csrf
                @if (isset($quotation))
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label class="form-label">Quote Request</label>
                    <select name="quote_request_id" id="quote_request_id" class="form-select">
                        <option value="">-- none --</option>
                        @foreach ($quoteRequests as $qr)
                            <option value="{{ $qr->id }}" @if (isset($quotation) && $quotation->quote_request_id == $qr->id) selected @endif>
                                #{{ $qr->id }} — {{ $qr->customer->name }} ({{ $qr->customer->mobile }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row gx-2">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Base Price</label>
                        <input type="number" step="0.01" name="base_price" id="base_price" class="form-control"
                            value="{{ old('base_price', $quotation->base_price ?? '') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Discount</label>
                        <input type="number" step="0.01" name="discount" id="discount" class="form-control"
                            value="{{ old('discount', $quotation->discount ?? 0) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Final Price</label>
                        <input type="number" step="0.01" name="final_price" id="final_price" class="form-control"
                            value="{{ old('final_price', $quotation->final_price ?? '') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Meta (JSON)</label>
                    <input type="hidden" name="meta" id="meta">
                    <textarea name="meta2" class="form-control" id="meta2" rows="4">
                        @if (old('meta'))
                            {{ old('meta') }}
                        @elseif(isset($quotation))
                            {{ json_encode($quotation->meta) }}
                        @endif
                    </textarea>
                    <small class="text-muted">Optional JSON for SKU, items, notes. Example:
                        {"sku":"Adani-2.16","items":[...]}</small>
                </div>

                <button class="btn btn-primary">{{ isset($quotation) ? 'Update' : 'Create' }}</button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.getElementById('quote_request_id').addEventListener('change', (e) => loadQuoteRequest(e.target.value));
        document.getElementById('discount').addEventListener('keyup', (e) => discount(e.target.value));
        let allData = {}
        async function loadQuoteRequest(val) {
            if(!val) return
            const res = await fetch(`/quote/requests/api/view/${val}`);
            const data = await res.json();
            document.getElementById('base_price').value = data.quote.payable
            document.getElementById('final_price').value = data.quote.payable
            allData = data
            allData.quote.final_price = data.quote.payable
            updateMeta(allData)

        }

        function discount(val){
            allData.quote.discount = val
            allData.quote.final_price = allData.quote.payable - val
            document.getElementById('final_price').value = allData.quote.payable - val
            updateMeta(allData)
        }

    function updateMeta(data){
        meta_value = "{\n"
        meta_value += `  "module" : "${data.quote.module}",\n`
        meta_value += `  "kw" : "${data.quote.kw}",\n`
        meta_value += `  "module"_count : ${data.quote.module_count},\n`
        meta_value += `  "value" : ${data.quote.value},\n`
        meta_value += `  "taxes" : ${data.quote.taxes},\n`
        meta_value += `  "metering"_cost : ${data.quote.metering_cost},\n`
        meta_value += `  "mcb"_ppa : ${data.quote.mcb_ppa},\n`
        meta_value += `  "payable" : ${data.quote.payable},\n`
        meta_value += `  "subsidy" : ${data.quote.subsidy},\n`
        meta_value += `  "projected" : ${data.quote.projected},\n`
        if(data.quote.discount && data.quote.discount > 0){
            meta_value += `  "discount" : ${data.quote.discount},\n`
        }
        if(data.quote.final_price && data.quote.final_price > 0){
            meta_value += `  "final_price" : ${data.quote.final_price},\n`
        }
        meta_value += "}"
        document.getElementById('meta2').value = meta_value
        document.getElementById('meta').value = JSON.stringify(data.quote)
    }
    </script>
@endsection
