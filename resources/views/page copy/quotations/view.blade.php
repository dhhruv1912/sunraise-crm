@extends('temp.common')

@section('title', 'Quotation #' . $quotation->quotation_no)

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h4>Quotation — {{ $quotation->quotation_no }}</h4>
        <div class="btn-group">
            <a href="{{ route('quote.quotations.edit', $quotation->id) }}" class="btn btn-sm btn-primary">Edit</a>
            <a href="{{ url('quote/quotations/'.$quotation->id.'/download') }}" class="btn btn-sm btn-secondary">Download PDF</a>
            <button id="sendMailBtn" class="btn btn-sm btn-success">Send Mail</button>
        </div>
    </div>

    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Customer</dt><dd class="col-sm-9">{{ optional($quotation->quoteRequest)->name ?? '—' }}</dd>
            <dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ optional($quotation->quoteRequest)->email ?? '—' }}</dd>
            <dt class="col-sm-3">Base Price</dt><dd class="col-sm-9">{{ $quotation->base_price }}</dd>
            <dt class="col-sm-3">Discount</dt><dd class="col-sm-9">{{ $quotation->discount }}</dd>
            <dt class="col-sm-3">Final</dt><dd class="col-sm-9">{{ $quotation->final_price }}</dd>
            <dt class="col-sm-3">Generated PDF</dt><dd class="col-sm-9">
                @if($quotation->pdf_path)
                    <a href="{{ asset('storage/'.$quotation->pdf_path) }}" target="_blank">View PDF</a>
                @else
                    Not generated
                @endif
            </dd>
        </dl>
    </div>
</div>

<script>
document.getElementById('sendMailBtn').addEventListener('click', async function(){
    const res = await crmFetch("{{ url('quote/quotations/'.$quotation->id.'/send-email') }}", { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }});
    const json = await res.json();
    alert(json.message || (json.status ? 'Sent' : 'Failed'));
});
</script>
@endsection
